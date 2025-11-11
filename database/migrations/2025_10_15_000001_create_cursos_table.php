<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id(); // BIGINT PK 
            $table->string('nombre'); // Nombre del curso
            $table->string('nombre_asesor'); // Nombre del asesor del curso
            $table->timestamps(); // created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
