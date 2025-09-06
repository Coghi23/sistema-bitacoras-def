<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Especialidade extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'especialidad';

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'id_institucion',
        'nombre',
        'condicion'
    ];

    // Relación con Institución (única foreign key directa en la tabla)
    public function instituciones(){
        return $this->belongsToMany(Institucione::class, 'especialidad_institucion', 'especialidad_id', 'institucion_id')
            ->withPivot('condicion')
            ->withTimestamps();
    }

    // Relación many-to-many con Profesor (tabla intermedia: especialidadprofesor)
    public function profesores()
    {
        return $this->belongsToMany(Profesor::class, 'especialidadprofesor');
    }

    // Relación many-to-many con Seccion (tabla intermedia: especialidad_seccion)
    public function secciones()
    {
        return $this->belongsToMany(Seccione::class, 'especialidad_seccion', 'especialidad_id', 'seccion_id')
                    ->withPivot('condicion')
                    ->withTimestamps();
    }
}
