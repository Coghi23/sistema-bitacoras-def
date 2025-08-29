<?php

namespace Database\Seeders;

use App\Models\Bitacora;
use App\Models\Recinto;
use App\Models\Seccione;
use App\Models\Subarea;
use App\Models\Horario;
use App\Models\Leccion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class BitacoraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si existen datos en las tablas necesarias
        if (Recinto::count() == 0 || Seccione::count() == 0 || 
            Subarea::count() == 0 || Horario::count() == 0) {
            echo "Por favor, asegúrate de que existan datos en las tablas relacionadas antes de ejecutar BitacoraSeeder.\n";
            return;
        }

        // Obtener datos necesarios de otras tablas
        $recintos = Recinto::all();
        $secciones = Seccione::all();
        $subareas = Subarea::all();
        $horarios = Horario::all();

        // Crear algunas bitácoras de ejemplo
        $bitacoras = [
            [
                'id_recinto' => $recintos->random()->id,
                'id_seccion' => $secciones->random()->id,
                'id_subarea' => $subareas->random()->id,
                'id_horario' => $horarios->random()->id,
                'fecha' => Carbon::now(),
                'hora_envio' => Carbon::now()->format('H:i:s'),
                'condicion' => 1
            ],
            [
                'id_recinto' => $recintos->random()->id,
                'id_seccion' => $secciones->random()->id,
                'id_subarea' => $subareas->random()->id,
                'id_horario' => $horarios->random()->id,
                'fecha' => Carbon::now()->subDays(1),
                'hora_envio' => '08:00:00',
                'condicion' => 1
            ],
            [
                'id_recinto' => $recintos->random()->id,
                'id_seccion' => $secciones->random()->id,
                'id_subarea' => $subareas->random()->id,
                'id_horario' => $horarios->random()->id,
                'fecha' => Carbon::now()->subDays(2),
                'hora_envio' => '14:30:00',
                'condicion' => 1
            ],
            [
                'id_recinto' => $recintos->random()->id,
                'id_seccion' => $secciones->random()->id,
                'id_subarea' => $subareas->random()->id,
                'id_horario' => $horarios->random()->id,
                'fecha' => Carbon::now()->subDays(3),
                'hora_envio' => '10:15:00',
                'condicion' => 1
            ],
            [
                'id_recinto' => $recintos->random()->id,
                'id_seccion' => $secciones->random()->id,
                'id_subarea' => $subareas->random()->id,
                'id_horario' => $horarios->random()->id,
                'fecha' => Carbon::now()->subDays(4),
                'hora_envio' => '16:45:00',
                'condicion' => 1
            ]
        ];

        // Insertar las bitácoras en la base de datos
        foreach ($bitacoras as $bitacora) {
            // Crear la bitácora
            $newBitacora = Bitacora::create($bitacora);

            // Opcionalmente, crear la relación horario_leccion si es necesaria
            if ($horario = Horario::find($bitacora['id_horario'])) {
                if ($leccion = Leccion::inRandomOrder()->first()) {
                    DB::table('horario_leccion')->insert([
                        'idHorario' => $horario->id,
                        'idLeccion' => $leccion->id,
                        'condicion' => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }
}
