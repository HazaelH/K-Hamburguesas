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
    /**
     * Muestra la interfaz del Punto de Venta (POS)
     */
    public function index()
    {
        // Traemos productos activos ordenados
        $productos = Product::where('is_active', 1)
                             ->orderBy('categoria')
                             ->orderBy('nombre')
                             ->get();

        return view('employee.pos.index', compact('productos'));
    }

    /**
     * Guarda la orden en la Base de Datos
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'total' => 'required|numeric',
            'mesa' => 'nullable',
            'cliente' => 'nullable|string',
            // Validamos que venga el método de pago
            'metodo_pago' => 'required|in:efectivo,tarjeta' 
        ]);

        try {
            DB::beginTransaction();

            // 1. OBTENER USUARIO GENÉRICO (Aquí arreglamos tu error "Undefined variable")
            // Esta línea es OBLIGATORIA antes de crear la orden
            $clienteGenerico = User::firstOrCreate(
                ['email' => 'mostrador@tuempresa.com'], 
                [
                    'name' => 'Venta de Mostrador',
                    'password' => bcrypt('sistema_pos_123'),
                ]
            );

            // 2. Definir datos
            $nombreCliente = $request->cliente ?: 'Cliente Casual';
            $tipoServicio = !empty($request->mesa) ? 'comedor' : 'para_llevar';
            
            // 3. Crear la Orden
            $order = Order::create([
                'user_id' => $clienteGenerico->id, // Ahora sí existe la variable
                
                'cliente_nombre' => $nombreCliente,
                'mesa' => $request->mesa,
                'tipo_servicio' => $tipoServicio,
                'status' => 'pendiente', 
                'total' => $request->total,
                
                // USAMOS LO QUE MANDA EL JS
                'metodo_pago' => $request->metodo_pago, 
                
                'datos_entrega' => [
                    'atendido_por' => Auth::user()->name,
                    'origen' => 'pos'
                ]
            ]);

            // 4. Guardar Items
            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'cantidad' => $item['qty'],
                    'precio_unitario' => $item['price'],
                    'subtotal' => $item['qty'] * $item['price'], // Calculamos subtotal
                    'opciones' => null 
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Orden #' . $order->id . ' creada.',
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Imprime el ticket (Vista simplificada)
     */
    public function printTicket($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return view('employee.pos.ticket', compact('order'));
    }
}