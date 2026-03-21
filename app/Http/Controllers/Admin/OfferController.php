<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\Product; 
use Illuminate\Support\Facades\Storage;

class OfferController extends Controller
{
    public function index()
    {
        // Traemos las ofertas ordenadas
        $offers = Offer::orderBy('created_at', 'desc')->get();
        
        // Traemos las categorías (ya las traducimos en la vista con el modelo Categoria)
        $categorias = Product::select('categoria')->distinct()->pluck('categoria');
        
        // CORRECCIÓN AQUÍ: Quitamos el select() estricto para que traiga los campos _en y _pt
        // y los accesores de traducción de Product.php puedan funcionar.
        $productos = Product::where('is_active', true)->get();

        return view('admin.offers.index', compact('offers', 'categorias', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo_aplicacion' => 'required|in:todo,categoria,producto',
            'porcentaje' => 'required|numeric|min:0|max:100',
            'precio_promo' => 'nullable|numeric',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240'
        ]);

        $data = $request->except('imagen', 'referencia_categoria', 'referencia_producto');

        // Lógica para guardar la referencia dependiendo de lo que eligió el Admin
        if ($request->tipo_aplicacion === 'categoria') {
            $data['referencia'] = $request->referencia_categoria;
        } elseif ($request->tipo_aplicacion === 'producto') {
            $data['referencia'] = $request->referencia_producto;
        } else {
            $data['referencia'] = null; // Si es a "todo", no hay referencia
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
    
    // Lo convertimos para que responda a peticiones AJAX (como en productos)
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
        
        // Traemos catálogos para el formulario
        $categorias = Product::select('categoria')->distinct()->pluck('categoria');
        
        // CORRECCIÓN AQUÍ TAMBIÉN
        $productos = Product::where('is_active', true)->get();

        // Si la oferta era para un producto específico, traemos su nombre para mostrarlo
        $productoSeleccionado = null;
        if ($offer->tipo_aplicacion === 'producto') {
            $productoSeleccionado = Product::find($offer->referencia);
        }

        return view('admin.offers.edit', compact('offer', 'categorias', 'productos', 'productoSeleccionado'));
    }

    // GUARDAR LOS CAMBIOS EN LA BASE DE DATOS
    public function update(Request $request, $id)
    {
        $offer = Offer::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
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
        ]);

        $data = $request->except('imagen', 'referencia_categoria', 'referencia_producto');

        // Asignamos la referencia correcta
        if ($request->tipo_aplicacion === 'categoria') {
            $data['referencia'] = $request->referencia_categoria;
        } elseif ($request->tipo_aplicacion === 'producto') {
            $data['referencia'] = $request->referencia_producto;
        } else {
            $data['referencia'] = null;
        }

        // Si subió una foto nueva, borramos la vieja y guardamos la nueva
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