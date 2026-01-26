<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Permitimos todo (Excelente para evitar errores de MassAssignment)
    protected $guarded = [];

    // TRUCO: Esto convierte automáticamente el JSON a Array y viceversa
    protected $casts = [
        'datos_entrega' => 'array',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}