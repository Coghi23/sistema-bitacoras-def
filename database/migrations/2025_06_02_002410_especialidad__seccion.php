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
            $table->foreignId('id_especialidad')->nullable()->constrained('especialidad')->onDelete('set null');
            $table->foreignId('id_seccion')->nullable()->constrained('seccione')->onDelete('set null');
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
