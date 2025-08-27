<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horarios';
    
    protected $fillable = ['idRecinto', 
                        'idSubarea',
                        'idSeccion',
                        'user_id',
                        'tipoHorario', 
                        'fecha',
                        'dia',
                        'condicion'];
    

    protected $casts = [
        'tipoHorario' => 'boolean',
        'fecha' => 'date',
        'condicion' => 'integer'
    ];
    
    
    public function recinto()
    {
        return $this->belongsTo(Recinto::class, 'idRecinto');
    }

    public function subarea()
    {
        return $this->belongsTo(Subarea::class, 'idSubarea');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccione::class, 'idSeccion');
    }

    public function leccion()
    {
        return $this->belongsToMany(Leccion::class, 'horario_leccion', 'idHorario', 'idLeccion');
    }

    public function profesor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function profesorUsuario()
    {
         return $this->belongsTo(User::class, 'user_id')->where('rol', 'profesor');
    }

    public function getHoraEntradaAttribute()
    {
        if ($this->leccion->isEmpty()) {
            return 'N/A';
        }
        
        return $this->leccion->sortBy(function($leccion) {
            $hora = $leccion->hora_inicio;
            $time = explode(':', $hora);
            $hour = (int)$time[0];
            $minute = (int)$time[1];
            
            // Convertir a formato de 24 horas para ordenamiento
            if ($hour >= 7 && $hour <= 11) {
                return sprintf('%02d:%02d', $hour, $minute);
            } elseif ($hour == 12) {
                return sprintf('12:%02d', $minute);
            } else {
                return sprintf('%02d:%02d', $hour + 12, $minute);
            }
        })->first()->hora_inicio;
    }

    public function getHoraSalidaAttribute()
    {
        if ($this->leccion->isEmpty()) {
            return 'N/A';
        }
        
        return $this->leccion->sortByDesc(function($leccion) {
            $hora = $leccion->hora_final;
            $time = explode(':', $hora);
            $hour = (int)$time[0];
            $minute = (int)$time[1];
            
            // Convertir a formato de 24 horas para ordenamiento
            if ($hour >= 7 && $hour <= 11) {
                return sprintf('%02d:%02d', $hour, $minute);
            } elseif ($hour == 12) {
                return sprintf('12:%02d', $minute);
            } else {
                return sprintf('%02d:%02d', $hour + 12, $minute);
            }
        })->first()->hora_final;
    }

}