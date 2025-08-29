<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacora';
    
    
    protected $fillable = [
        'id_recinto',
        'condicion'
    ];

    public function recinto()
    {
    
        return $this->belongsTo(Recinto::class, 'id_recinto');
    
    }
    
    public function evento(){

        return $this->hasMany(Evento::class, 'id_bitacora');

    }
}
