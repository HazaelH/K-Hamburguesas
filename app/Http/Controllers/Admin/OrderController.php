<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $stats = (object)[
            'total_pendientes' => Order::whereIn('status', ['pendiente', 'pagado', 'preparando', 'cocinando', 'listo'])->count(),
            'ingresos_hoy' => Order::whereDate('created_at', \Carbon\Carbon::today())->where('status', '!=', 'cancelado')->sum('total'),
            'total_entregados' => Order::where('status', 'entregado')->count(),
        ];

        $pedidos = Order::with('items')->orderBy('created_at', 'desc')->paginate(15);

        foreach ($pedidos as $pedido) {
            $tipo = strtolower($pedido->tipo_servicio ?? 'mesa');
            $pedido->tipo_real = $tipo; 
            $pedido->es_domicilio = ($tipo === 'domicilio');
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