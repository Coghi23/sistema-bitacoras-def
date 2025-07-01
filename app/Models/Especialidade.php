<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Especialidade extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'especialidad';

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'id_institucion',
        'nombre',
        'condicion'
    ];

    // Relación con Institución
    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'id_institucion');
    }
}
