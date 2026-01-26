<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class KitchenController extends Controller
{
    // 1. EL PANEL CENTRAL (Hub)
    public function index()
    {
        return view('employee.panel');
    }

    // 2. LA PANTALLA DE COCINA (KDS)
    public function kitchen()
    {
        $orders = Order::with('items.product', 'user')
                        ->whereIn('status', ['pendiente', 'pagado', 'cocinando', 'en_camino', 'preparando'])
                        ->orderBy('created_at', 'asc')
                        ->get();

        // OJO: Ahora retornamos la vista 'employee.kitchen' (la que renombraste)
        return view('employee.kitchen', compact('orders'));
    }

    // 3. ACTUALIZAR ESTADO (AJAX)
    public function updateStatus(Request $request, $id)
    {
        // Buscamos la orden (usando findOrFail para que si no existe de 404)
        $order = Order::findOrFail($id);
        
        // Validamos que el estado sea válido
        $order->status = $request->status;
        $order->save();

        return response()->json(['success' => true, 'message' => 'Estado actualizado']);
    }
}