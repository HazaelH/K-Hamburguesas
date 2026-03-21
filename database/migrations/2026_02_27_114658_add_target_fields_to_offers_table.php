<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            // 'todo', 'categoria', 'producto'
            $table->string('tipo_aplicacion')->default('todo')->after('descripcion'); 
            // Guardará el nombre de la categoría o el ID del producto
            $table->string('referencia')->nullable()->after('tipo_aplicacion');
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn(['tipo_aplicacion', 'referencia']);
        });
    }
};