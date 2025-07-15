<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recinto extends Model
{
    use HasFactory;

    protected $table = 'recinto';   
    public $timestamps = false;     

    protected $fillable = [
        'institucion_id',
        'nombre',
        'tipo',
        'estado',
        'condicion',
    ];

    // Un recinto *pertenece a* una institución via institucion_id
    public function institucion()
    {
        return $this->belongsTo(Institucion::class);
    }

    // Un recinto *puede tener* múltiples horarios
    /*public function horarios()
    {
        return $this->hasMany(Horario::class);
    }*/
}