<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;

class StockController extends Controller
{
    public function index()
    {
        $productos = Product::orderBy('categoria')->orderBy('nombre')->get();
        return view('employee.stock.stock', compact('productos'));
    }

    // ÚNICA función para solicitar cambio (Agotar/Activar)
    public function requestStatusChange($id)
    {
        try {
            $producto = Product::findOrFail($id);
            
            // Usamos las traducciones para la acción deseada
            $accionDeseada = $producto->is_active 
                ? __('employee/stock/messages.action_out_of_stock') 
                : __('employee/stock/messages.action_activate');
                
            $cacheKey = 'alerta_stock_' . $producto->id_producto;
            
            Cache::put($cacheKey, [
                'id' => $producto->id_producto,
                'nombre' => $producto->nombre,
                'accion' => $accionDeseada,
                'empleado' => auth()->user()->name,
                'fecha' => now()->format('H:i')
            ], now()->addHours(24));

            return response()->json([
                'success' => true,
                'message' => __('employee/stock/messages.request_sent', ['action' => $accionDeseada])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('employee/stock/messages.server_error', ['message' => $e->getMessage()])
            ], 500);
        }
    }
}