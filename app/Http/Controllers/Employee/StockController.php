<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class StockController extends Controller
{
    public function index()
    {
        // Obtenemos todos los productos agrupados por categoría
        // Esto facilita visualizarlos en la pantalla
        $productos = Product::orderBy('categoria')->orderBy('nombre')->get();
        
        return view('employee.stock', compact('productos'));
    }

    public function toggle(Request $request, $id)
    {
        $producto = Product::findOrFail($id);
        
        // Invertimos el valor: Si es true pasa a false, y viceversa
        $producto->is_active = !$producto->is_active;
        $producto->save();

        return response()->json([
            'success' => true,
            'new_status' => $producto->is_active,
            'message' => $producto->is_active ? 'Producto Activado' : 'Producto Pausado'
        ]);
    }
}