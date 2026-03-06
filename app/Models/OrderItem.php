<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relación inversa: Un item pertenece a una orden
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Un item es de un producto específico
    public function product()
    {
        // Nota: usamos 'id_producto' porque así le pusiste en tu migración
        return $this->belongsTo(Product::class, 'product_id', 'id_producto');
    }
}