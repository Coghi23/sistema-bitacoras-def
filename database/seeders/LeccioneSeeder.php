<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Leccion;


class LeccioneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Leccion::insert([
            //lecciones academicas
            [
                'leccion' => 'Lección 1',
                'hora_inicio' => '07:00',
                'hora_final' => '07:40',
            ],
            [
                'leccion' => 'Lección 2',
                'hora_inicio' => '07:40',
                'hora_final' => '08:20',
            ],
            [
                'leccion' => 'Lección 3',
                'hora_inicio' => '08:20',
                'hora_final' => '09:00',
            ],
            [
                'leccion' => 'Lección 4',
                'hora_inicio' => '09:20',
                'hora_final' => '10:00',
            ],
            [
                'leccion' => 'Lección 5',
                'hora_inicio' => '10:00',
                'hora_final' => '10:40',
            ],
            [
                'leccion' => 'Lección 6',
                'hora_inicio' => '10:40',
                'hora_final' => '11:20',
            ],
            [
                'leccion' => 'Lección 7',
                'hora_inicio' => '12:05',
                'hora_final' => '12:45',
            ],
            [
                'leccion' => 'Lección 8',
                'hora_inicio' => '12:45',
                'hora_final' => '01:25',
            ],
            [
                'leccion' => 'Lección 9',
                'hora_inicio' => '01:25',
                'hora_final' => '02:05',
            ],
            [
                'leccion' => 'Lección 10',
                'hora_inicio' => '02:20',
                'hora_final' => '03:00',
            ],
            [
                'leccion' => 'Lección 11',
                'hora_inicio' => '03:00',
                'hora_final' => '03:40',
            ],
            [
                'leccion' => 'Lección 12',
                'hora_inicio' => '03:40',
                'hora_final' => '04:20',
            ],

            //lecciones tecnicas
            [
                'leccion' => 'Lección Técnica 1',
                'hora_inicio' => '07:00',
                'hora_final' => '08:00',
            ],
            [
                'leccion' => 'Lección Técnica 2',
                'hora_inicio' => '08:00',
                'hora_final' => '09:00',
            ],
            [
                'leccion' => 'Lección Técnica 3',
                'hora_inicio' => '09:20',
                'hora_final' => '10:20',
            ],
            [
                'leccion' => 'Lección Técnica 4',
                'hora_inicio' => '10:20',
                'hora_final' => '11:20',
            ],
            [
                'leccion' => 'Lección Técnica 5',
                'hora_inicio' => '12:05',
                'hora_final' => '01:05',
            ],
            [
                'leccion' => 'Lección Técnica 6',
                'hora_inicio' => '01:05',
                'hora_final' => '02:05',
            ],
            [
                'leccion' => 'Lección Técnica 7',
                'hora_inicio' => '02:20',
                'hora_final' => '03:20',
            ],
            [
                'leccion' => 'Lección Técnica 8',
                'hora_inicio' => '03:20',
                'hora_final' => '04:20',
            ],
            
            
        ]);
    }
}
