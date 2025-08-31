<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Bitacora extends Model
{
    protected $table = 'bitacora';
   
   
    protected $fillable = [
        'id_recinto',
        'id_llave',
        'condicion',
        'estado'
    ];


    public function recinto()
    {
   
        return $this->belongsTo(Recinto::class, 'id_recinto');
   
    }




    public function llave()
    {
        return $this->belongsTo(Llave::class, 'id_llave');
    }


    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }


    // Método para obtener solo profesores
    public function profesor()
    {
        return $this->belongsTo(User::class, 'id_usuario')->whereHas('roles', function($query) {
            $query->where('name', 'profesor');
        });
    }


    public function evento(){


        return $this->hasMany(Evento::class, 'id_bitacora');


    }
}
