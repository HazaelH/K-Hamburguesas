<?php

namespace App\Http\Controllers\Admin; // Ajusta el namespace si no usaste la carpeta Admin

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        // 1. OBTENER ESTADÍSTICAS (Las tarjetas de arriba)
        $stats = DB::table('pedidos_historial as ph')
            ->join('productos as p', 'ph.id_producto', '=', 'p.id_producto')
            ->where('ph.status', 'Pendiente')
            ->select(
                DB::raw('COUNT(DISTINCT ph.id_grupo_pedido) as total_pendientes'),
                DB::raw('SUM(ph.cantidad) as total_items'),
                DB::raw('SUM(ph.cantidad * p.precio) as valor_total'),
                DB::raw('AVG(TIMESTAMPDIFF(MINUTE, ph.fecha_pedido, NOW())) as tiempo_espera_promedio')
            )
            ->first();

        // 2. OBTENER LA LISTA DE PEDIDOS PENDIENTES
        // Traducimos tu Query principal de 'gestionar_pedidos.php'
        $pedidos = DB::table('pedidos_historial as ph')
            ->join('usuarios as u', 'ph.id_usuario', '=', 'u.id')
            ->join('productos as p', 'ph.id_producto', '=', 'p.id_producto')
            ->leftJoin('pedidos as pd', 'ph.id_grupo_pedido', '=', 'pd.id_grupo_pedido')
            ->leftJoin('direcciones_entrega as de', 'pd.id_direccion', '=', 'de.id_direccion')
            ->where('ph.status', 'Pendiente')
            ->select(
                'ph.id_grupo_pedido',
                DB::raw('MAX(ph.fecha_pedido) as fecha'),
                'u.nombre as nombre_cliente',
                'de.municipio',
                'de.colonia',
                'de.telefono',
                DB::raw('SUM(ph.cantidad) as total_items'),
                DB::raw('SUM(ph.cantidad * p.precio) as total_precio')
            )
            ->groupBy(
                'ph.id_grupo_pedido', 
                'u.nombre', 
                'de.municipio', 
                'de.colonia', 
                'de.telefono'
            )
            ->orderBy('fecha', 'asc')
            ->get();

        // 3. ENVIAR A LA VISTA
        return view('admin.orders.index', compact('stats', 'pedidos'));
    }
    public function show($id)
    {
        // Consulta A: Información del Cliente y Dirección
        $infoPedido = DB::table('pedidos_historial as ph')
            ->join('usuarios as u', 'ph.id_usuario', '=', 'u.id')
            ->leftJoin('pedidos as pd', 'ph.id_grupo_pedido', '=', 'pd.id_grupo_pedido')
            ->leftJoin('direcciones_entrega as de', 'pd.id_direccion', '=', 'de.id_direccion')
            ->where('ph.id_grupo_pedido', $id)
            ->select(
                'u.nombre as nombre_cliente',
                'u.email as email_cliente',
                DB::raw('MAX(ph.fecha_pedido) as fecha_pedido'),
                'de.municipio', 'de.colonia', 'de.calle', 'de.numero_exterior', 
                'de.numero_interior', 'de.codigo_postal', 'de.telefono', 
                'de.referencias', 'pd.nombre_titular'
            )
            ->groupBy(
                'u.nombre', 'u.email', 'de.municipio', 'de.colonia', 'de.calle',
                'de.numero_exterior', 'de.numero_interior', 'de.codigo_postal', 
                'de.telefono', 'de.referencias', 'pd.nombre_titular'
            )
            ->first();

        if (!$infoPedido) {
            return redirect()->route('admin.orders.index')->with('error', 'Pedido no encontrado');
        }

        // Consulta B: Items del pedido
        $items = DB::table('pedidos_historial as ph')
            ->join('productos as p', 'ph.id_producto', '=', 'p.id_producto')
            ->where('ph.id_grupo_pedido', $id)
            ->select(
                'p.nombre as nombre_producto',
                'ph.cantidad',
                'p.precio as precio_unitario',
                DB::raw('(ph.cantidad * p.precio) as subtotal')
            )
            ->get();

        return view('admin.orders.show', compact('infoPedido', 'items', 'id'));
    }

    // 2. MÉTODO PARA COMPLETAR PEDIDO (Equivalente a completar_pedido.php)
    public function complete($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // Actualizar historial
                DB::table('pedidos_historial')
                    ->where('id_grupo_pedido', $id)
                    ->update(['status' => 'Completado']);

                // Actualizar tabla pedidos (si existe registro)
                DB::table('pedidos')
                    ->where('id_grupo_pedido', $id)
                    ->update(['status' => 'entregado']);
            });

            return redirect()->route('admin.orders.index')->with('success', 'Pedido #' . $id . ' marcado como completado.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al completar pedido: ' . $e->getMessage());
        }
    }
}