<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfesorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario profesor por defecto
        $profesor = User::firstOrCreate(
            ['email' => 'profesor@sistema.com'],
            [
                'name' => 'Profesor de Prueba',
                'cedula' => '87654321',
                'password' => Hash::make('profesor123'),
                'condicion' => true,
            ]
        );

        // Asignar rol de profesor
        $profesor->assignRole('profesor');
    }
}
