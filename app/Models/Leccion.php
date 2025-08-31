<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Storage;
use Illuminate\Database\Eloquent\Model;

class Leccion extends Model
{
    use HasFactory;
    protected $table = 'leccion';

    protected $fillable = [
        'leccion',
        'hora_inicio',
        'hora_final',
    ];


    public function horarios()
    {
        return $this->belongsToMany(Horario::class, 'horario_leccion', 'idLeccion', 'idHorario');
    }

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class);
    }

}
