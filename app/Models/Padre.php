<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Estudiante;

class Padre extends Model
{
    use HasFactory;

    protected $table = 'padres';

    protected $fillable = [
        'user_id',
        'nombre',
        'apellido',
        'numero',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class);
    }
}
