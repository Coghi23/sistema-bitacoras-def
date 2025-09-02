<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('especialidad_institucion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('especialidad_id')->constrained('especialidad')->onDelete('cascade');
            $table->foreignId('institucion_id')->constrained('institucione')->onDelete('cascade');
            $table->tinyInteger('condicion')->default(1);
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('especialidad_institucion');
    }
};
