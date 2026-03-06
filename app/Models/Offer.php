<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo', 
        'titulo_en', // <-- VITAL
        'titulo_pt', // <-- VITAL
        'descripcion', 
        'descripcion_en', // <-- VITAL
        'descripcion_pt', // <-- VITAL
        'tipo_aplicacion',
        'referencia',      
        'porcentaje',
        'precio_promo',
        'fecha_inicio',
        'fecha_fin',
        'imagen_url',
        'activa'
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

    public function getTituloTraducidoAttribute()
    {
        if (app()->getLocale() === 'en' && !empty($this->titulo_en)) return $this->titulo_en;
        if (app()->getLocale() === 'pt' && !empty($this->titulo_pt)) return $this->titulo_pt;
        return $this->titulo;
    }

    public function getDescripcionTraducidaAttribute()
    {
        if (app()->getLocale() === 'en' && !empty($this->descripcion_en)) return $this->descripcion_en;
        if (app()->getLocale() === 'pt' && !empty($this->descripcion_pt)) return $this->descripcion_pt;
        return $this->descripcion;
    }
}
