<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Product; // <-- Importamos el modelo Product
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('admin.categorias.index', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre',
            'nombre_en' => 'nullable|string|max:255',
            'nombre_pt' => 'nullable|string|max:255',
        ]);

        Categoria::create($request->all());
        
        Cache::forget('diccionario_categorias');
        return redirect()->back()->with('success', __('admin/categorias/messages.create_success'));
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $categoria->id,
            'nombre_en' => 'nullable|string|max:255',
            'nombre_pt' => 'nullable|string|max:255',
        ]);

        $categoria->update($request->all());
        
        Cache::forget('diccionario_categorias');
        return redirect()->back()->with('success', __('admin/categorias/messages.update_success'));
    }

    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);

        // VALIDACIÓN DE SEGURIDAD: ¿Hay productos usando esta categoría?
        $tieneProductos = Product::where('categoria', $categoria->nombre)->exists();
        
        if ($tieneProductos) {
            return redirect()->back()->with('error', __('admin/categorias/messages.delete_error'));
        }

        $categoria->delete();
        
        Cache::forget('diccionario_categorias');
        return redirect()->back()->with('success', __('admin/categorias/messages.delete_success'));
    }
}