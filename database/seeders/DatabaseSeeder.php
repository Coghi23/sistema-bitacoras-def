<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar seeders en orden específico
        $this->call([
            RolePermissions::class,          // Primero crear roles y permisos
            AdminUserSeeder::class,
            LeccioneSeeder::class, // Crear lecciones
            //BitacoraSeeder::class,
            //
            // EventoSeeder::class, // Luego crear eventos que dependen de bitácoras
        ]);

        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
