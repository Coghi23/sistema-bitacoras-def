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
        Schema::table("subareas", function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_especialidad')->constrained('especialidades')->onDelete('cascade');
            $table->string('nombre',55);
            $table->tinyInteger('condicion')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
