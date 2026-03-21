<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Muestra el Monitor de Caja (Pedidos del día)
     */
    public function index()
    {
        $orders = Order::whereDate('created_at', today())
                        ->where('status', '!=', 'cancelado')
                        ->whereIn('tipo_servicio', ['comedor', 'para_llevar']) 
                        ->orderByRaw("FIELD(status, 'pagado') ASC") 
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('employee.orders.index', compact('orders'));
    }

    /**
     * Acción de Cobrar (Registrar que el dinero entró)
     */
    public function markAsPaid($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status == 'cancelado') {
             return back()->with('error', __('employee/delivery/messages.cannot_charge_canceled'));
        }

        $datos = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
        
        $datos['pagado'] = true;
        $datos['fecha_pago'] = now()->toDateTimeString();
        $order->datos_entrega = $datos;

        if ($order->status == 'listo' || $order->status == 'en_camino') {
            $order->status = 'entregado';
        }
        elseif ($order->status == 'pendiente') {
            $order->status = 'pagado';
        }
        
        $order->save();
        
        return back()->with('success', __('employee/delivery/messages.charge_success'));
    }

    /**
     * Ver detalle rápido en un modal
     */
    public function show($id)
    {
        if(request()->ajax()) {
            $order = Order::with('items.product')->findOrFail($id);
            return response()->json($order);
        }
    }

    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status == 'pagado') {
            return back()->with('error', __('employee/delivery/messages.cannot_cancel_paid'));
        }

        $order->status = 'cancelado';
        $order->save();

        return back()->with('success', __('employee/delivery/messages.cancel_success', ['id' => $id]));
    }

    public function escanearCodigo(Request $request)
    {
        $request->validate(['codigo' => 'required|string']);

        $order = Order::where('codigo_entrega', $request->codigo)->first();

        if (!$order) {
            return back()->with('error', __('employee/delivery/messages.invalid_code'));
        }

        if ($order->status == 'entregado') {
            return back()->with('error', __('employee/delivery/messages.already_delivered'));
        }

        $datos = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
        $datos['pagado'] = true;
        $datos['fecha_pago'] = now()->toDateTimeString();
        $datos['metodo_validacion'] = 'QR Escaneado';
        $order->datos_entrega = $datos;

        $order->status = 'entregado';
        $order->save();

        return back()->with('success', __('employee/delivery/messages.code_validated', ['id' => $order->id]));
    }

    public function vistaRepartidor()
    {
        $userId = auth()->id();

        $pedidosDisponibles = Order::with('user')
            ->where('status', 'listo')
            ->where('tipo_servicio', 'domicilio')
            ->orderBy('updated_at', 'asc')
            ->get();

        $todosEnCamino = Order::with('user')
            ->where('status', 'en_camino')
            ->where('tipo_servicio', 'domicilio')
            ->orderBy('updated_at', 'desc')
            ->get();

        $pedidosEnCamino = $todosEnCamino->filter(function($pedido) use ($userId) {
            $datos = is_string($pedido->datos_entrega) ? json_decode($pedido->datos_entrega, true) : ($pedido->datos_entrega ?? []);
            return isset($datos['repartidor_id']) && $datos['repartidor_id'] == $userId;
        });

        $entregasHoy = Order::whereDate('updated_at', today())
            ->where('status', 'entregado')
            ->where('tipo_servicio', 'domicilio')
            ->get()
            ->filter(function($pedido) use ($userId) {
                $datos = is_string($pedido->datos_entrega) ? json_decode($pedido->datos_entrega, true) : ($pedido->datos_entrega ?? []);
                return isset($datos['repartidor_id']) && $datos['repartidor_id'] == $userId;
            })
            ->count();

        return view('employee.delivery.scan', compact('pedidosDisponibles', 'pedidosEnCamino', 'entregasHoy'));
    }

    public function tomarPedido($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'listo') {
            return response()->json(['success' => false, 'message' => __('employee/delivery/messages.order_taken_by_other')]);
        }

        $datos = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
        
        $datos['repartidor_id'] = auth()->id();
        $datos['repartidor_nombre'] = auth()->user()->name;

        $order->datos_entrega = json_encode($datos);
        $order->status = 'en_camino'; 
        $order->save();

        return response()->json(['success' => true]);
    }

    public function checkNuevosPedidos()
    {
        $nuevosIds = Order::where('status', 'listo')
            ->where('tipo_servicio', 'domicilio')
            ->pluck('id');
            
        $pedidosActivos = Order::whereIn('status', ['listo', 'en_camino'])
            ->where('tipo_servicio', 'domicilio')
            ->get();

        $respuestasSOS = [];
        foreach($pedidosActivos as $pedido) {
            $datos = is_string($pedido->datos_entrega) ? json_decode($pedido->datos_entrega, true) : ($pedido->datos_entrega ?? []);
            
            if(isset($datos['sos_resuelto']) && $datos['sos_resuelto'] === true && isset($datos['sos_respuesta_admin'])) {
                if(!isset($datos['sos_leido_repartidor']) || $datos['sos_leido_repartidor'] !== true) {
                    $respuestasSOS[] = [
                        'id' => $pedido->id,
                        'respuesta' => $datos['sos_respuesta_admin']
                    ];
                }
            }
        }
            
        return response()->json([
            'ordenes' => $nuevosIds,
            'respuestas_sos' => $respuestasSOS
        ]);
    }

    public function marcarSOSLeido($id)
    {
        $order = Order::findOrFail($id);
        $datos = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);

        $datos['sos_leido_repartidor'] = true;

        $order->datos_entrega = json_encode($datos);
        $order->save();

        return response()->json(['success' => true]);
    }

    public function reportarProblema(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $datos = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
        
        $datos['alerta_repartidor'] = true;
        $datos['mensaje_repartidor'] = $request->mensaje ?? __('employee/delivery/messages.sos_default_message');
        
        $order->datos_entrega = json_encode($datos);
        $order->save();

        return response()->json(['success' => true]);
    }

    public function notificarCliente(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $datos = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
        
        $datos['notificacion_cliente'] = $request->estado;
        $datos['hora_notificacion'] = now()->toDateTimeString();

        $order->datos_entrega = json_encode($datos);
        $order->save();

        return response()->json(['success' => true]);
    }
}