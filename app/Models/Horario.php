<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $fillable = ['idRecinto', 
                        'idSubareaSeccion', 
                        'user_id', // Cambiado de idProfesor a user_id
                        'tipoHorario', 
                        'horaEntrada',
                        'horaSalida',
                        'fecha',
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
        // Relación con User, solo para usuarios con rol 'profesor'
        return $this->belongsTo(User::class, 'user_id');
    }

    // Opcional: scope para obtener solo usuarios con rol profesor
    public function profesorUsuario()
    {
         return $this->belongsTo(User::class, 'user_id')->where('rol', 'profesor');
    }
}

