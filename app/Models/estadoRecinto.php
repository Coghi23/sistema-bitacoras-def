<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoRecinto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre' 
    ];

    public function recinto()
    {
        return $this->hasMany(Recinto::class, 'estadoRecinto_id');
    }

    protected $table = 'estadoRecinto';

}
