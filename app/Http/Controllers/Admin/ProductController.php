<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File; // <--- OJO: Usamos File en lugar de Storage

class ProductController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Product::count(),
            'categorias' => Product::distinct('categoria')->count('categoria'),
            'precio_promedio' => Product::avg('precio'),
        ];

        $chartLabels = Product::select('categoria')->distinct()->pluck('categoria');
        $chartData = Product::selectRaw('count(*) as total, categoria')->groupBy('categoria')->pluck('total');

        $productos = Product::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.products.index', compact('stats', 'productos', 'chartLabels', 'chartData'));
    }

    public function create()
    {
        $categorias = Product::select('categoria')->distinct()->pluck('categoria');
        return view('admin.products.create', compact('categorias'));
    }

    // --- AQUÍ ESTÁ EL CAMBIO CLAVE EN STORE ---
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'required|string',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $rutaImagen = null;

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            // Generamos un nombre único: time + nombre original limpio
            $filename = time() . '-' . $file->getClientOriginalName();
            // LO MOVEMOS A LA CARPETA PUBLIC REAL
            $file->move(public_path('imagenes'), $filename);
            
            // Guardamos solo el nombre del archivo en la BD
            $rutaImagen = $filename;
        }

        Product::create([
            'nombre' => $request->nombre,
            'slug' => Str::slug($request->nombre) . '-' . uniqid(),
            'precio' => $request->precio,
            'categoria' => $request->categoria,
            'descripcion' => $request->descripcion,
            'imagen_url' => $rutaImagen, // Guardamos 'foto.jpg'
            'is_active' => true,
            'is_available' => true,
            'opciones_personalizacion' => []
        ]);

        return redirect()->route('admin.products.index')->with('success', '¡Producto creado exitosamente!');
    }

    public function edit($id)
    {
        $producto = Product::findOrFail($id);
        $categorias = Product::select('categoria')->distinct()->pluck('categoria');
        return view('admin.products.edit', compact('producto', 'categorias'));
    }

    // --- AQUÍ ESTÁ EL CAMBIO CLAVE EN UPDATE ---
    public function update(Request $request, $id)
    {
        $producto = Product::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'required|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $data = [
            'nombre' => $request->nombre,
            'precio' => $request->precio,
            'categoria' => $request->categoria,
            'descripcion' => $request->descripcion,
        ];

        if ($request->hasFile('imagen')) {
            // 1. Borrar imagen vieja si existe en public/imagenes
            if ($producto->imagen_url) {
                $oldPath = public_path('imagenes/' . $producto->imagen_url);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            // 2. Subir nueva
            $file = $request->file('imagen');
            $filename = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('imagenes'), $filename);
            
            $data['imagen_url'] = $filename;
        }

        $producto->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy($id)
    {
        $producto = Product::findOrFail($id);
        
        // Borrar imagen física de public/imagenes
        if ($producto->imagen_url) {
            $path = public_path('imagenes/' . $producto->imagen_url);
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        $producto->delete();

        return redirect()->route('admin.products.index')->with('success', 'Producto eliminado.');
    }
}