<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacora';
    
    protected $fillable = [
        'id_recinto',
        'id_profesor',
        'id_seccion',
        'id_subarea',
        'id_horario',
        'condicion',
    ];

    public function recinto()
    {
    
        return $this->belongsTo(Recinto::class, 'id_recinto');
    
    }

    public function profesor()
    {
    
        return $this->belongsTo(Profesor::class, 'id_profesor');
    
    }

    public function seccion()
    {
    
        return $this->belongsTo(Seccione::class, 'id_seccion');
    
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
