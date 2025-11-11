<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_faltas', function (Blueprint $table) {
            $table->id(); // BIGINT PK 
            $table->foreignId('sancion_id')->constrained('sanciones')->onDelete('cascade'); // FK a sanciones
            $table->string('nombre'); // Nombre del tipo de falta
            $table->timestamps(); // created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_faltas');
    }
};
