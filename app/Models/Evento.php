<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Evento extends Model
{
    use HasFactory;
    protected $table = 'evento';
    protected $fillable = [
        'idBitacora',
        'user_id',
        'fecha',
        'observacion',
        'prioridad',
        'confirmacion',
        'condicion'
    ];

    public function bitacora()
    {
        return $this->belongsTo(Bitacora::class);
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class);
    }
}
