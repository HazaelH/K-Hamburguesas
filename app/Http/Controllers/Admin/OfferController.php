<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\Product; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter; // <-- 1. IMPORTANTE: Importamos el RateLimiter
use Illuminate\Validation\ValidationException; // <-- Para lanzar el error de bloqueo

class OfferController extends Controller
{
    public function index()
    {
        $offers = Offer::orderBy('created_at', 'desc')->get();
        $categorias = Product::select('categoria')->distinct()->pluck('categoria');
        $productos = Product::where('is_active', true)->get();

        return view('admin.offers.index', compact('offers', 'categorias', 'productos'));
    }

    public function store(Request $request)
    {
        // ===============================================================
        // 2. BLOQUEO ANTI-SPAM (Rate Limiting)
        // Permite máximo 5 creaciones por minuto por usuario.
        // ===============================================================
        $llaveBloqueo = 'crear-oferta:' . auth()->id();
        
        if (RateLimiter::tooManyAttempts($llaveBloqueo, 5)) {
            $segundosRestantes = RateLimiter::availableIn($llaveBloqueo);
            // Redirige de vuelta con un mensaje de error usando la variable de sesión
            return redirect()->back()->with('error', "Has superado el límite de creación de ofertas. Por favor, espera {$segundosRestantes} segundos antes de intentar de nuevo.");
        }
        
        // Si no está bloqueado, registramos este intento
        RateLimiter::hit($llaveBloqueo, 60); // 60 segundos de penalización si se pasa de 5
        // ===============================================================

        $request->validate([
            'titulo' => 'required|string|max:255|unique:offers,titulo',
            'descripcion' => 'nullable|string',
            'tipo_aplicacion' => 'required|in:todo,categoria,producto',
            'porcentaje' => 'required|numeric|min:0|max:100',
            'precio_promo' => 'nullable|numeric',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240'
        ], [
            'titulo.unique' => __('admin/offers/messages.offer_exists'),
        ]);

        $data = $request->except('imagen', 'referencia_categoria', 'referencia_producto');

        if ($request->tipo_aplicacion === 'categoria') {
            $data['referencia'] = $request->referencia_categoria;
        } elseif ($request->tipo_aplicacion === 'producto') {
            $data['referencia'] = $request->referencia_producto;
        } else {
            $data['referencia'] = null; 
        }

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('ofertas', 'public');
            $data['imagen_url'] = $path;
        }

        Offer::create($data);

        return redirect()->back()->with('success', __('admin/offers/messages.create_success'));
    }

    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);
        
        if ($offer->imagen_url) {
            Storage::disk('public')->delete($offer->imagen_url);
        }

        $offer->delete();
        return redirect()->back()->with('success', __('admin/offers/messages.delete_success'));
    }
    
    public function toggle($id)
    {
        $offer = Offer::findOrFail($id);
        $offer->activa = !$offer->activa;
        $offer->save();
        
        return response()->json([
            'success' => true,
            'activa' => $offer->activa
        ]);
    }

    public function edit($id)
    {
        $offer = Offer::findOrFail($id);
        $categorias = Product::select('categoria')->distinct()->pluck('categoria');
        $productos = Product::where('is_active', true)->get();

        $productoSeleccionado = null;
        if ($offer->tipo_aplicacion === 'producto') {
            $productoSeleccionado = Product::find($offer->referencia);
        }

        return view('admin.offers.edit', compact('offer', 'categorias', 'productos', 'productoSeleccionado'));
    }

    public function update(Request $request, $id)
    {
        // ===============================================================
        // 3. BLOQUEO ANTI-SPAM (Rate Limiting) PARA ACTUALIZACIÓN
        // ===============================================================
        $llaveBloqueo = 'editar-oferta:' . auth()->id();
        
        if (RateLimiter::tooManyAttempts($llaveBloqueo, 5)) {
            $segundosRestantes = RateLimiter::availableIn($llaveBloqueo);
            return redirect()->back()->with('error', "Has superado el límite de edición de ofertas. Por favor, espera {$segundosRestantes} segundos antes de intentar de nuevo.");
        }
        
        RateLimiter::hit($llaveBloqueo, 60);
        // ===============================================================

        $offer = Offer::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255|unique:offers,titulo,' . $id,
            'titulo_en' => 'nullable|string|max:255',
            'titulo_pt' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'descripcion_en' => 'nullable|string',
            'descripcion_pt' => 'nullable|string',
            'tipo_aplicacion' => 'required|in:todo,categoria,producto',
            'porcentaje' => 'required|numeric|min:0|max:100',
            'precio_promo' => 'nullable|numeric',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ], [
            'titulo.unique' => __('admin/offers/messages.offer_exists'),
        ]);

        $data = $request->except('imagen', 'referencia_categoria', 'referencia_producto');

        if ($request->tipo_aplicacion === 'categoria') {
            $data['referencia'] = $request->referencia_categoria;
        } elseif ($request->tipo_aplicacion === 'producto') {
            $data['referencia'] = $request->referencia_producto;
        } else {
            $data['referencia'] = null;
        }

        if ($request->hasFile('imagen')) {
            if ($offer->imagen_url) {
                Storage::disk('public')->delete($offer->imagen_url);
            }
            $path = $request->file('imagen')->store('ofertas', 'public');
            $data['imagen_url'] = $path;
        }

        $offer->update($data);

        return redirect()->route('admin.offers.index')->with('success', __('admin/offers/messages.update_success'));
    }
}