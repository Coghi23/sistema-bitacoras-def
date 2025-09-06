<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    use HasFactory;
    
    protected $table = 'instituciones';
    
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email'
    ];

    public function recintos()
    {
        return $this->hasMany(Recinto::class);
    }
}
