<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoRecinto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre' 
    ];

    public function recinto()
    {
        return $this->hasMany(Recinto::class, 'tipoRecinto_id');
    }

    protected $table = 'tipoRecinto';

}
