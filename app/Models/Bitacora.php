<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    
    protected $fillable = [
        'id_recinto',
        'condicion',
    ];

    public function recinto()
    {
    
        return $this->belongsTo(Recinto::class, 'id_recinto');
    
    }

    public function docente()
    {
    
        return $this->belongsTo(Profesor::class, 'id_profesor');
    
    }

    public function seccion()
    {
    
        return $this->belongsTo(Seccion::class, 'id_seccion');
    
    }

    public function subarea()
    {
    
        return $this->belongsTo(Subarea::class, 'id_subarea');
    
    }

    public function horario()
    {
    
        return $this->belongsTo(Horario::class, 'id_horario');
    
    }

    public function evento(){

        return $this->hasMany(Evento::class, 'id_bitacora');

    }
}
