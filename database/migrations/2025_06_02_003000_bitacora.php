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
        Schema::create('bitacora', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_docente')->constrained('profesor');
            $table->foreignId('id_recinto')->constrained('recinto');
            $table->foreignId('id_seccion')->constrained('seccione');
            $table->foreignId('id_subarea')->constrained('subarea');
            $table->foreignId('id_horario')->constrained('horarios');
            $table->timestamp('fecha')->useCurrent();
            $table->time('hora_envio');
            $table->tinyInteger('condicion')->default(1);
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacora');
    }
};
