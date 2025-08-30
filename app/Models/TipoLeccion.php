<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoLeccion extends Model
{
    use HasFactory;
    
    protected $table = 'tipo_leccion';

    protected $fillable = [
        'nombre',
        'condicion',
    ];

    // Relación: Un tipo de lección puede tener muchas lecciones
    public function lecciones()
    {
        return $this->hasMany(Leccion::class, 'idTipoLeccion');
    }
}