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
        Schema::create('qr_temporales', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_qr')->unique(); // Código QR aleatorio
            $table->unsignedBigInteger('recinto_id'); // ID del recinto
            $table->unsignedBigInteger('profesor_id'); // ID del profesor
            $table->unsignedBigInteger('llave_id'); // ID de la llave
            $table->boolean('usado')->default(false); // Si ya se usó
            $table->timestamp('expira_en'); // Cuándo expira el QR
            $table->timestamps();
            
            $table->foreign('recinto_id')->references('id')->on('recinto');
            $table->foreign('profesor_id')->references('id')->on('profesor');
            $table->foreign('llave_id')->references('id')->on('llave');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_temporales');
    }
};
