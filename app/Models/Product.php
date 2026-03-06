<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache; // <--- AGREGA ESTA LÍNEA
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';
    protected $primaryKey = 'id_producto';

    protected $fillable = [
        'nombre',
        'nombre_en',       // <--- NUEVO
        'slug',
        'precio',
        'descripcion',
        'descripcion_en',  // <--- NUEVO
        'imagen_url',
        'categoria',
        'opciones_personalizacion', 
        'is_active',    
        'is_available'  
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'is_active' => 'boolean',
        'is_available' => 'boolean',
        'opciones_personalizacion' => 'array',
    ];

    protected $appends = [
        'precio_final',
        'nombre_traducido', 
        'descripcion_traducida', 
        'categoria_traducida' // <-- ¡Asegúrate de agregar este!
    ];

    // ==========================================
    // ACCESORES DE TRADUCCIÓN AUTOMÁTICA
    // ==========================================
    public function getNombreTraducidoAttribute()
    {
        // Si estamos en Inglés y hay texto
        if (app()->getLocale() === 'en' && !empty($this->nombre_en)) {
            return $this->nombre_en;
        }
        // Si estamos en Portugués y hay texto
        if (app()->getLocale() === 'pt' && !empty($this->nombre_pt)) {
            return $this->nombre_pt;
        }
        
        // Retorno por defecto (Español)
        return $this->nombre;
    }

    public function getDescripcionTraducidaAttribute()
    {
        if (app()->getLocale() === 'en' && !empty($this->descripcion_en)) {
            return $this->descripcion_en;
        }
        if (app()->getLocale() === 'pt' && !empty($this->descripcion_pt)) {
            return $this->descripcion_pt;
        }
        
        return $this->descripcion;
    }

    // ==========================================
    // LÓGICA DE PRECIOS
    // ==========================================
    public function getPrecioFinalAttribute()
    {
        $precioOriginal = $this->precio;
        $hoy = now()->toDateString();

        $ofertasActivas = \App\Models\Offer::where('activa', true)
            ->whereDate('fecha_inicio', '<=', $hoy)
            ->whereDate('fecha_fin', '>=', $hoy)
            ->get();

        if ($ofertasActivas->isEmpty()) {
            return $precioOriginal;
        }

        $ofertasAplicables = $ofertasActivas->filter(function ($oferta) {
            if ($oferta->tipo_aplicacion === 'producto' && $oferta->referencia == $this->id_producto) return true;
            if ($oferta->tipo_aplicacion === 'categoria' && $oferta->referencia === $this->categoria) return true;
            if ($oferta->tipo_aplicacion === 'todo') return true;
            return false;
        });

        if ($ofertasAplicables->isEmpty()) {
            return $precioOriginal;
        }

        $ofertaGanadora = $ofertasAplicables->sortBy(function ($oferta) {
            if ($oferta->tipo_aplicacion === 'producto') return 1;
            if ($oferta->tipo_aplicacion === 'categoria') return 2;
            return 3;
        })->first();

        if ($ofertaGanadora->precio_promo > 0) {
            return $ofertaGanadora->precio_promo;
        }
        
        if ($ofertaGanadora->porcentaje > 0) {
            return $precioOriginal - ($precioOriginal * ($ofertaGanadora->porcentaje / 100));
        }

        return $precioOriginal;
    }

    public function getCategoriaTraducidaAttribute()
    {
        if (empty($this->categoria)) {
            return '';
        }

        // Descargamos y cacheamos las categorías para no saturar la BD
        $categoriasDict = Cache::rememberForever('diccionario_categorias', function () {
            return DB::table('categorias')->get()->keyBy('nombre')->toArray();
        });

        // Buscamos si existe la categoría en nuestro diccionario
        $cat = $categoriasDict[$this->categoria] ?? null;

        if ($cat) {
            $locale = app()->getLocale();
            if ($locale === 'en' && !empty($cat->nombre_en)) {
                return $cat->nombre_en;
            }
            if ($locale === 'pt' && !empty($cat->nombre_pt)) {
                return $cat->nombre_pt;
            }
        }

        // Si no tiene traducción o no existe en la tabla, devolvemos la original en español
        return $this->categoria;
    }
}