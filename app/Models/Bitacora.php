<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacora';
    
    // Estados de la bitácora
    const ESTADO_INACTIVO = 0;
    const ESTADO_ACTIVA = 1;
    
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
            case self::ESTADO_INACTIVO:
                return 'Entregada';
            case self::ESTADO_ACTIVA:
                return 'Sin Entregar';
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
            case self::ESTADO_INACTIVO:
                return 'bg-secondary';
            case self::ESTADO_ACTIVA:
                return 'bg-warning';
            default:
                return 'bg-dark';
        }
    }

    /**
     * Verificar si la bitácora está pendiente
     */
    public function estaPendiente()
    {
        return $this->estado === self::ESTADO_INACTIVO;
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
        return $this->estado === self::ESTADO_INACTIVO;
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