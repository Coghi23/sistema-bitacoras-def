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
        // Crear superadministrador inborrable con id 1
        $superAdmin = User::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Super Administrador',
                'email' => 'superadmin@sistema.com',
                'cedula' => '00000001',
                'password' => Hash::make('superadmin123'),
                'condicion' => true,
            ]
        );
        $superAdmin->assignRole('superadmin');

        // Crear usuario administrador normal si no existe
        $admin = User::firstOrCreate(
            ['email' => 'admin@sistema.com'],
            [
                'name' => 'Administrador',
                'cedula' => '12345678',
                'password' => Hash::make('admin123'),
                'condicion' => true,
            ]
        );
        $admin->assignRole('administrador');
    }
}
