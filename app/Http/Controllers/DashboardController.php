<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Lógica para roles específicos existentes
        if ($user->hasRole('administrador')) {
            return view('Dashboard.indexAdmin');
        } elseif ($user->hasRole('director')) {
            return view('Dashboard.indexAdmin'); // Usar admin dashboard como base
        } elseif ($user->hasRole('superadmin')) {
            return view('Dashboard.indexAdmin'); // Usar admin dashboard como base
        } elseif ($user->hasRole('profesor')) {
            return view('Dashboard.indexDocente');
        } elseif ($user->hasRole('soporte')) {
            return view('Dashboard.indexSoporte');
        }
        
        // Para roles personalizados, crear dashboard dinámico basado en permisos
        return $this->dashboardGenerico($user);
    }
    
    private function dashboardGenerico($user)
    {
        // Obtener todos los permisos del usuario
        $permisos = $user->getAllPermissions();
        
        // Organizar permisos por categorías basándose en los nombres de los permisos
        $categoriasPermisos = [];
        
        foreach ($permisos as $permiso) {
            $nombrePermiso = $permiso->name;
            
            // Extraer la categoría basándose en el patrón del nombre del permiso
            if (preg_match('/^(view|create|edit|delete)_(.+)$/', $nombrePermiso, $matches)) {
                $accion = $matches[1];
                $modulo = $matches[2];
                
                if (!isset($categoriasPermisos[$modulo])) {
                    $categoriasPermisos[$modulo] = [];
                }
                $categoriasPermisos[$modulo][$accion] = true;
            }
        }
        
        // Definir las rutas y títulos amigables para cada módulo
        $modulosConfig = [
            'especialidad' => [
                'titulo' => 'Especialidades',
                'icono' => 'bi-mortarboard',
                'ruta' => 'especialidad.index',
                'descripcion' => 'Gestionar especialidades académicas'
            ],
            'seccion' => [
                'titulo' => 'Secciones',
                'icono' => 'bi-collection',
                'ruta' => 'seccion.index',
                'descripcion' => 'Administrar secciones'
            ],
            'institucion' => [
                'titulo' => 'Instituciones',
                'icono' => 'bi-building',
                'ruta' => 'institucion.index',
                'descripcion' => 'Gestionar instituciones'
            ],
            'usuario' => [
                'titulo' => 'Usuarios',
                'icono' => 'bi-people',
                'ruta' => 'usuario.index',
                'descripcion' => 'Administrar usuarios del sistema'
            ],
            'bitacora' => [
                'titulo' => 'Bitácoras',
                'icono' => 'bi-journal-text',
                'ruta' => 'bitacora.index',
                'descripcion' => 'Registros y seguimiento'
            ],
            'profesor' => [
                'titulo' => 'Profesores',
                'icono' => 'bi-person-badge',
                'ruta' => 'profesor.index',
                'descripcion' => 'Gestionar profesores'
            ],
            'recinto' => [
                'titulo' => 'Recintos',
                'icono' => 'bi-geo-alt',
                'ruta' => 'recinto.index',
                'descripcion' => 'Administrar recintos'
            ]
        ];
        
        // Filtrar solo los módulos a los que el usuario tiene acceso
        $modulosAccesibles = [];
        foreach ($categoriasPermisos as $modulo => $acciones) {
            if (isset($modulosConfig[$modulo])) {
                $modulosAccesibles[$modulo] = array_merge(
                    $modulosConfig[$modulo],
                    ['permisos' => $acciones]
                );
            }
        }
        
        return view('Dashboard.generic', [
            'user' => $user,
            'modulos' => $modulosAccesibles,
            'roles' => $user->roles->pluck('name')->toArray()
        ]);
    }
}
