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

    public function getStatusTraducidoAttribute()
    {
        $statusDb = strtolower(trim($this->status));

        $traducciones = [
            'pendiente'   => __('admin/orders/orders.status_pendiente'),
            'pagado'      => __('admin/orders/orders.status_pagado'),
            'preparando'  => __('admin/orders/orders.status_preparando'),
            'cocinando'   => __('admin/orders/orders.status_cocinando'),
            'listo'       => __('admin/orders/orders.status_listo'),
            'en_camino'   => __('admin/orders/orders.status_en_camino'),
            'entregado'   => __('admin/orders/orders.status_entregado'),
            'cancelado'   => __('admin/orders/orders.status_cancelado'),
        ];

        return $traducciones[$statusDb] ?? ucfirst($statusDb);
    }
}