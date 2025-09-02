<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Definir permisos para cada módulo
        $modulos = [
            'especialidad' => ['view', 'create', 'edit', 'delete'],
            'seccion' => ['view', 'create', 'edit', 'delete'],
            'institucion' => ['view', 'create', 'edit', 'delete'],
            'usuario' => ['view', 'create', 'edit', 'delete'],
            'bitacora' => ['view', 'create', 'edit', 'delete'],
            'profesor' => ['view', 'create', 'edit', 'delete'],
            'recinto' => ['view', 'create', 'edit', 'delete'],
        ];

        // Crear permisos
        foreach ($modulos as $modulo => $acciones) {
            foreach ($acciones as $accion) {
                $nombrePermiso = $accion . '_' . $modulo;
                Permission::firstOrCreate([
                    'name' => $nombrePermiso,
                    'guard_name' => 'web'
                ]);
                echo "Creado/Verificado permiso: $nombrePermiso\n";
            }
        }

        // Crear roles básicos si no existen
        $rolesBasicos = ['administrador', 'director', 'superadmin', 'profesor', 'soporte'];
        foreach ($rolesBasicos as $rolNombre) {
            $rol = Role::firstOrCreate([
                'name' => $rolNombre,
                'guard_name' => 'web'
            ]);
            echo "Creado/Verificado rol: $rolNombre\n";
        }

        // Asignar todos los permisos al rol administrador
        $adminRole = Role::where('name', 'administrador')->first();
        if ($adminRole) {
            $adminRole->syncPermissions(Permission::all());
            echo "Permisos asignados al administrador\n";
        }

        echo "Seeder de permisos completado exitosamente.\n";
    }
}
