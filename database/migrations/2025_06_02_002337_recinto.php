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
        Schema::create('recinto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institucion_id')->constrained('institucione');
            $table->foreignId('llave_id')->constrained('llave');
            $table->string('nombre');
            $table->foreignId('tipoRecinto_id')->constrained('tipoRecinto');
            $table->foreignId('estadoRecinto_id')->constrained('estadoRecinto');
            $table->tinyInteger('condicion')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recinto');
    }
};
