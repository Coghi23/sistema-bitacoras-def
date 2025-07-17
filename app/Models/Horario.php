<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $fillable = ['idRecinto', 
                        'idSubareaSeccion', 
                        'idProfesor', 
                        'tipoHorario', 
                        'horaEntrada',
                        'horaSalida',
                        'dia',
                        'condicion'];
    
    
    public function recinto()
    {
        return $this->belongsTo(Recinto::class, 'idRecinto');
    }

    /*public function subareaSeccion()
    {
        return $this->belongsTo(SubareaSeccion::class, 'idSubareaSeccion');
    }*/

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor');
    }
}

