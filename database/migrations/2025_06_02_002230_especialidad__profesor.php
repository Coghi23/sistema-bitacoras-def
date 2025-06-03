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
        Schema::create('EspecialidadesProfesores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('especialidad_id')->unique()->constrained('especialidades')->onDelete('cascade');
            $table->foreignId('profesor_id')->unique()->constrained('profesores')->onDelete('cascade');    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        chema::dropIfExists('EspecialidadesProfesores');
    }
};
