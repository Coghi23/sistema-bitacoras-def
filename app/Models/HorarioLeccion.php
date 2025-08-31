<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioLeccion extends Model
{
    protected $table = 'horario_leccion';

    public function leccion()
    {
        return $this->belongsTo(Leccion::class, 'idLeccion');
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class, 'idHorario');
    }
}
