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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            $table->string('cliente');
            $table->string('telefono');
            
            $table->enum('estado', [
                'pendiente',
                'contactado',
                'confirmado',
                'preparacion',
                'entregado',
                'cancelado'
            ])->default('pendiente');

            $table->text('notas')->nullable();

            $table->decimal('total', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
