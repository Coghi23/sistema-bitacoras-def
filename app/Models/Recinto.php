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
        'llave_id',
        'nombre',
        'tipoRecinto_id',
        'estadoRecinto_id',
        'condicion',
    ];

    // Un recinto *pertenece a* una institución via institucion_id
    public function institucion()
    {
        return $this->belongsTo(Institucione::class);
    }
    public function llave()
    {
        return $this->belongsTo(Llave::class);
    }

    public function tipoRecinto()
    {
        return $this->belongsTo(TipoRecinto::class);
    }

    public function estadoRecinto()
    {
        return $this->belongsTo(EstadoRecinto::class);
    }
    // Un recinto *puede tener* múltiples horarios
    /*public function horarios()
    {
        return $this->hasMany(Horario::class);
    }*/
}