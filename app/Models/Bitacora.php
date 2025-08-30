<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacora';
    
    
    protected $fillable = [
        'id_recinto',
        'id_seccion',
        'id_subarea',
        'id_horario',
        'id_horario_leccion',
        'usuario_id',
        'condicion',
    ];

    public function recinto()
    {
    
        return $this->belongsTo(Recinto::class, 'id_recinto');
    
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

    public function leccion()
    {
        return $this->belongsToMany(Leccion::class, 'horario_leccion', 'idHorario', 'idLeccion');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // Método para obtener solo profesores
    public function profesor()
    {
        return $this->belongsTo(User::class, 'id_usuario')->whereHas('roles', function($query) {
            $query->where('name', 'profesor');
        });
    }

    public function evento(){

        return $this->hasMany(Evento::class, 'id_bitacora');

    }
}
