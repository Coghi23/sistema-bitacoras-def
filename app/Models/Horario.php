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
                        'idLeccion', 
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
        return $this->belongsTo(Leccion::class, 'idLeccion');
    }

    public function profesor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function profesorUsuario()
    {
         return $this->belongsTo(User::class, 'user_id')->where('rol', 'profesor');
    }
}

