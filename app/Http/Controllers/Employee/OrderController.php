<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order; // Usamos el MODELO NUEVO, no DB::table

class OrderController extends Controller
{
    /**
     * Muestra el Monitor de Caja (Pedidos del día)
     */
    public function index()
    {
        // Traemos órdenes creadas HOY
        $orders = Order::whereDate('created_at', today())
                        ->where('status', '!=', 'cancelado')
                        
                        // --- FILTRO NUEVO: SOLO LOCAL ---
                        // Ignoramos 'domicilio' (Web)
                        ->whereIn('tipo_servicio', ['comedor', 'para_llevar']) 
                        // --------------------------------
                        
                        // Ordenamos: Primero lo pendiente de cobro
                        ->orderByRaw("FIELD(status, 'pagado') ASC") // Pone los no pagados primero
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

        // 1. VALIDACIÓN: Permitimos cobrar incluso si ya está 'entregado' (caso Comedor que paga al final)
        // Solo bloqueamos si está cancelado.
        if ($order->status == 'cancelado') {
             return back()->with('error', 'No se puede cobrar una orden cancelada.');
        }

        // 2. BANDERA DE SEGURIDAD (JSON)
        // Esto asegura que el sistema sepa que hay dinero, sin importar el estado visual
        $datos = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
        
        // Si ya estaba pagado, avisamos para evitar duplicados (opcional)
        if (!empty($datos['pagado'])) {
            // return back()->with('info', 'Esta orden ya tenía registrado el pago.');
        }

        $datos['pagado'] = true;
        $datos['fecha_pago'] = now()->toDateTimeString();
        $order->datos_entrega = $datos;

        // 3. LÓGICA DE ESTADOS (Unificación a 'entregado')
        
        // ESCENARIO A: Orden Para Llevar o Delivery que estaba lista
        // Si ya estaba cocinada ('listo') o en ruta ('en_camino'), al cobrarla cerramos el ciclo.
        if ($order->status == 'listo' || $order->status == 'en_camino') {
            $order->status = 'entregado';
        }
        
        // ESCENARIO B: Cobro anticipado (Fast Food / Web)
        // Si apenas llegó ('pendiente'), la pasamos a 'pagado' para que Cocina sepa que es una venta firme.
        // (Eventualmente Cocina la pasará a 'listo' y luego 'entregado').
        elseif ($order->status == 'pendiente') {
            $order->status = 'pagado';
        }

        // ESCENARIO C: Comedor (Sobremesa)
        // Si el estado ya era 'entregado' (porque ya se sirvió la comida), NO lo cambiamos.
        // Simplemente guardamos la bandera de pago (paso 2) y listo.
        
        $order->save();
        
        return back()->with('success', 'Cobro registrado y orden actualizada.');
    }

    /**
     * (Opcional) Ver detalle rápido en un modal si el cajero necesita confirmar items
     */
    public function show($id)
    {
        if(request()->ajax()) {
            $order = Order::with('items.product')->findOrFail($id);
            return response()->json($order);
        }
        // Si no es ajax, podrías retornar una vista detalle completa
    }

    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        // Seguridad: No cancelar si ya pagaron (primero tendrían que hacer una devolución, pero eso es avanzado)
        if ($order->status == 'pagado') {
            return back()->with('error', 'No puedes cancelar una orden ya cobrada. Haz una devolución primero.');
        }

        // Actualizamos estado
        $order->status = 'cancelado';
        $order->save();

        return back()->with('success', 'Orden #' . $id . ' cancelada correctamente.');
    }

    public function escanearCodigo(Request $request)
    {
        $request->validate(['codigo' => 'required|string']);

        // Buscamos la orden por el código
        $order = Order::where('codigo_entrega', $request->codigo)->first();

        if (!$order) {
            return back()->with('error', 'Código no válido o no existe.');
        }

        // Si ya está entregada, avisamos
        if ($order->status == 'entregado') {
            return back()->with('error', 'Esta orden YA fue entregada anteriormente.');
        }

        // APLICAMOS LA LÓGICA DE COBRO (Reutilizamos la lógica de markAsPaid)
        // 1. Marcar pagado en JSON
        $datos = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
        $datos['pagado'] = true;
        $datos['fecha_pago'] = now()->toDateTimeString();
        $datos['metodo_validacion'] = 'QR Escaneado'; // Auditoría
        $order->datos_entrega = $datos;

        // 2. Cambiar estado a ENTREGADO (Porque el QR implica presencia física)
        $order->status = 'entregado';
        $order->save();

        return back()->with('success', '¡Código Validado! Orden #' . $order->id . ' marcada como ENTREGADA.');
    }
    public function vistaRepartidor()
    {
        return view('employee.delivery.scan');
    }
}