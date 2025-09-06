<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seccione extends Model
{
    use HasFactory;

    protected $table = 'seccione';

    public function especialidades(){
        return $this->belongsToMany(Especialidade::class, 'especialidad_seccion', 'seccion_id', 'especialidad_id')
            ->withPivot('condicion')
            ->withTimestamps();
    }

    public function subareas(){
        return $this->belongsToMany(Subarea::class, 'subareaseccion', 'id_seccion', 'id_subarea')
            ->withPivot('condicion')
            ->withTimestamps();
    }

    protected $fillable = [
        'nombre',
        'id_institucion',
        'condicion',
    ];
}
