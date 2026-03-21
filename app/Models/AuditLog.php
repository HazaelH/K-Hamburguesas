<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['admin_name', 'empleado_name', 'accion', 'detalle'];
}
