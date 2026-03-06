<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    // Los campos que permitimos guardar masivamente
    protected $fillable = [
        'user_id',
        'alias',
        'codigo_pais',
        'telefono',
        'calle',
        'numero',
        'codigo_postal',
        'colonia',
        'municipio',
        'estado',
        'referencias',
        'is_default',
    ];

    // Para asegurarnos de que is_default siempre se lea como true/false
    protected $casts = [
        'is_default' => 'boolean',
    ];

    // Relación Inversa: Una dirección pertenece a un Usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}