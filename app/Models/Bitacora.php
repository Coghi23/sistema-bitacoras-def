<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacora';
    
    // Estados de la bitácora
    const ESTADO_PENDIENTE = 0;
    const ESTADO_ACTIVA = 1;
    const ESTADO_COMPLETADA = 2;
    
    protected $fillable = [
        'id_recinto',
        'id_llave',
        'estado',
        'condicion',
    ];

    public function recinto()
    {
        return $this->belongsTo(Recinto::class, 'id_recinto');
    }

    public function llave()
    {
        return $this->belongsTo(Llave::class, 'id_llave');
    }

    public function evento(){
        return $this->hasMany(Evento::class, 'id_bitacora');
    }

    /**
     * Obtener el estado de la bitácora en formato legible
     */
    public function getEstadoTextoAttribute()
    {
        switch ($this->estado) {
            case self::ESTADO_PENDIENTE:
                return 'Pendiente';
            case self::ESTADO_ACTIVA:
                return 'En Uso';
            case self::ESTADO_COMPLETADA:
                return 'Completada';
            default:
                return 'Desconocido';
        }
    }

    /**
     * Obtener la clase CSS para el badge del estado
     */
    public function getEstadoBadgeClassAttribute()
    {
        switch ($this->estado) {
            case self::ESTADO_PENDIENTE:
                return 'bg-secondary';
            case self::ESTADO_ACTIVA:
                return 'bg-warning';
            case self::ESTADO_COMPLETADA:
                return 'bg-success';
            default:
                return 'bg-dark';
        }
    }

    /**
     * Verificar si la bitácora está pendiente
     */
    public function estaPendiente()
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    /**
     * Verificar si la bitácora está activa
     */
    public function estaActiva()
    {
        return $this->estado === self::ESTADO_ACTIVA;
    }

    /**
     * Verificar si la bitácora está completada
     */
    public function estaCompletada()
    {
        return $this->estado === self::ESTADO_COMPLETADA;
    }

    /**
     * Activar bitácora (cuando se escanea la llave para usar)
     */
    public function activar()
    {
        $this->estado = self::ESTADO_ACTIVA;
        $this->save();
    }

    /**
     * Completar bitácora (cuando se escanea la llave para entregar)
     */
    public function completar()
    {
        $this->estado = self::ESTADO_COMPLETADA;
        $this->save();
    }

    /**
     * Resetear bitácora a pendiente
     */
    public function resetear()
    {
        $this->estado = self::ESTADO_PENDIENTE;
        $this->save();
    }
}
