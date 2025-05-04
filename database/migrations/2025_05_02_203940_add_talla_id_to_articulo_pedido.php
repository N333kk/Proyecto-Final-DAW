<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articulo_pedido', function (Blueprint $table) {
            $table->foreignId('talla_id')->nullable()->constrained('tallas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articulo_pedido', function (Blueprint $table) {
            $table->dropForeign(['talla_id']);
            $table->dropColumn('talla_id');
        });
    }
};
