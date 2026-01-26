<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class ClientController extends Controller
{
    // =========================================================================
    // 1. VISTAS PRINCIPALES (Home y Menú)
    // =========================================================================

    public function home()
    {
        $platosPopulares = Product::where('is_active', true)
                                ->inRandomOrder()
                                ->take(4)
                                ->get();

        $mensajeBienvenida = "Bienvenido a K-Hamburguesas. Prepárate para un festín.";
        $ultimoPedido = null;

        if (Auth::check()) {
            $user = Auth::user();
            $mensajeBienvenida = "¡Qué bueno verte de nuevo, " . $user->name . "!";
            $ultimoPedido = Order::where('user_id', $user->id)->latest()->first();
            
            if ($ultimoPedido) {
                $mensajeBienvenida .= " ¿Quieres repetir tu último pedido?";
            }
        }

        return view('client.home', compact('platosPopulares', 'mensajeBienvenida', 'ultimoPedido'));
    }

    public function menu()
    {
        $categorias = Product::where('is_active', true)
                            ->select('categoria')
                            ->distinct()
                            ->pluck('categoria');

        $tituloRecomendacion = "Ofertas del Día:";
        
        if (Auth::check()) {
            $user = Auth::user();
            $recomendaciones = Product::where('is_active', true)->inRandomOrder()->limit(5)->get();
            $tituloRecomendacion = "Especialmente para ti, " . $user->name;
        } else {
            $recomendaciones = Product::where('is_active', true)->inRandomOrder()->limit(6)->get();
        }

        $productosIniciales = Product::orderBy('is_active', 'desc')->get();

        return view('client.menu', compact('categorias', 'recomendaciones', 'tituloRecomendacion', 'productosIniciales'));
    }

    // =========================================================================
    // 2. API INTERNA (Para el JS del Menú)
    // =========================================================================

    public function getProductDetails($id)
    {
        $product = Product::findOrFail($id);
        
        return response()->json([
            'id_producto' => $product->id_producto,
            'nombre' => $product->nombre,
            'precio' => $product->precio,
            'descripcion' => $product->descripcion,
            'imagen_url' => $product->imagen_url,
            'opciones' => $product->opciones_personalizacion 
        ]);
    }

    public function filterProducts($categoria)
    {
        if ($categoria === 'Todas') {
            return Product::where('is_active', true)->get();
        }
        return Product::where('categoria', $categoria)->where('is_active', true)->get();
    }

    // =========================================================================
    // 3. CARRITO DE COMPRAS
    // =========================================================================

    public function viewCart()
    {
        $carrito = session('carrito', []);
        
        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        // Desglose de IVA (16%)
        $subtotal = $total / 1.16; 
        $iva = $total - $subtotal;

        return view('client.cart', compact('carrito', 'total', 'subtotal', 'iva'));
    }

    public function addToCart(Request $request)
    {
        $data = $request->validate([
            'id_producto' => 'required|exists:products,id_producto',
            'modificaciones' => 'nullable|array',
            'cantidad' => 'required|integer|min:1' 
        ]);

        $product = Product::findOrFail($data['id_producto']);
        
        $descripcionMods = "";
        $modificacionesGuardar = [];

        if (!empty($data['modificaciones'])) {
            foreach ($data['modificaciones'] as $mod) {
                $modificacionesGuardar[] = ['tipo' => $mod['grupo'], 'valor' => $mod['valor']];
                $descripcionMods .= "{$mod['valor']}. ";
            }
        }

        $cartItem = [
            'row_id' => uniqid(),
            'id_producto' => $product->id_producto,
            'nombre' => $product->nombre,
            'precio' => $product->precio,
            'imagen_url' => $product->imagen_url,
            'cantidad' => $data['cantidad'], 
            'descripcion_mods' => $descripcionMods,
            'modificaciones' => $modificacionesGuardar
        ];

        session()->push('carrito', $cartItem);

        return response()->json([
            'status' => 'ok',
            'mensaje' => "{$data['cantidad']} x {$product->nombre} agregado(s).",
            'total_items' => count(session('carrito', []))
        ]);
    }

    public function removeFromCart($row_id)
    {
        $carrito = session('carrito', []);
        $nuevoCarrito = array_filter($carrito, function($item) use ($row_id) {
            return $item['row_id'] !== $row_id;
        });
        session(['carrito' => array_values($nuevoCarrito)]);

        return back()->with('success', 'Producto eliminado del pedido.');
    }

    // =========================================================================
    // 4. CHECKOUT Y PAGOS (PROCESAMIENTO DE ORDEN)
    // =========================================================================

    public function checkout()
    {
        $carrito = session('carrito', []);
        
        if (empty($carrito)) return redirect()->route('menu');

        $subtotal = array_reduce($carrito, fn($sum, $item) => $sum + ($item['precio'] * $item['cantidad']), 0);

        $costoEnvio = 35.00; 
        $umbralEnvioGratis = 200.00;

        if ($subtotal >= $umbralEnvioGratis) {
            $envio = 0; 
            $mensajeEnvio = "¡Felicidades! Tu envío es GRATIS";
        } else {
            $envio = $costoEnvio;
            $faltaParaGratis = $umbralEnvioGratis - $subtotal;
            $mensajeEnvio = "Agrega $" . number_format($faltaParaGratis, 2) . " más para envío GRATIS";
        }

        $totalFinal = $subtotal + $envio;

        return view('client.checkout', compact('carrito', 'subtotal', 'envio', 'totalFinal', 'mensajeEnvio'));
    }

    public function processPayment(Request $request)
    {
        // 1. Validar formulario
        $request->validate([
            'telefono' => 'required|string',
            'calle' => 'required|string',
            'numero' => 'required|string',
            'colonia' => 'required|string',
            'codigo_postal' => 'required|string',
            'metodo_pago' => 'required|in:efectivo,tarjeta',
            'referencias' => 'nullable|string'
        ]);

        $carrito = session('carrito', []);
        if (empty($carrito)) return redirect()->route('menu');

        // 2. CALCULAR TOTALES
        $subtotal = array_reduce($carrito, fn($sum, $item) => $sum + ($item['precio'] * $item['cantidad']), 0);
        $umbralEnvioGratis = 200.00;
        $costoEnvio = ($subtotal >= $umbralEnvioGratis) ? 0 : 35.00;
        $totalFinal = $subtotal + $costoEnvio;

        try {
            // 3. Transacción de Base de Datos
            $orden = DB::transaction(function () use ($request, $carrito, $totalFinal, $costoEnvio) {
                
                $status = ($request->metodo_pago === 'tarjeta') ? 'pagado' : 'pendiente';

                // Construimos la dirección completa para guardarla limpia
                $direccionCompleta = "{$request->calle} #{$request->numero}, Col. {$request->colonia}, CP {$request->codigo_postal}. Ref: " . ($request->referencias ?? 'Sin referencias');

                // A. Crear Orden Maestra (CORREGIDO: Usando columnas nativas)
                $orden = Order::create([
                    'user_id' => Auth::id(),

                    'codigo_entrega' => 'K-' . strtoupper(\Illuminate\Support\Str::random(5)),
                    
                    // DATOS NATIVOS (Para compatibilidad con POS y Monitor de Caja)
                    'cliente_nombre' => Auth::user()->name,
                    'telefono' => $request->telefono,
                    'direccion' => $direccionCompleta,
                    'tipo_servicio' => 'domicilio',
                    
                    'total' => $totalFinal,
                    'status' => $status, 
                    'metodo_pago' => $request->metodo_pago,
                    
                    // Metadata extra
                    'datos_entrega' => [
                        'costo_envio_cobrado' => $costoEnvio,
                        'origen' => 'web'
                    ]
                ]);

                // B. Crear Items
                foreach ($carrito as $item) {
                    $orden->items()->create([
                        'product_id' => $item['id_producto'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio'],
                        // CORREGIDO: Calculamos subtotal obligatorio
                        'subtotal' => $item['cantidad'] * $item['precio'], 
                        'opciones' => json_encode($item['modificaciones']) 
                    ]);
                }

                return $orden;
            });

            // 4. Limpiar carrito y redirigir
            session()->forget('carrito');
            return redirect()->route('order.success', $orden->id);

        } catch (\Exception $e) {
            // Importante: Este mensaje 'error' ahora sí se verá en la vista
            return back()->with('error', 'Ocurrió un error al crear la orden: ' . $e->getMessage());
        }
    }

    public function orderSuccess($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        if ($order->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver esta orden.');
        }
        return view('client.success', compact('order'));
    }

    public function offers()
    {
        $ofertas = \App\Models\Offer::vigentes()->orderBy('fecha_fin', 'asc')->get();
        return view('client.offers', compact('ofertas'));
    }

    public function ticket($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        if ($order->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver este ticket.');
        }
        return view('client.ticket', compact('order'));
    }
}