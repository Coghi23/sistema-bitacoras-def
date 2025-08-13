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
        Schema::create('llave', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 60);
            $table->tinyInteger('estado')->default(0); // 0: No entregada, 1: Entregada
            $table->tinyInteger('condicion')->default(1); // 1: Activa, 0: Inactiva (eliminación lógica)
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('llave');
    }
};
