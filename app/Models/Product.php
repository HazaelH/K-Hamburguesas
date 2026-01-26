<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'products';

    // Tu llave primaria es 'id_producto', no 'id'
    protected $primaryKey = 'id_producto';

    // Estos son los campos que permitimos guardar
    protected $fillable = [
        'nombre',
        'slug',
        'precio',
        'descripcion',
        'imagen_url',
        'categoria',
        'opciones_personalizacion', // <--- IMPORTANTE: Debe llamarse igual que en la BD
        'is_active',    // Usaremos este para el Stock (Disponibilidad manual)
        'is_available'  // Este lo tienes en la BD, lo dejamos por si lo usas en el futuro
    ];

    // Conversión automática de tipos
    protected $casts = [
        'precio' => 'decimal:2',
        'is_active' => 'boolean',
        'is_available' => 'boolean',
        'opciones_personalizacion' => 'array', // <--- IMPORTANTE: Para que se lea como JSON automáticamente
    ];
}