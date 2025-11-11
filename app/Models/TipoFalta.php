<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sancion;
use App\Models\FaltaGrave;

class TipoFalta extends Model
{
    use HasFactory;

    protected $table = 'tipo_faltas';

    protected $fillable = [
        'sancion_id',
        'nombre',
    ];

    public function sancion()
    {
        return $this->belongsTo(Sancion::class);
    }

    public function faltas()
    {
        return $this->hasMany(FaltaGrave::class);
    }
}
