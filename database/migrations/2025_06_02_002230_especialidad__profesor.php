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
        Schema::create('especialidadprofesor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('especialidad_id')->constrained('especialidad')->onDelete('cascade');
            $table->foreignId('profesor_id')->constrained('profesor')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('especialidadprofesor');
    }
};
