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
        Schema::table('offers', function (Blueprint $table) {
            $table->string('titulo_en')->nullable()->after('titulo');
            $table->string('titulo_pt')->nullable()->after('titulo_en');
            $table->text('descripcion_en')->nullable()->after('descripcion');
            $table->text('descripcion_pt')->nullable()->after('descripcion_en');
        });
    }

    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn(['titulo_en', 'titulo_pt', 'descripcion_en', 'descripcion_pt']);
        });
    }
};
