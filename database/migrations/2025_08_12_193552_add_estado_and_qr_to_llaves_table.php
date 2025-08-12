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
        Schema::table('llave', function (Blueprint $table) {
            $table->tinyInteger('estado')->default(0)->after('nombre'); // 0: No entregada, 1: Entregada
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('llave', function (Blueprint $table) {
            $table->dropColumn(['estado']);
        });
    }
};
