<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Roles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $director = Role::create(['name' => 'director']);
        $profesor = Role::create(['name' => 'profesor']);
        $soporte = Role::create(['name' => 'soporte']);
        $administrador = Role::create(['name' => 'administrador']);
    
        // Definir permisos
        $permisos = [
            //general
            'ver dashboard',

            //secciones
            'ver seccion',
            'crear seccion',
            'editar seccion',
            'eliminar seccion',

            //subareas
            'ver subarea',
            'crear subarea',
            'editar subarea',
            'eliminar subarea',
            
            //especialidades
            'ver especialidad',
            'crear especialidad',
            'editar especialidad',
            'eliminar especialidad',
        ];

        foreach ($permisos as $permiso) {
            Permission::create(['name' => $permiso]);
        }

        // Asignar permisos a roles
        $director->givePermissionTo([
            'ver dashboard',
            'ver seccion',
            'ver subarea',
            'ver especialidad'
        ]);

        $soporte->givePermissionTo([
            'ver dashboard'
        ]);

        $profesor->givePermissionTo([
            'ver dashboard',
            'ver seccion',
            'ver subarea',
            'ver especialidad'
        ]);

        $administrador->givePermissionTo(Permission::all());
    }
}
?>