<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('padres', function (Blueprint $table) {
            $table->id(); // BIGINT PK 
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // FK a users
            $table->string('nombre'); // nom del padre
            $table->string('apellido'); // Apellido
            $table->string('numero'); // Número de contacto
            $table->timestamps(); // created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('padres');
    }
};
