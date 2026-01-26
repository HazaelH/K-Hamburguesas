<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;   
use App\Models\Product; 
use App\Models\User;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // --- 1. TARJETAS SUPERIORES (KPIs) ---

        // A. Ingresos del Mes Actual (Sumamos todo lo que NO esté cancelado)
        $ingresosMensuales = 0;
        try {
            $ingresosMensuales = Order::where('status', '!=', 'cancelado')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total');
        } catch (\Exception $e) { $ingresosMensuales = 0; }

        // B. Pedidos Pendientes (Estados activos en cocina o espera)
        $pedidosPendientes = 0;
        try {
            $pedidosPendientes = Order::whereIn('status', ['pendiente', 'pagado', 'cocinando', 'preparando'])
                ->count();
        } catch (\Exception $e) { }

        // C. Total Productos (Usamos DB directo para ser más rápidos)
        $totalProductos = DB::table('products')->count();

        // D. Total Clientes (Usuarios que no son admin ni empleados)
        $totalClientes = DB::table('users')
            ->whereNotIn('rol', ['admin', 'empleado'])
            ->count();


        // --- 2. GRÁFICA LINEAL (Tendencia de Ingresos - Últimos 6 meses) ---
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
                // Formato bonito: "Ene 2026"
                $chartLabels[] = Carbon::createFromFormat('Y-m', $venta->mes_anio)->translatedFormat('M Y');
                $chartData[] = $venta->total_ventas;
            }
        } catch (\Exception $e) {
            $chartLabels = ['Sin datos'];
            $chartData = [0];
        }


        // --- 3. GRÁFICA DE DONA (Top 5 Productos) ---
        // CORRECCIÓN IMPORTANTE: Usamos 'id_producto' en el JOIN
        $topProductsLabels = [];
        $topProductsValues = [];

        try {
            $topProductos = DB::table('order_items')
                // Asegúrate que 'product_id' en order_items coincida con tu FK
                ->join('products', 'order_items.product_id', '=', 'products.id_producto') 
                ->select('products.nombre', DB::raw('SUM(order_items.cantidad) as total_vendidos'))
                ->groupBy('products.id_producto', 'products.nombre') // Agrupamos por la PK correcta
                ->orderByDesc('total_vendidos')
                ->limit(5)
                ->get();

            if ($topProductos->isEmpty()) {
                throw new \Exception("Sin ventas");
            }

            $topProductsLabels = $topProductos->pluck('nombre');
            $topProductsValues = $topProductos->pluck('total_vendidos');

        } catch (\Exception $e) {
            // Datos dummy para que la gráfica no se rompa visualmente si está vacía
            $topProductsLabels = ['Sin ventas aún'];
            $topProductsValues = [1]; 
        }


        // --- 4. TABLA DE ACTIVIDAD RECIENTE (Últimos 5 pedidos) ---
        $recentOrders = [];
        try {
            $recentOrders = Order::latest()->take(5)->get();
        } catch (\Exception $e) { }


        // --- RETORNO A LA VISTA ---
        return view('admin.dashboard', compact(
            'ingresosMensuales',
            'pedidosPendientes',
            'totalProductos',
            'totalClientes',
            'chartLabels',
            'chartData',
            'topProductsLabels',
            'topProductsValues',
            'recentOrders'
        ));
    }
    public function getOrderDetails($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        
        // Formateamos la respuesta
        return response()->json([
            'id' => $order->id,
            'cliente' => $order->cliente_nombre ?? 'Invitado',
            'status' => $order->status,
            'total' => number_format($order->total, 2),
            'fecha' => $order->created_at->format('d/m/Y H:i'),
            'items' => $order->items->map(function($item) {
                return [
                    'producto' => $item->product->nombre,
                    'cantidad' => $item->cantidad,
                    'precio' => number_format($item->product->precio, 2),
                    'opciones' => $item->opciones // Si tienes opciones JSON
                ];
            })
        ]);
    }
}