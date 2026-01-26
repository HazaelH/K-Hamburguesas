<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo', 'descripcion', 'porcentaje', 'precio_promo', 
        'fecha_inicio', 'fecha_fin', 'imagen_url', 'activa'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activa' => 'boolean',
    ];

    // Función auxiliar para saber si la oferta está vigente hoy
    public function scopeVigentes($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('activa', true)
                     ->where('fecha_inicio', '<=', $today)
                     ->where('fecha_fin', '>=', $today);
    }
}
