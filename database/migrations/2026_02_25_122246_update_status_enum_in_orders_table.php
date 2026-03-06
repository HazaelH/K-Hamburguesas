<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Actualizamos la lista ENUM para que acepte todos los estados de tu sistema actual
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pendiente', 'pagado', 'preparando', 'cocinando', 'listo', 'en_camino', 'entregado', 'cancelado') NOT NULL DEFAULT 'pendiente'");
    }

    public function down()
    {
        // Opcional: regresar a una lista básica si te equivocas
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pendiente', 'preparando', 'en_camino', 'entregado', 'cancelado') NOT NULL DEFAULT 'pendiente'");
    }
};