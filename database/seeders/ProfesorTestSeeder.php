<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profesor;
use App\Models\Horario;
use App\Models\Recinto;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ProfesorTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear rol profesor si no existe
        $rolProfesor = Role::firstOrCreate(['name' => 'profesor']);

        // Crear usuario profesor de prueba
        $user = User::firstOrCreate(
            ['email' => 'profesor@test.com'],
            [
                'name' => 'Profesor de Prueba',
                'cedula' => '987654321',
                'password' => Hash::make('password123'),
                'condicion' => true,
            ]
        );

        // Asignar rol
        $user->assignRole('profesor');

        // Crear registro de profesor
        $profesor = Profesor::firstOrCreate(
            ['usuario_id' => $user->id],
            ['usuario_id' => $user->id]
        );

        // Obtener un recinto existente
        $recinto = Recinto::first();
        
        if ($recinto) {
            // Crear horario de prueba para el profesor
            Horario::firstOrCreate([
                'user_id' => $user->id,
                'idRecinto' => $recinto->id,
                'dia' => 'lunes',
                'tipoHorario' => true,
                'idSubarea' => 1, // Asume que existe
                'idSeccion' => 1, // Asume que existe
                'condicion' => 1
            ]);

            echo "✅ Profesor de prueba creado:\n";
            echo "📧 Email: profesor@test.com\n";
            echo "🔑 Password: password123\n";
            echo "🏢 Recinto asignado: {$recinto->nombre}\n";
        } else {
            echo "❌ No hay recintos disponibles para asignar al profesor\n";
        }
    }
}
