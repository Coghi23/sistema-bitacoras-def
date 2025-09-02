<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Recinto extends Model
{
    use HasFactory;


    protected $table = 'recinto';  


    protected $fillable = [
        'llave_id',
        'nombre',
        'tiporecinto_id',
        'estadorecinto_id',
        'condicion',
    ];


    public function llave()
    {
        return $this->belongsTo(Llave::class);
    }


    public function tipoRecinto()
    {
        return $this->belongsTo(TipoRecinto::class, 'tiporecinto_id');
    }


    public function estadoRecinto()
    {
        return $this->belongsTo(EstadoRecinto::class, 'estadorecinto_id');
    }


    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class, 'id_recinto');
    }


    public function bitacoraActiva()
    {
        return $this->hasOne(Bitacora::class, 'id_recinto')
                   ->where('condicion', 1);
    }


    /**
     * Boot method para eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();
       
        // Crear bitácora automáticamente cuando se crea un recinto
        static::created(function ($recinto) {
            \Log::info('Creando bitácora para recinto: ' . $recinto->id);
           
            Bitacora::create([
                'id_recinto' => $recinto->id,
                'id_llave' => $recinto->llave_id,
                'condicion' => 1,
            ]);
           
            \Log::info('Bitácora creada exitosamente para recinto: ' . $recinto->id);
        });
    }
}
