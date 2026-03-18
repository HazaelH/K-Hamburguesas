<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // 1. Estadísticas globales
        $stats = (object)[
            'total_pendientes' => Order::whereIn('status', ['pendiente', 'pagado', 'preparando', 'cocinando', 'listo'])->count(),
            'ingresos_hoy' => Order::whereDate('created_at', \Carbon\Carbon::today())->where('status', '!=', 'cancelado')->sum('total'),
            'total_entregados' => Order::where('status', 'entregado')->count(),
        ];

        // 2. Iniciamos la consulta base
        $query = Order::with('items');

        // --- FILTRO: Búsqueda por Texto ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $idSearch = str_replace('#', '', $search);
                $q->where('id', 'like', "%{$idSearch}%")
                  ->orWhere('cliente_nombre', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        // --- FILTRO: Estado ---
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // --- FILTRO: Tipo de Servicio (MEJORADO) ---
        if ($request->filled('tipo')) {
            $tipoFiltro = strtolower(trim($request->tipo));
            
            $query->where(function($q) use ($tipoFiltro) {
                if ($tipoFiltro === 'mesa') {
                    $q->where('tipo_servicio', 'LIKE', '%mesa%')
                      ->orWhere('tipo_servicio', 'LIKE', '%local%')
                      ->orWhere('tipo_servicio', 'LIKE', '%comedor%')
                      ->orWhereNull('tipo_servicio')
                      ->orWhere('tipo_servicio', '');
                } elseif ($tipoFiltro === 'llevar') {
                    // Atrapa variaciones como "para_llevar", "llevar", "pickup", "recoger"
                    $q->where('tipo_servicio', 'LIKE', '%llevar%')
                      ->orWhere('tipo_servicio', 'LIKE', '%pickup%')
                      ->orWhere('tipo_servicio', 'LIKE', '%recoger%');
                } else {
                    $q->where('tipo_servicio', 'LIKE', '%domicilio%')
                      ->orWhere('tipo_servicio', 'LIKE', '%delivery%');
                }
            });
        }

        // --- FILTRO: Método de Pago ---
        if ($request->filled('pago')) {
            $query->where('metodo_pago', $request->pago);
        }

        // --- FILTRO: Fechas ---
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        // 3. Ejecutamos la consulta con Paginación
        $pedidos = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // ========================================================
        // 4. NORMALIZACIÓN PARA LA VISTA (AQUÍ ESTÁ LA SOLUCIÓN)
        // ========================================================
        foreach ($pedidos as $pedido) {
            $rawType = strtolower($pedido->tipo_servicio ?? '');
            
            if (str_contains($rawType, 'domicilio') || str_contains($rawType, 'delivery')) {
                $pedido->tipo_real = 'domicilio';
            } elseif (str_contains($rawType, 'llevar') || str_contains($rawType, 'pickup') || str_contains($rawType, 'recoger')) {
                $pedido->tipo_real = 'llevar';
            } else {
                // Si es nulo, está vacío o dice "local/mesa", forzamos a que sea "mesa"
                $pedido->tipo_real = 'mesa'; 
            }
            
            $pedido->es_domicilio = ($pedido->tipo_real === 'domicilio');
        }

        return view('admin.orders.index', compact('stats', 'pedidos'));
    }

    public function show($id)
    {
        $order = Order::with(['items.product' => function($query) {
            $query->withTrashed(); 
        }])->findOrFail($id);
        
        $tipoBruto = strtolower($order->tipo_servicio ?? 'mesa');
        $esDomicilio = ($tipoBruto === 'domicilio');

        return view('admin.orders.show', compact('order', 'tipoBruto', 'esDomicilio', 'id'));
    }

    public function complete($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->status = 'entregado';
            $order->save();

            // Usamos la traducción pasando el ID como variable
            return redirect()->back()->with('success', __('admin/orders/orders.success_delivered', ['id' => $id]));
            
        } catch (\Exception $e) {
            return back()->with('error', __('admin/orders/orders.error_complete') . $e->getMessage());
        }
    }

    
}