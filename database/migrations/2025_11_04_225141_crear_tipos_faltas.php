<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ejecuta las inserciones en la base de datos.
     */
    public function up(): void
    {
        // Insertar sanciones base
        DB::table('sanciones')->insert([
            [
                'descripcion' => 'Advertencia verbal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Llamado de atención por escrito',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Suspensión de clases por un día',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Comunicación con los padres',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insertar tipos de faltas asociados a las sanciones
        DB::table('tipo_faltas')->insert([
            [
                'sancion_id' => 1,
                'nombre' => 'Corte de cabello',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sancion_id' => 2,
                'nombre' => 'Uniforme',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sancion_id' => 3,
                'nombre' => 'Comportamiento',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sancion_id' => 4,
                'nombre' => 'Impuntualidad',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Revertir las inserciones si se hace rollback.
     */
    public function down(): void
    {
        // Eliminar los tipos de faltas insertados
        DB::table('tipo_faltas')->whereIn('nombre', [
            'Corte de cabello',
            'Uniforme',
            'Comportamiento',
            'Impuntualidad',
        ])->delete();

        // Eliminar las sanciones insertadas
        DB::table('sanciones')->whereIn('descripcion', [
            'Advertencia verbal',
            'Llamado de atención por escrito',
            'Suspensión de clases por un día',
            'Comunicación con los padres',
        ])->delete();
    }
};
