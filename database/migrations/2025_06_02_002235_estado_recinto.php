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
         Schema::create('estadoRecinto', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 60);
            $table->string('color', 20)->nullable();
            $table->tinyInteger('condicion')->default(1);
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estadoRecinto');
    }
};
