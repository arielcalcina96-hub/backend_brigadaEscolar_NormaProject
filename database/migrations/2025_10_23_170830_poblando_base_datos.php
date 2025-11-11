<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // -------------------
        // USUARIO DIRECTOR
        // -------------------
        $directorId = DB::table('users')->insertGetId([
            'name' => 'Juan Perez',
            'email' => 'director@gmail.com',
            'password' => Hash::make('director'),
            'role' => 'director',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // -------------------
        // CURSO
        // -------------------
        $cursoId = DB::table('cursos')->insertGetId([
            'nombre' => '6to de Secundaria',
            'nombre_asesor' => 'Laura Sanchez',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // -------------------
        // PADRES Y ESTUDIANTES
        // -------------------
        $datos = [
            ['estudiante' => 'Vanessa Bautista Quiroz', 'padre' => 'Bonifacia Quiroz'],
            ['estudiante' => 'Norma Cardozo Cotrina', 'padre' => 'Jovita Cotrina'],
            ['estudiante' => 'Nadiely Jaimes Juyari', 'padre' => 'Romalda Juyari'],
            ['estudiante' => 'Ana Lizeth Loayza Nava', 'padre' => 'Martina Nava'],
            ['estudiante' => 'Kevin Israel Lopez Garcia', 'padre' => 'Emiliana Garcia'],
            ['estudiante' => 'Edison Lopez Llanos', 'padre' => 'Rosa Llanos'],
            ['estudiante' => 'Ariel Martinez Soraide', 'padre' => 'Felipa Soraide'],
            ['estudiante' => 'Wilber Puma Gutierrez', 'padre' => 'Damian Puma'],
            ['estudiante' => 'Luis Rojas Camacho', 'padre' => 'German Rojas'],
            ['estudiante' => 'Kevin Rojas Arias', 'padre' => 'Ercelia Arias'],
            ['estudiante' => 'Jose Brandon Rocabado Aranibal', 'padre' => 'Isidoro Rocabado'],
            ['estudiante' => 'Jose Alvaro Rosa Mamani', 'padre' => 'Ilda Mamani'],
            ['estudiante' => 'Maria Elva Salazar Lopez', 'padre' => 'Justina Lopez'],
            ['estudiante' => 'Ruben Veizaga Ramos', 'padre' => 'Leonor Ramos'],
            ['estudiante' => 'Jhon Brian Vela Herbas', 'padre' => 'Juanita Herbas'],
        ];

        foreach ($datos as $index => $item) {
            // crear usuario padre
            $userPadreId = DB::table('users')->insertGetId([
                'name' => $item['padre'],
                'email' => strtolower(str_replace(' ', '.', $item['padre'])) . '@gmail.com',
                'password' => Hash::make('padre123'),
                'role' => 'padre',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // crear registro padre
            $padreId = DB::table('padres')->insertGetId([
                'user_id' => $userPadreId,
                'nombre' => explode(' ', $item['padre'])[0],
                'apellido' => explode(' ', $item['padre'])[1] ?? '',
                'numero' => '789' . str_pad($index + 1000, 4, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // crear estudiante
            $nombres = explode(' ', $item['estudiante']);
            $nombre = array_shift($nombres);
            $apellido = implode(' ', $nombres);

            DB::table('estudiantes')->insert([
                'curso_id' => $cursoId,
                'padre_id' => $padreId,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'numero' => '7123' . str_pad($index + 1000, 4, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('estudiantes')->truncate();
        DB::table('padres')->truncate();
        DB::table('users')->where('role', 'padre')->orWhere('role', 'director')->delete();
        DB::table('cursos')->where('nombre', '6to de Secundaria')->delete();
    }
};