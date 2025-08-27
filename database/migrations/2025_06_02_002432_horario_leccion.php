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
        Schema::create('horario_leccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idHorario')->constrained('horarios')->onDelete('cascade');
            $table->foreignId('idLeccion')->nullable()->constrained('leccion')->onDelete('cascade');
            $table->tinyInteger('condicion')->default(1);
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horario_leccion');
    }
};