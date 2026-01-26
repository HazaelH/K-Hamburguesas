<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;
use Illuminate\Support\Facades\Storage; // Importante para borrar fotos

class OfferController extends Controller
{
    public function index()
    {
        $offers = Offer::orderBy('created_at', 'desc')->get();
        return view('admin.offers.index', compact('offers'));
    }

    public function store(Request $request)
    {
        // 1. VALIDACIÓN COMPLETA
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'porcentaje' => 'required|numeric|min:0|max:100',
            'precio_promo' => 'nullable|numeric',
            'fecha_inicio' => 'required|date', // Faltaba este
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $data = $request->except('imagen'); // Tomamos todo menos la imagen para procesarla aparte

        // 2. SUBIDA DE IMAGEN
        if ($request->hasFile('imagen')) {
            // Guardamos en 'storage/app/public/ofertas'
            // DB guardará: 'ofertas/nombre_archivo.jpg'
            $path = $request->file('imagen')->store('ofertas', 'public');
            $data['imagen_url'] = $path;
        }

        Offer::create($data);

        return redirect()->back()->with('success', 'Oferta creada con éxito');
    }

    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);
        
        // 3. BORRAR IMAGEN DEL SERVIDOR (Limpieza)
        if ($offer->imagen_url) {
            Storage::disk('public')->delete($offer->imagen_url);
        }

        $offer->delete();
        return redirect()->back()->with('success', 'Oferta eliminada');
    }
    
    public function toggle($id)
    {
        $offer = Offer::findOrFail($id);
        $offer->activa = !$offer->activa;
        $offer->save();
        
        // Regresamos atrás para recargar la página
        return redirect()->back()->with('success', 'Estado actualizado');
    }
}