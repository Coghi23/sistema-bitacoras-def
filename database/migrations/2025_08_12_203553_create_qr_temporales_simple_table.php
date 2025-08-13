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
            $table->string('codigo_qr', 20)->unique();
            $table->unsignedBigInteger('recinto_id');
            $table->unsignedBigInteger('profesor_id');
            $table->unsignedBigInteger('llave_id');
            $table->boolean('usado')->default(false);
            $table->timestamp('expira_en');
            $table->timestamps();
            
            // Foreign keys sin constraints para evitar problemas
            $table->index('recinto_id');
            $table->index('profesor_id');
            $table->index('llave_id');
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
