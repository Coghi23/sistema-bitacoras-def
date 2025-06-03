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
            $table->foreignId('institucion_id')->constrained();
            $table->string('nombre');
            $table->string('tipo');
            $table->string('estado');
            $table->boolean('condicion')->default(true);
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
