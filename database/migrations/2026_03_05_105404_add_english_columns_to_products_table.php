<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Agregamos las columnas justo después de las originales en español
            $table->string('nombre_en')->nullable()->after('nombre');
            $table->text('descripcion_en')->nullable()->after('descripcion');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['nombre_en', 'descripcion_en']);
        });
    }
};
