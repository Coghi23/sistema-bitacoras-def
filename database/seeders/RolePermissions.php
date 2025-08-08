<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissions extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'administrador']);

            $permisos = [
                // permisos de vista
                'ver-dashboard',
                'ver-usuarios',
                'ver-roles',
                'ver-permisos',

                // permisos de creación
                'crear-usuarios',
                'crear-roles',
                'crear-permisos',

                // permisos de edición
                'editar-usuarios',
                'editar-roles',
                'editar-permisos',

                // permisos de eliminación
                'eliminar-usuarios',
                'eliminar-roles',
                'eliminar-permisos',
            ];

            foreach ($permisos as $permiso) {
                Permission::firstOrCreate(['name' => $permiso,
                    'guard_name' => 'web'
                ]);
            }

            $adminRole->syncPermissions(Permission::all());
    }
    }


}
