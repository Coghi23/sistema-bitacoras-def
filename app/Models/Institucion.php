<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Storage;
use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{

    use HasFactory;

    protected $fillable = [
        'nombre',
        'condicion'
    ];

    public function usuarioInstitucion(){

        return $this->hasMany(UsuarioInstitucion::class);

    }

    public function especialidad(){

        return $this->hasMany(Especialidad::class);

    }
    
}
