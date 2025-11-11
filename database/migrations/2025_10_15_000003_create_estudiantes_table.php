<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id(); // BIGINT PK 
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade'); // FK a cursos
            $table->foreignId('padre_id')->nullable() // permite que sea null
                ->constrained('padres')
                ->nullOnDelete(); // FK a padres
            $table->string('nombre'); // Nombre del estudiante
            $table->string('apellido'); // Apellido
            $table->string('numero'); // num de contacto
            $table->timestamps(); // created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
