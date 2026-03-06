<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem; 
use App\Models\User;

class PosController extends Controller
{
    public function index()
    {
        $productos = Product::where('is_active', 1)
                             ->orderBy('categoria')
                             ->orderBy('nombre')
                             ->get();

        return view('employee.pos.index', compact('productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'total' => 'required|numeric',
            'mesa' => 'nullable',
            'cliente' => 'nullable|string',
            'metodo_pago' => 'required|in:efectivo,tarjeta' 
        ]);

        try {
            DB::beginTransaction();

            $clienteGenerico = User::firstOrCreate(
                ['email' => 'mostrador@tuempresa.com'], 
                [
                    'name' => __('employee/pos/messages.counter_sale'),
                    'password' => bcrypt('sistema_pos_123'),
                ]
            );

            $nombreCliente = $request->cliente ?: __('employee/pos/messages.casual_client');
            $tipoServicio = !empty($request->mesa) ? 'comedor' : 'para_llevar';
            
            $order = Order::create([
                'user_id' => $clienteGenerico->id, 
                'cliente_nombre' => $nombreCliente,
                'mesa' => $request->mesa,
                'tipo_servicio' => $tipoServicio,
                'status' => 'pendiente', 
                'total' => $request->total,
                'metodo_pago' => $request->metodo_pago, 
                'datos_entrega' => [
                    'atendido_por' => Auth::user()->name,
                    'origen' => 'pos'
                ]
            ]);

            foreach ($request->items as $item) {
                $opcionesJson = !empty($item['opciones']) ? json_encode($item['opciones']) : null;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'cantidad' => $item['qty'],
                    'precio_unitario' => $item['price'],
                    'subtotal' => $item['qty'] * $item['price'], 
                    'opciones' => $opcionesJson 
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => __('employee/pos/messages.order_created', ['id' => $order->id]),
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => __('employee/pos/messages.error', ['message' => $e->getMessage()])], 500);
        }
    }

    public function printTicket($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return view('employee.pos.ticket', compact('order'));
    }
}