<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissions extends Seeder
{
    /**
     * Run the database seeds.
     */
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles básicos
        $roles = [
            'superadmin',
            'administrador',
            'profesor',
            'soporte',
            'director'
        ];

        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role]);
        }

        // Crear permisos específicos para el director (solo lectura)
        $permissions = [
            'view_usuarios',
            'view_bitacoras',
            'view_eventos',
            'view_reportes',
            'view_instituciones',
            'view_profesores',
            'view_secciones',
            'view_dashboard',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // Asignar permisos de solo vista al director
        $director = \Spatie\Permission\Models\Role::where('name', 'director')->first();
        if ($director) {
            $director->syncPermissions($permissions);
        }

        // El administrador tiene todos los permisos
        $admin = \Spatie\Permission\Models\Role::where('name', 'administrador')->first();
        if ($admin) {
            // Asignar todos los permisos existentes al administrador
            $admin->syncPermissions(Permission::all());
        }

        $superadmin = \Spatie\Permission\Models\Role::where('name', 'superadmin')->first();
        if($superadmin) {
            $superadmin->syncPermissions(Permission::all());
        };
    }
}