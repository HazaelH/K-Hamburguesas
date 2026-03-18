<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Stripe\Stripe;
use Stripe\Charge;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmedMail;

class ClientController extends Controller
{
    // =========================================================================
    // 1. VISTAS PRINCIPALES (Inicio y Catálogo)
    // =========================================================================

    public function home()
    {
        $platosPopulares = Product::where('is_active', true)->inRandomOrder()->take(4)->get();
        
        $ofertas = \App\Models\Offer::where('activa', true)
            ->whereDate('fecha_inicio', '<=', now())
            ->whereDate('fecha_fin', '>=', now())
            ->latest()
            ->take(3)
            ->get();

        $mensajeBienvenida = __('client/messages.welcome_guest');
        $ultimoPedido = null;

        if (Auth::check()) {
            $user = Auth::user();
            $mensajeBienvenida = __('client/messages.welcome_back', ['name' => $user->name]);
            
            $ultimoPedido = Order::where('user_id', $user->id)->latest()->first();
            if ($ultimoPedido) {
                $mensajeBienvenida .= __('client/messages.order_again');
            }
        }

        return view('client.home', compact('platosPopulares', 'ofertas', 'mensajeBienvenida', 'ultimoPedido'));
    }

    public function menu()
    {
        $categorias = Product::where('is_active', true)->select('categoria')->distinct()->pluck('categoria');
        
        $tituloRecomendacion = Auth::check() 
            ? __('client/messages.special_selection', ['name' => Auth::user()->name]) 
            : __('client/messages.featured_promos');
            
        $recomendaciones = Product::where('is_active', true)->inRandomOrder()->limit(5)->get();
        $productosIniciales = Product::orderBy('is_active', 'desc')->get();

        return view('client.menu', compact('categorias', 'recomendaciones', 'tituloRecomendacion', 'productosIniciales'));
    }

    public function getProductDetails($id)
    {
        $product = Product::findOrFail($id);
        return response()->json([
            'id_producto' => $product->id_producto,
            'nombre' => $product->nombre,
            'precio' => $product->precio_final,
            'descripcion' => $product->descripcion,
            'imagen_url' => $product->imagen_url,
            'opciones' => $product->opciones_personalizacion 
        ]);
    }

    public function filterProducts($categoria)
    {
        if ($categoria === 'Todas') return Product::where('is_active', true)->get();
        return Product::where('categoria', $categoria)->where('is_active', true)->get();
    }

    // =========================================================================
    // 3. GESTIÓN DEL CARRITO (AJAX)
    // =========================================================================

    public function viewCart()
    {
        $carrito = session('carrito', []);
        $totales = $this->calcularTotales($carrito);

        return view('client.cart', [
            'carrito' => $carrito,
            'total' => $totales['total_num'],
            'subtotal' => $totales['subtotal_num'],
            'iva' => $totales['iva_num']
        ]);
    }

    public function addToCart(Request $request)
    {
        $data = $request->validate([
            'id_producto' => 'required|exists:products,id_producto',
            'modificaciones' => 'nullable|array',
            'cantidad' => 'required|integer|min:1' 
        ]);

        $product = Product::findOrFail($data['id_producto']);
        $modificacionesGuardar = [];
        $descripcionMods = "";
        $costoExtras = 0; 

        $opcionesBD = is_string($product->opciones_personalizacion) 
            ? json_decode($product->opciones_personalizacion, true) 
            : ($product->opciones_personalizacion ?? []);

        if (!empty($data['modificaciones'])) {
            usort($data['modificaciones'], fn($a, $b) => strcmp($a['valor'], $b['valor']));
            
            foreach ($data['modificaciones'] as $mod) {
                $precioDeEstaOpcion = 0;

                if ($mod['grupo'] === 'Predefinido') {
                    foreach ($opcionesBD as $opcionBD) {
                        $nombreBD = is_array($opcionBD) ? $opcionBD['nombre'] : $opcionBD;
                        $precioBD = is_array($opcionBD) ? ($opcionBD['precio'] ?? 0) : 0;

                        if ($nombreBD === $mod['valor']) {
                            $precioDeEstaOpcion = floatval($precioBD);
                            break;
                        }
                    }
                }

                $costoExtras += $precioDeEstaOpcion;
                $textoPrecio = $precioDeEstaOpcion > 0 ? " (+$" . number_format($precioDeEstaOpcion, 2) . ")" : "";

                $modificacionesGuardar[] = ['tipo' => $mod['grupo'], 'valor' => $mod['valor'], 'precio' => $precioDeEstaOpcion];
                $descripcionMods .= "{$mod['valor']}{$textoPrecio}. ";
            }
        }

        $signature = md5($product->id_producto . serialize($modificacionesGuardar));
        $carrito = session('carrito', []);

        $precioUnitarioFinal = $product->precio_final + $costoExtras;

        if (isset($carrito[$signature])) {
            $carrito[$signature]['cantidad'] += $data['cantidad'];
        } else {
            $carrito[$signature] = [
                'row_id' => $signature,
                'id_producto' => $product->id_producto,
                'nombre' => $product->nombre,
                'precio' => $precioUnitarioFinal,
                'imagen_url' => $product->imagen_url,
                'cantidad' => $data['cantidad'], 
                'descripcion_mods' => trim($descripcionMods),
                'modificaciones' => $modificacionesGuardar
            ];
        }

        session(['carrito' => $carrito]);

        // 1. Detectamos el idioma actual y elegimos la columna correcta
        $idioma = app()->getLocale();
        $nombreProducto = $product->nombre; // Español por defecto

        if ($idioma === 'en' && !empty($product->nombre_en)) {
            $nombreProducto = $product->nombre_en;
        } elseif ($idioma === 'pt' && !empty($product->nombre_pt)) {
            $nombreProducto = $product->nombre_pt;
        }

        // 2. Pasamos el nombre ya traducido a tu archivo de mensajes
        return response()->json([
            'status' => 'ok',
            'mensaje' => __('client/messages.item_added', ['product' => $nombreProducto]),
            'total_items' => count($carrito)
        ]);
    }

    public function updateCart(Request $request, $row_id)
    {
        $carrito = session('carrito', []);

        if (!isset($carrito[$row_id])) {
            return response()->json(['success' => false], 404);
        }

        if ($request->action == 'increase') {
            $carrito[$row_id]['cantidad']++;
        } elseif ($request->action == 'decrease') {
            $carrito[$row_id]['cantidad']--;
        }

        if ($carrito[$row_id]['cantidad'] <= 0) {
            unset($carrito[$row_id]);
            session(['carrito' => $carrito]);
            $totales = $this->calcularTotales($carrito);
            
            return response()->json([
                'success' => true,
                'action' => 'removed',
                'cart_empty' => empty($carrito),
                'cart_count' => count($carrito),
                'new_subtotal' => $totales['subtotal'],
                'new_iva' => $totales['iva'],
                'new_total' => $totales['total']
            ]);
        }

        session(['carrito' => $carrito]);
        $totales = $this->calcularTotales($carrito);
        $itemTotal = $carrito[$row_id]['precio'] * $carrito[$row_id]['cantidad'];

        return response()->json([
            'success' => true,
            'action' => 'updated',
            'new_qty' => $carrito[$row_id]['cantidad'],
            'new_item_total' => number_format($itemTotal, 2),
            'new_subtotal' => $totales['subtotal'],
            'new_iva' => $totales['iva'],
            'new_total' => $totales['total'],
            'cart_count' => count($carrito)
        ]);
    }

    public function removeFromCart(Request $request, $row_id)
    {
        $carrito = session('carrito', []);
        if (isset($carrito[$row_id])) {
            unset($carrito[$row_id]);
            session(['carrito' => $carrito]);
        }

        $totales = $this->calcularTotales($carrito);

        return response()->json([
            'success' => true,
            'cart_empty' => empty($carrito),
            'cart_count' => count($carrito),
            'new_subtotal' => $totales['subtotal'],
            'new_iva' => $totales['iva'],
            'new_total' => $totales['total']
        ]);
    }

    private function calcularTotales($carrito) {
        $total = array_reduce($carrito, fn($sum, $i) => $sum + ($i['precio'] * $i['cantidad']), 0);
        $subtotal = $total / 1.16;
        return [
            'total' => number_format($total, 2, '.', ''),
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'iva' => number_format($total - $subtotal, 2, '.', ''),
            'total_num' => $total,
            'subtotal_num' => $subtotal,
            'iva_num' => $total - $subtotal
        ];
    }

    // =========================================================================
    // 4. PAGO Y CONFIRMACIÓN
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
            $mensajeEnvio = __('client/messages.free_shipping_congrats');
        } else {
            $envio = $costoEnvio;
            $faltaParaGratis = $umbralEnvioGratis - $subtotal;
            $mensajeEnvio = __('client/messages.free_shipping_missing', ['amount' => number_format($faltaParaGratis, 2)]);
        }

        $totalFinal = $subtotal + $envio;
        return view('client.checkout', compact('carrito', 'subtotal', 'envio', 'totalFinal', 'mensajeEnvio'));
    }

    public function processPayment(Request $request)
    {
        // 1. Añadimos el codigo_pais y guardar_direccion a la validación
        $request->validate([
            'codigo_pais' => 'required|string', 
            'telefono' => 'required|string',
            'calle' => 'required|string',
            'numero' => 'required|string',
            'colonia' => 'required|string',
            'codigo_postal' => 'required|string',
            'municipio' => 'nullable|string', 
            'estado' => 'nullable|string',    
            'metodo_pago' => 'required|in:efectivo,tarjeta_entrega,stripe',
            'referencias' => 'nullable|string',
            'referencia_tarjeta' => 'nullable|string|max:4',
            'guardar_direccion' => 'nullable|boolean' 
        ]);

        $carrito = session('carrito', []);
        if (empty($carrito)) return redirect()->route('menu');

        $subtotal = array_reduce($carrito, fn($sum, $item) => $sum + ($item['precio'] * $item['cantidad']), 0);
        $umbralEnvioGratis = 200.00;
        $costoEnvio = ($subtotal >= $umbralEnvioGratis) ? 0 : 35.00;
        $totalFinal = $subtotal + $costoEnvio;

        try {
            $orden = DB::transaction(function () use ($request, $carrito, $totalFinal, $costoEnvio) {
                
                $status = 'pendiente';
                
                if ($request->metodo_pago === 'stripe') {
                    Stripe::setApiKey(env('STRIPE_SECRET'));
                    
                    try {
                        $charge = Charge::create([
                            'amount' => intval($totalFinal * 100), 
                            'currency' => 'mxn',
                            'description' => 'Pedido K-Hamburguesas',
                            'source' => $request->stripeToken,
                        ]);
                        
                        $status = 'pagado';
                        
                    } catch (\Exception $e) {
                        throw new \Exception(__('client/messages.card_declined', ['error' => $e->getMessage()]));
                    }
                }

                // 2. Unimos el prefijo del país con el teléfono
                $telefonoCompleto = $request->codigo_pais . $request->telefono;
                
                $ref = $request->referencias ?? __('client/messages.no_reference');
                $direccionCompleta = "{$request->calle} #{$request->numero}, Col. {$request->colonia}, C.P. {$request->codigo_postal}. Ref: " . $ref;

                // 3. Crear la Orden 
                $orden = Order::create([
                    'user_id' => Auth::id(),
                    'codigo_entrega' => 'K-' . strtoupper(\Illuminate\Support\Str::random(5)),
                    'cliente_nombre' => Auth::user() ? Auth::user()->name : 'Invitado',
                    'telefono' => $telefonoCompleto, 
                    'direccion' => $direccionCompleta,
                    'tipo_servicio' => 'domicilio',
                    'total' => $totalFinal,
                    'status' => $status, 
                    'metodo_pago' => $request->metodo_pago,
                    'datos_entrega' => [
                        'costo_envio_cobrado' => $costoEnvio,
                        'origen' => 'web',
                        'terminal_ref' => $request->referencia_tarjeta 
                    ]
                ]);

                // 4. Guardar los Items
                foreach ($carrito as $item) {
                    $orden->items()->create([
                        'product_id' => $item['id_producto'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio'],
                        'subtotal' => $item['cantidad'] * $item['precio'], 
                        'opciones' => json_encode($item['modificaciones']) 
                    ]);
                }

                // ======================================================
                // 5. LÓGICA DE LIBRETA DE DIRECCIONES
                // ======================================================
                if (Auth::check() && $request->has('guardar_direccion') && $request->guardar_direccion == 1) {
                    
                    $esPrimera = Auth::user()->addresses()->count() === 0;

                    $direccionExistente = Auth::user()->addresses()
                        ->where('calle', $request->calle)
                        ->where('numero', $request->numero)
                        ->first();

                    if (!$direccionExistente) {
                        Auth::user()->addresses()->create([
                            'alias' => $request->calle . ' #' . $request->numero,
                            'codigo_pais' => $request->codigo_pais,
                            'telefono' => $request->telefono,
                            'calle' => $request->calle,
                            'numero' => $request->numero,
                            'codigo_postal' => $request->codigo_postal,
                            'colonia' => $request->colonia,
                            'municipio' => $request->municipio ?? '',
                            'estado' => $request->estado ?? '',
                            'referencias' => $request->referencias,
                            'is_default' => $esPrimera
                        ]);
                    }
                }

                return $orden;
            });

            // ======================================================
            // 6. ENVIAR CORREO DE CONFIRMACIÓN (Medio Duradero)
            // ======================================================
            if (Auth::check() && Auth::user()->email) {
                Mail::to(Auth::user()->email)->send(new OrderConfirmedMail($orden));
            } elseif ($request->has('stripeEmail')) {
                Mail::to($request->stripeEmail)->send(new OrderConfirmedMail($orden));
            }

            session()->forget('carrito');
            return redirect()->route('order.success', $orden->id);

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function orderSuccess($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        if ($order->user_id !== Auth::id()) abort(403, __('client/messages.access_denied_order'));
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
        if ($order->user_id !== Auth::id()) abort(403, __('client/messages.access_denied_ticket'));
        return view('client.ticket', compact('order'));
    }

    public function terms()
    {
        return view('client.terms');
    }

    public function privacy()
    {
        return view('client.privacy');
    }
}