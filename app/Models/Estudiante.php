<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Curso;
use App\Models\Padre;
use App\Models\FaltaGrave;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';

    protected $fillable = [
        'curso_id',
        'padre_id',
        'nombre',
        'apellido',
        'numero',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function padre()
    {
        return $this->belongsTo(Padre::class);
    }

    public function faltas()
    {
        return $this->hasMany(FaltaGrave::class);
    }
}
