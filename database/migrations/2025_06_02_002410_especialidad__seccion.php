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
        Schema::create("especialidad_seccion", function (Blueprint $table) {
            $table->id();
            $table->foreignId('especialidad_id')->constrained('especialidad')->onDelete('cascade');
            $table->foreignId('seccion_id')->constrained('seccione')->onDelete('cascade');
            $table->tinyInteger('condicion')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('especialidad_seccion');
    }
};
