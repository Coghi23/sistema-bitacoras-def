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

    /**
     * Obtener los recintos asociados al profesor a través de sus horarios
     */
    public function recintos()
    {
        return $this->hasManyThrough(
            Recinto::class,
            Horario::class,
            'user_id',      // Foreign key en horarios (relaciona con users)
            'id',           // Foreign key en recintos 
            'usuario_id',   // Local key en profesor (relaciona con users)
            'idRecinto'     // Local key en horarios (relaciona con recintos)
        )->distinct();
    }

    /**
     * Obtener horarios del profesor de hoy
     */
    public function horariosHoy()
    {
        $hoy = now()->format('Y-m-d');
        $diaSemana = now()->locale('es')->dayName;
        
        return $this->hasMany(Horario::class, 'user_id', 'usuario_id')
                    ->where(function($query) use ($hoy, $diaSemana) {
                        $query->where('fecha', $hoy)
                              ->orWhere('dia', $diaSemana);
                    })
                    ->where('condicion', 1);
    }

    /**
     * Obtener recintos del profesor para hoy
     */
    public function recintosHoy()
    {
        return $this->hasManyThrough(
            Recinto::class,
            Horario::class,
            'user_id',
            'id',
            'usuario_id',
            'idRecinto'
        )->whereIn('horarios.id', $this->horariosHoy()->pluck('id'))
         ->distinct();
    }
    
}