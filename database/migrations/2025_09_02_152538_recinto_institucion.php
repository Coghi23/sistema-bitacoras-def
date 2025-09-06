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
        Schema::create('recinto_institucion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recinto_id')->constrained('recinto');
            $table->foreignId('institucion_id')->constrained('institucione');
            $table->timestamps();
            $table->tinyInteger('condicion')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recinto_institucion');
    }
};
