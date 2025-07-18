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
        return $this->hasMany(Especialidade::class, 'id_institucion');
    }

    protected $table = 'institucione';

}
