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
        Schema::create('horarios', function (Blueprint $table) {
           $table->id();
           $table->boolean('tipoHorario')->default(true);
           $table->time('horaEntrada');
           $table->time('horaSalida');
           $table->date('dia');
           $table->foreignId('id_recinto')->constrained('recinto')->onDelete('cascade');
           $table->foreignId('id_subareaseccion')->constrained('subareaseccion')->onDelete('cascade');
           $table->foreignId('id_profesor')->constrained('profesor')->onDelete('cascade');
          $table->tinyInteger('condicion')->default(1);
           $table->timestamps();
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horario');   
    }
};
