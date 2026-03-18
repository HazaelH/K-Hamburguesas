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
            'mesa' => 'nullable',
            'cliente' => 'nullable|string',
            'metodo_pago' => 'required|in:efectivo,tarjeta' 
        ]);

        try {
            DB::beginTransaction();

            $granTotalCalculado = 0;
            $itemsParaGuardar = [];

            foreach ($request->items as $item) {
                $producto = Product::where('id_producto', $item['id'])->firstOrFail();
                
                $precioBase = floatval($producto->precio);
                $costoExtras = 0;
                $opcionesSeguras = [];

                // CORRECCIÓN 1: Leer 'modificaciones' (como lo manda el JS)
                $modificaciones = $item['modificaciones'] ?? [];

                if (!empty($modificaciones) && is_array($modificaciones)) {
                    $opcionesBD = is_string($producto->opciones_personalizacion) 
                        ? json_decode($producto->opciones_personalizacion, true) 
                        : ($producto->opciones_personalizacion ?? []);

                    foreach ($modificaciones as $opFront) {
                        $precioRealExtra = 0;
                        $nombreOpcionFront = $opFront['valor'] ?? '';

                        if (is_array($opcionesBD)) {
                            foreach ($opcionesBD as $opBd) {
                                $nombreBD = is_array($opBd) ? ($opBd['nombre'] ?? '') : $opBd;
                                $precioBD = is_array($opBd) ? ($opBd['precio'] ?? 0) : 0;

                                if (strtolower($nombreBD) === strtolower($nombreOpcionFront)) {
                                    $precioRealExtra = floatval($precioBD);
                                    break;
                                }
                            }
                        }

                        $costoExtras += $precioRealExtra;
                        // CORRECCIÓN 2: Guardar como 'nombre' para que el Ticket lo pueda leer
                        $opcionesSeguras[] = [
                            'nombre' => $nombreOpcionFront,
                            'precio' => $precioRealExtra
                        ];
                    }
                }

                $precioFinalItem = $precioBase + $costoExtras;
                $cantidad = max(1, (int)$item['qty']); 
                $subtotalItem = $precioFinalItem * $cantidad;
                
                $granTotalCalculado += $subtotalItem;

                $itemsParaGuardar[] = [
                    'product_id' => $producto->id_producto,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioFinalItem,
                    'subtotal' => $subtotalItem,
                    'opciones' => empty($opcionesSeguras) ? null : json_encode($opcionesSeguras)
                ];
            }

            $nombreCliente = $request->cliente ?: (__('employee/pos/messages.casual_client', [], 'es') ?? 'Cliente Casual');
            $tipoServicio = !empty($request->mesa) ? 'mesa' : 'llevar';
            
            $order = Order::create([
                // CORRECCIÓN 3: El 'user_id' debe ser el del Empleado (Auth::id())
                'user_id' => Auth::id(), 
                'cliente_nombre' => $nombreCliente,
                'mesa' => $request->mesa,
                'tipo_servicio' => $tipoServicio,
                'status' => 'pendiente', 
                'total' => $granTotalCalculado,
                'metodo_pago' => $request->metodo_pago, 
                'datos_entrega' => [
                    'atendido_por' => Auth::user()->name,
                    'origen' => 'pos'
                ]
            ]);

            foreach ($itemsParaGuardar as $itemSeguro) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemSeguro['product_id'],
                    'cantidad' => $itemSeguro['cantidad'],
                    'precio_unitario' => $itemSeguro['precio_unitario'],
                    'subtotal' => $itemSeguro['subtotal'], 
                    'opciones' => $itemSeguro['opciones'] 
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
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function printTicket($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return view('employee.pos.ticket', compact('order'));
    }
}