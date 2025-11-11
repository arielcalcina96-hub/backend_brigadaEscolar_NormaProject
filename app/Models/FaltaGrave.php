<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TipoFalta;
use App\Models\Estudiante;

class FaltaGrave extends Model
{
    use HasFactory;

    protected $table = 'faltas';

    protected $fillable = [
        'tipo_falta_id',
        'estudiante_id',
        'descripcion',
        'estado',
        'fecha',
    ];

    public function tipo_falta()
    {
        return $this->belongsTo(TipoFalta::class);
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }
}
