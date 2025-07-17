<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profesor extends Model
{
    use HasFactory;

    protected $table = 'profesor';
    public $timestamps = true;

    protected $fillable = ['usuario_id'];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
    
    public function especialidades()
    {
        return $this->belongsToMany(Especialidade::class);
    }

    public function horarios()
    {
        return $this->belongsToMany(Horario::class);
    }
    public function eventos()
    {
        return $this->belongsToMany(Evento::class);
    }
    
}