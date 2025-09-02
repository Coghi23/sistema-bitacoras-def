<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Storage;
use Illuminate\Database\Eloquent\Model;

class Institucione extends Model
{
    use HasFactory;

     

    protected $fillable = [
        'nombre' 
    ];

    public function especialidad()
    {
        return $this->belongsToMany(Especialidade::class, 'especialidad_institucion', 'institucion_id', 'especialidad_id')
                    ->withPivot('condicion')
                    ->withTimestamps();
    }

    protected $table = 'institucione';

}
