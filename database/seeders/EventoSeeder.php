<?php

namespace Database\Seeders;

use App\Models\Evento;
use App\Models\User;
use App\Models\Bitacora;
use Illuminate\Database\Seeder;

class EventoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos usuarios y bitácoras existentes para usar en los eventos
        $users = User::all();
        $bitacoras = Bitacora::all();

        // Crear algunos eventos de ejemplo
        $eventos = [
            [
                'id_bitacora' => $bitacoras->random()->id,
                'user_id' => $users->random()->id,
                'fecha' => now(),
                'observacion' => 'Problema con el equipo de cómputo',
                'prioridad' => 'Alta',
                'confirmacion' => true,
                'condicion' => 1
            ],
            [
                'id_bitacora' => $bitacoras->random()->id,
                'user_id' => $users->random()->id,
                'fecha' => now()->subHours(2),
                'observacion' => 'Mantenimiento rutinario completado',
                'prioridad' => 'Normal',
                'confirmacion' => true,
                'condicion' => 1
            ],
            [
                'id_bitacora' => $bitacoras->random()->id,
                'user_id' => $users->random()->id,
                'fecha' => now()->subDays(1),
                'observacion' => 'Revisión de instalaciones eléctricas',
                'prioridad' => 'Baja',
                'confirmacion' => true,
                'condicion' => 1
            ],
            [
                'id_bitacora' => $bitacoras->random()->id,
                'user_id' => $users->random()->id,
                'fecha' => now()->subHours(5),
                'observacion' => 'Reporte de fallo en proyector',
                'prioridad' => 'Alta',
                'confirmacion' => false,
                'condicion' => 1
            ]
        ];

        // Insertar los eventos en la base de datos
        foreach ($eventos as $evento) {
            Evento::create($evento);
        }
    }
}
