<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Subarea extends Model
{
    use HasFactory;

    protected $table = 'subarea';
    public function especialidad(){
        return $this->belongsTo(Especialidade::class, 'id_especialidad', 'id');
    }

    public function secciones(){
        return $this->belongsToMany(Seccione::class, 'subareaseccion', 'id_subarea', 'id_seccion')
               ->withPivot('condicion')
               ->withTimestamps();
    }


    protected $fillable =['id_especialidad','nombre', 'condicion'];

}
