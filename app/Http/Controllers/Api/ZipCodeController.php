<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ZipCodeController extends Controller
{
    public function show($cp)
    {
        // Buscamos todas las colonias que coincidan con ese CP
        $resultados = DB::table('zip_codes')
                        ->where('codigo_postal', $cp)
                        ->get();

        if ($resultados->isEmpty()) {
            return response()->json(['error' => 'Código postal no encontrado'], 404);
        }

        // Estructuramos la respuesta
        return response()->json([
            'estado' => $resultados->first()->estado,
            'municipio' => $resultados->first()->municipio,
            'colonias' => $resultados->pluck('colonia') // Devuelve array de colonias
        ]);
    }

    public function searchByColonia($nombre)
    {
        // Buscamos colonias que contengan el texto (ej: "San...")
        // Limitamos a 15 para no saturar la lista
        $resultados = DB::table('zip_codes')
                        ->where('colonia', 'LIKE', '%' . $nombre . '%')
                        ->limit(15)
                        ->get();

        return response()->json($resultados);
    }
}