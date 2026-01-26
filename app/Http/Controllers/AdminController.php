<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Importante para usar la base de datos

class AdminController extends Controller
{
    public function index()
    {
        // 1. TRADUCCIÓN DE 'resumen_sistema.php'
        // Usamos DB::table para consultar tus tablas existentes sin complicarnos con Modelos todavía.
        
        $totalProductos = DB::table('productos')->count();
        
        $pedidosPendientes = DB::table('pedidos_historial')
            ->where('status', 'Pendiente')
            ->distinct('id_grupo_pedido')
            ->count('id_grupo_pedido');

        // Calcular ingresos de este mes (Lógica de 'metricas_productividad.php')
        $ingresosMensuales = DB::table('pedidos_historial')
            ->join('productos', 'pedidos_historial.id_producto', '=', 'productos.id_producto')
            ->where('pedidos_historial.status', 'Completado')
            ->whereMonth('pedidos_historial.fecha_pedido', now()->month)
            ->sum(DB::raw('pedidos_historial.cantidad * productos.precio'));

        $totalClientes = DB::table('usuarios')
            ->where('rol', 'cliente') // Asumiendo que tienes esta columna basada en tu index.php
            ->count();


        // 2. TRADUCCIÓN DE 'ventas_mensuales.php' (Para el Gráfico)
        // Obtenemos ventas de los últimos 6 meses
        $ventasMes = DB::table('pedidos_historial')
            ->join('productos', 'pedidos_historial.id_producto', '=', 'productos.id_producto')
            ->select(
                DB::raw('DATE_FORMAT(fecha_pedido, "%Y-%m") as mes'),
                DB::raw('SUM(pedidos_historial.cantidad * productos.precio) as total')
            )
            ->where('pedidos_historial.status', 'Completado')
            ->where('fecha_pedido', '>=', now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        // Preparamos los arrays para Chart.js
        $chartLabels = $ventasMes->pluck('mes');
        $chartData = $ventasMes->pluck('total');

        // 3. ENVIAR TODO A LA VISTA
        return view('admin.dashboard', compact(
            'totalProductos', 
            'pedidosPendientes', 
            'ingresosMensuales', 
            'totalClientes',
            'chartLabels',
            'chartData'
        ));
    }
}