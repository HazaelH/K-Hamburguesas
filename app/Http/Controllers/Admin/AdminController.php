<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;   
use App\Models\Product; 
use App\Models\User;
use App\Models\AuditLog;
use Carbon\Carbon;

class AdminController extends Controller
{
    // =========================================================================
    // 1. VISTA PRINCIPAL DEL DASHBOARD
    // =========================================================================
    public function index(Request $request)
    {
        // 1. Determinar el rango de fechas basado en el filtro de la URL
        $filter = $request->query('filter', 'month');
        
        $startDate = match($filter) {
            'today' => Carbon::today(),
            'year'  => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(), 
        };
        $endDate = Carbon::now()->endOfDay();

        // 2. Ingresos
        $ingresosMensuales = 0;
        try {
            $sumaMXN = Order::where('status', '!=', 'cancelado')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total');
            $ingresosMensuales = convertCurrencyValue($sumaMXN);
        } catch (\Exception $e) { $ingresosMensuales = 0; }

        // 3. Pedidos Pendientes
        $pedidosPendientes = 0;
        try {
            $pedidosPendientes = Order::whereIn('status', ['pendiente', 'pagado', 'cocinando', 'preparando'])->count();
        } catch (\Exception $e) { }

        // 4. Total Productos
        $totalProductos = DB::table('products')->where('is_active', true)->count();

        // 5. Clientes Registrados
        $totalClientes = DB::table('users')
            ->where('rol', 'cliente')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();


        // --- GRÁFICA 1: LINEAL (Evolución de ventas) ---
        $chartLabels = [];
        $chartData = [];
        try {
            $ventasHistoricas = Order::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes_anio'),
                    DB::raw('SUM(total) as total_ventas')
                )
                ->where('status', '!=', 'cancelado')
                ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
                ->groupBy('mes_anio')
                ->orderBy('mes_anio', 'asc')
                ->get();

            foreach ($ventasHistoricas as $venta) {
                $chartLabels[] = Carbon::createFromFormat('Y-m', $venta->mes_anio)->translatedFormat('M Y');
                $chartData[] = convertCurrencyValue($venta->total_ventas);
            }
        } catch (\Exception $e) {
            $chartLabels = [__('admin/messages.no_data')];
            $chartData = [0];
        }


        // --- GRÁFICA 2: DONA (Top Productos) ---
        $topProductsLabels = [];
        $topProductsValues = [];
        try {
            $topProductos = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id') 
                ->join('products', 'order_items.product_id', '=', 'products.id_producto') 
                ->select('products.nombre', DB::raw('SUM(order_items.cantidad) as total_vendidos'))
                ->where('orders.status', '!=', 'cancelado')
                ->whereBetween('orders.created_at', [$startDate, $endDate]) 
                ->groupBy('products.id_producto', 'products.nombre') 
                ->orderByDesc('total_vendidos')
                ->limit(5)
                ->get();

            if (!$topProductos->isEmpty()) {
                $topProductsLabels = $topProductos->pluck('nombre')->toArray();
                $topProductsValues = $topProductos->pluck('total_vendidos')->toArray();
            } else {
                $topProductsLabels = [__('admin/messages.no_sales_yet') ?? 'Sin ventas'];
                $topProductsValues = [1]; 
            }
        } catch (\Exception $e) {
            $topProductsLabels = ['Sin ventas'];
            $topProductsValues = [1]; 
        }

        // --- GRÁFICA 3: BARRAS (Horas Pico) ---
        $peakHoursLabels = [];
        $peakHoursValues = [];
        try {
            $horasPico = Order::select(
                    DB::raw('HOUR(created_at) as hora'),
                    DB::raw('COUNT(*) as total_pedidos')
                )
                ->where('status', '!=', 'cancelado')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('hora')
                ->orderBy('hora', 'asc')
                ->get();

            $horasCompletas = array_fill(0, 24, 0);
            foreach ($horasPico as $pico) {
                $horasCompletas[$pico->hora] = $pico->total_pedidos;
            }

            for ($i = 10; $i <= 22; $i++) {
                $amPm = $i >= 12 ? 'PM' : 'AM';
                $horaDisplay = $i > 12 ? $i - 12 : $i;
                $peakHoursLabels[] = "{$horaDisplay}:00 {$amPm}";
                $peakHoursValues[] = $horasCompletas[$i];
            }
        } catch (\Exception $e) {
            $peakHoursLabels = ['10:00 AM', '12:00 PM', '02:00 PM', '04:00 PM', '06:00 PM'];
            $peakHoursValues = [0, 0, 0, 0, 0];
        }

        $recentOrders = [];
        try {
            $recentOrders = Order::latest()->take(5)->get();
        } catch (\Exception $e) { }

        // AQUÍ ESTABA EL ERROR: Faltaba pasar peakHoursLabels y peakHoursValues a la vista
        return view('admin.dashboard', compact(
            'ingresosMensuales',
            'pedidosPendientes',
            'totalProductos',
            'totalClientes',
            'chartLabels',
            'chartData',
            'topProductsLabels',
            'topProductsValues',
            'peakHoursLabels',
            'peakHoursValues',
            'recentOrders'
        ));
    }

    // =========================================================================
    // 2. DETALLE RÁPIDO DE ORDEN
    // =========================================================================
    public function getOrderDetails($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        
        return response()->json([
            'id' => $order->id,
            'cliente' => $order->cliente_nombre ?? __('admin/messages.guest'),
            'status' => $order->status,
            'total' => number_format(convertCurrencyValue($order->total), 2),
            'fecha' => app()->getLocale() == 'en' ? $order->created_at->format('m/d/Y h:i A') : $order->created_at->format('d/m/Y H:i'),
            'items' => $order->items->map(function($item) {
                return [
                    'producto' => $item->product ? $item->product->nombre : __('admin/messages.deleted_product'),
                    'cantidad' => $item->cantidad,
                    'precio' => number_format(convertCurrencyValue($item->precio_unitario ?? 0), 2),
                    'opciones' => $item->opciones
                ];
            })
        ]);
    }

    // =========================================================================
    // 3. SISTEMA DE ALERTAS EN TIEMPO REAL (AJAX)
    // =========================================================================
    public function checkAlerts()
    {
        $orders = Order::whereIn('status', ['pendiente', 'pagado', 'cocinando', 'listo', 'en_camino'])->get();
        $alertas = [];

        foreach($orders as $o) {
            // DECODIFICACIÓN BLINDADA (Anti Doble-Encoding)
            $datos = $o->datos_entrega;
            if (is_string($datos)) {
                $datos = json_decode($datos, true);
                // Si tras decodificar sigue siendo un string, lo decodificamos de nuevo
                if (is_string($datos)) { 
                    $datos = json_decode($datos, true); 
                }
            }
            $datos = $datos ?? [];
            
            // Verificamos si existe la solicitud
            if(isset($datos['solicita_cancelacion']) && ($datos['solicita_cancelacion'] === true || $datos['solicita_cancelacion'] === 'true')) {
                
                // Extraemos el motivo para que el Admin sepa por qué se quiere cancelar
                $motivo = $datos['motivo_cancelacion'] ?? 'Sin motivo especificado';
                
                $alertas[] = [
                    'tipo' => 'cancelacion',
                    'id' => $o->id,
                    'mensaje' => __('admin/messages.kitchen_cancel_req', ['id' => $o->id]) . ' - Motivo: "' . $motivo . '"'
                ];
            }

            if(isset($datos['alerta_repartidor']) && ($datos['alerta_repartidor'] === true || $datos['alerta_repartidor'] === 'true')) {
                $alertas[] = [
                    'tipo' => 'sos_repartidor',
                    'id' => $o->id,
                    'mensaje' => __('admin/messages.sos_driver', ['id' => $o->id, 'msg' => $datos['mensaje_repartidor'] ?? ''])
                ];
            }
        }

        $productos = \App\Models\Product::all();
        foreach($productos as $p) {
            $cacheKey = 'alerta_stock_' . $p->id_producto;
            if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                $info = \Illuminate\Support\Facades\Cache::get($cacheKey);
                $alertas[] = [
                    'tipo' => 'stock',
                    'id' => $info['id'],
                    'mensaje' => __('admin/messages.kitchen_stock_req', ['action' => $info['accion'], 'name' => $info['nombre']])
                ];
            }
        }

        return response()->json(['alertas' => $alertas]);
    }

    public function resolverCancelacion(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        // DECODIFICACIÓN BLINDADA
        $datos = $order->datos_entrega;
        if (is_string($datos)) {
            $datos = json_decode($datos, true);
            if (is_string($datos)) { $datos = json_decode($datos, true); }
        }
        $datos = $datos ?? [];
        
        $accionLog = __('admin/messages.action_rejected');
        $mensajeLog = __('admin/messages.log_cancel_req', ['id' => $id]);

        if ($request->accion === 'aprobar') {
            if ($request->pin !== '1234') {
                return response()->json(['success' => false, 'message' => __('admin/messages.invalid_master_pin')], 403);
            }
            $order->status = 'cancelado';
            $accionLog = __('admin/messages.action_approved');
        }

        $nombreResponsable = $datos['empleado_solicitante'] ?? __('admin/messages.unknown_user');

        \App\Models\AuditLog::create([
            'admin_name' => auth()->user()->name,
            'empleado_name' => $nombreResponsable,
            'accion' => $accionLog,
            'detalle' => $mensajeLog
        ]);

        // Apagamos la alerta
        $datos['solicita_cancelacion'] = false;
        
        $order->datos_entrega = json_encode($datos);
        $order->save();

        return response()->json(['success' => true]);
    }

    public function resolverAlertaInsumo(Request $request, $id)
    {
        $producto = Product::findOrFail($id);
        $cacheKey = 'alerta_stock_' . $id;

        $infoSolicitud = \Illuminate\Support\Facades\Cache::get($cacheKey);
        $nombreEmpleado = $infoSolicitud ? $infoSolicitud['empleado'] : __('admin/messages.unknown_employee');
        $accionSolicitada = $infoSolicitud ? $infoSolicitud['accion'] : __('admin/messages.default_modification');

        if ($request->decision === 'aprobar') {
            $producto->is_active = !$producto->is_active;
            $producto->save();
            $accionLog = __('admin/messages.action_approved');
        } else {
            $accionLog = __('admin/messages.action_rejected');
        }

        AuditLog::create([
            'admin_name' => auth()->user()->name,
            'empleado_name' => $nombreEmpleado,
            'accion' => $accionLog,
            'detalle' => __('admin/messages.log_stock_req', ['action' => $accionSolicitada, 'name' => $producto->nombre])
        ]);

        \Illuminate\Support\Facades\Cache::forget($cacheKey);
        
        return response()->json(['success' => true]);
    }

    public function resolverSOS(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $datos = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
        
        $respuestaAdmin = $request->respuesta ?? __('admin/messages.admin_resolved_default');
        
        \App\Models\AuditLog::create([
            'admin_name' => auth()->user()->name,
            'empleado_name' => __('admin/messages.driver_on_route'),
            'accion' => __('admin/messages.action_resolved_sos'),
            'detalle' => __('admin/messages.log_sos_instruction', ['id' => $id, 'msg' => $respuestaAdmin])
        ]);

        $datos['alerta_repartidor'] = false;
        $datos['sos_resuelto'] = true;
        $datos['sos_respuesta_admin'] = $respuestaAdmin;
        
        $order->datos_entrega = json_encode($datos);
        $order->save();

        return response()->json(['success' => true]);
    }
}