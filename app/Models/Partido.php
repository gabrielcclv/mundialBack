<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    use HasFactory;

protected $fillable = [
        'equipo_local',
        'equipo_visitante',
        'fase',
        'fecha_partido', 
        'goles_local',
        'goles_visitante',
        'estado' 
    ];

    protected $table = 'partidos';

    public function predicciones(){
        return $this->hasMany(Prediccion::class, 'partido_id');
    }
}
