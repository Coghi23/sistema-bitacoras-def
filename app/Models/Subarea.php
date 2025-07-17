<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Subarea extends Model
{
    use HasFactory;

    public function especialidad(){
        return $this->belongsTo(Especialidade::class);
    }

    public function secciones(){
        return $this->belongsToMany(Seccion::class, 'subareaseccion', 'id_subarea', 'id_seccion')
               ->withPivot('condicion')
               ->withTimestamps();
    }


    protected $fillable =['id_especialidad','nombre', 'condicion'];

}
