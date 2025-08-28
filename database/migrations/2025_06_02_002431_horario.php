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
           $table->date('fecha')->nullable();
           $table->string('dia')->nullable();
           $table->foreignId('idRecinto')->constrained('recinto')->onDelete('cascade');
           $table->foreignId('idSubarea')->constrained('subarea')->onDelete('cascade');
           $table->foreignId('idSeccion')->constrained('seccione')->onDelete('cascade');
           $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
           $table->tinyInteger('condicion')->default(1);
           $table->timestamps();
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');   
    }
};
