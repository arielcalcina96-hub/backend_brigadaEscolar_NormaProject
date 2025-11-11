<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // BIGINT PK
            $table->string('name'); // Nombre completo
            $table->string('email')->unique(); // Email único para login
            $table->string('password'); // Contraseña (hash)
            $table->enum('role', ['padre', 'director']); // Rol del usuario
            $table->timestamps(); // created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
