<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Database\QueryException;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('ver_bajas')) {
            $query->onlyTrashed();
        }

        // 1. Filtro por Búsqueda (Busca en español y en inglés)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search . '%')
                  ->orWhere('nombre_en', 'like', '%' . $request->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->search . '%')
                  ->orWhere('descripcion_en', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('estado')) {
            $is_active = $request->estado === 'disponible' ? 1 : 0;
            $query->where('is_active', $is_active);
        }

        // 4. Filtro por Rango de Precios (Con reversa de moneda)
        if ($request->filled('precio_min')) {
            $precioMinMXN = convertToBaseCurrency($request->precio_min);
            $query->where('precio', '>=', $precioMinMXN);
        }
        if ($request->filled('precio_max')) {
            $precioMaxMXN = convertToBaseCurrency($request->precio_max);
            $query->where('precio', '<=', $precioMaxMXN);
        }

        if ($request->filled('ordenar')) {
            switch ($request->ordenar) {
                case 'precio_asc':
                    $query->orderBy('precio', 'asc');
                    break;
                case 'precio_desc':
                    $query->orderBy('precio', 'desc');
                    break;
                case 'recientes':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc'); 
        }

        $productos = $query->paginate(10)->withQueryString();
        $categorias = Product::select('categoria')->distinct()->pluck('categoria');
        
        $stats = [
            'total' => Product::count(),
            'categorias' => $categorias->count(),
            'precio_promedio' => Product::avg('precio') ?? 0,
        ];

        return view('admin.products.index', compact('productos', 'stats', 'categorias'));
    }

    public function create()
    {
        $categorias = Product::select('categoria')->distinct()->pluck('categoria');
        return view('admin.products.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'nombre_en' => 'nullable|string|max:255',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'required|string',
            'descripcion' => 'nullable|string',
            'descripcion_en' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $rutaImagen = null;

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $filename = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('imagenes'), $filename);
            $rutaImagen = $filename;
        }

        $opciones = [];
        if ($request->filled('opciones_personalizacion')) {
            $opciones = json_decode($request->opciones_personalizacion, true) ?? [];
        }

        Product::create([
            'nombre' => $request->nombre,
            'nombre_en' => $request->nombre_en,
            'slug' => Str::slug($request->nombre) . '-' . uniqid(),
            'precio' => $request->precio,
            'categoria' => $request->categoria,
            'descripcion' => $request->descripcion,
            'descripcion_en' => $request->descripcion_en,
            'imagen_url' => $rutaImagen, 
            'is_active' => true,
            'is_available' => true,
            'opciones_personalizacion' => $opciones 
        ]);

        return redirect()->route('admin.products.index')->with('success', __('admin/products/messages.create_success'));
    }

    public function edit($id)
    {
        $producto = Product::findOrFail($id);
        $categorias = Product::select('categoria')->distinct()->pluck('categoria');
        return view('admin.products.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        $producto = Product::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'nombre_en' => 'nullable|string|max:255',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'required|string',
            'descripcion' => 'nullable|string',
            'descripcion_en' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $data = [
            'nombre' => $request->nombre,
            'nombre_en' => $request->nombre_en,
            'precio' => $request->precio,
            'categoria' => $request->categoria,
            'descripcion' => $request->descripcion,
            'descripcion_en' => $request->descripcion_en,
        ];

        if ($request->filled('opciones_personalizacion')) {
            $data['opciones_personalizacion'] = json_decode($request->opciones_personalizacion, true) ?? [];
        }

        if ($request->hasFile('imagen')) {
            if ($producto->imagen_url) {
                $oldPath = public_path('imagenes/' . $producto->imagen_url);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $file = $request->file('imagen');
            $filename = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('imagenes'), $filename);
            
            $data['imagen_url'] = $filename;
        }

        $producto->update($data);

        return redirect()->route('admin.products.index')->with('success', __('admin/products/messages.update_success'));
    }

    public function destroy($id)
    {
        $producto = Product::findOrFail($id);
        $producto->delete();
        return redirect()->route('admin.products.index')->with('success', __('admin/products/messages.trash_success'));
    }

    public function restore($id)
    {
        $producto = Product::withTrashed()->findOrFail($id);
        $producto->restore();
        return redirect()->route('admin.products.index', ['ver_bajas' => 1])->with('success', __('admin/products/messages.restore_success'));
    }

    public function forceDestroy($id)
    {
        $producto = Product::withTrashed()->findOrFail($id);
        
        try {
            $producto->forceDelete();
            
            if ($producto->imagen_url) {
                $path = public_path('imagenes/' . $producto->imagen_url);
                if (File::exists($path)) {
                    File::delete($path);
                }
            }

            return redirect()->route('admin.products.index', ['ver_bajas' => 1])
                             ->with('success', __('admin/products/messages.force_delete_success'));

        } catch (QueryException $e) {
            if ($e->getCode() == "23000") {
                return redirect()->route('admin.products.index', ['ver_bajas' => 1])
                                 ->with('error', __('admin/products/messages.force_delete_error'));
            }
            return redirect()->route('admin.products.index', ['ver_bajas' => 1])
                             ->with('error', __('admin/products/messages.db_error'));
        }
    }

    public function toggleStatus($id)
    {
        $producto = Product::findOrFail($id);
        $producto->is_active = !$producto->is_active;
        $producto->save();

        return response()->json([
            'success' => true,
            'is_active' => $producto->is_active,
            'message' => 'Estado actualizado'
        ]);
    }
}