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
        
        Schema::create("especialidad", function (Blueprint $table) {

        $table->id();
        $table->foreignId('id_institucion')->nullable()->constrained('institucion')->onDelete('set null');
        $table->string('nombre', 50);
       $table->tinyInteger('condicion')->default(1);

        $table->timestamps();

        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('especialidad');
    }
};
