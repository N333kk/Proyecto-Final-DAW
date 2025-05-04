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
        Schema::create('articulo_tallas', function (Blueprint $table) {
            $table->id();
            $table->integer('stock')->default(150);
            $table->foreignId('articulo_id')->constrained()->onDelete('cascade');
            $table->foreignId('talla_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articulo_tallas');
    }
};
