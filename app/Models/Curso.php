<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Estudiante;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';

    protected $fillable = [
        'nombre',
        'nombre_asesor',
    ];

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class);
    }
}
