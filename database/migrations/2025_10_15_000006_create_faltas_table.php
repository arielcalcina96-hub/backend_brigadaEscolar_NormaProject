<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\FaltaGrave;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faltas', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('tipo_falta_id')->constrained('tipo_faltas')->onDelete('cascade');
            $table->foreignId('estudiante_id')->constrained('estudiantes')->onDelete('cascade'); 
            $table->string('descripcion'); 
            $table->string('estado')->default('pendiente');
            $table->date('fecha');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faltas');
    }
};
