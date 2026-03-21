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
        return view('employee.kitchen.kitchen', compact('orders'));
    }

    // 3. ACTUALIZAR ESTADO (AJAX)
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'success' => true, 
            'message' => __('employee/kitchen/messages.status_updated')
        ]);
    }

    // 4. SOLICITAR CANCELACIÓN AL GERENTE (NUEVO)
    public function solicitarCancelacion($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        
        if ($order->status == 'pagado') {
            return response()->json([
                'success' => false, 
                'message' => __('employee/kitchen/messages.cannot_cancel_paid')
            ], 400);
        }

        $datos = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
        $datos['solicita_cancelacion'] = true;
        $datos['empleado_solicitante'] = auth()->user()->name;
        $order->datos_entrega = json_encode($datos);
        $order->save();

        return response()->json(['success' => true]);
    }
}