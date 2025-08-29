<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador por defecto
        $admin = User::firstOrCreate(
            ['email' => 'admin@sistema.com'],
            [
                'name' => 'Administrador',
                'cedula' => '12345678',
                'password' => Hash::make('admin123'),
                'condicion' => true,
            ]
        );

        // Asignar rol de administrador
        $admin->assignRole('administrador');

        // Crear usuario administrador por defecto
        $profe = User::firstOrCreate(
            ['email' => 'profe@sistema.com'],
            [
                'name' => 'Profesor',
                'cedula' => '12345678',
                'password' => Hash::make('profe123'),
                'condicion' => true,
            ]
        );

        // Asignar rol de administrador
        $admin->assignRole('profesor');
    }
}
