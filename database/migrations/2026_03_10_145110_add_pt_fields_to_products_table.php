<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('nombre_pt')->nullable()->after('nombre');
            $table->text('descripcion_pt')->nullable()->after('descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['nombre_pt', 'descripcion_pt']);
        });
    }
};