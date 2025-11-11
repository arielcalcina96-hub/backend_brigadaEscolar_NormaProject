<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TipoFalta;

class Sancion extends Model
{
    use HasFactory;

    protected $table = 'sanciones';

    protected $fillable = [
        'descripcion',
    ];

    public function tipos_faltas()
    {
        return $this->hasMany(TipoFalta::class);
    }
}
