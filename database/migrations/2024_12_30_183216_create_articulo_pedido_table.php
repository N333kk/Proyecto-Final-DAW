<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticuloPedidoTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articulo_pedido', function (Blueprint $table) {
            $table->id();
            $table->foreignId('articulo_id')->constrained('articulos')->onDelete('cascade');
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->integer('cantidad');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articulo_pedido');
    }
}
