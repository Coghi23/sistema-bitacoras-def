<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EstadoEvento extends Model
{
    use HasFactory;

    protected $fillable = [
        'observacion',
        'fecha',
        'estado'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}
