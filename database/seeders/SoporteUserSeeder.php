<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SoporteUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario soporte por defecto
        $soporte = User::firstOrCreate(
            ['email' => 'soporte@sistema.com'],
            [
                'name' => 'Soporte Técnico',
                'cedula' => '11223344',
                'password' => Hash::make('soporte123'),
                'condicion' => true,
            ]
        );

        // Asignar rol de soporte
        $soporte->assignRole('soporte');
    }
}
