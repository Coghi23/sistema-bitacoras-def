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
        Schema::create(('evento'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bitacora')->constrained('bitacora')->onDelete('cascade');
            $table->foreignId('id_profesor')->constrained('profesor')->onDelete('cascade');
            $table->timestamp('fecha');
            $table->string('observacion',255);
            $table->string('prioridad',255);
            $table->boolean('confirmacion');
            $table->tinyInteger('condicion')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento');
    }
};
