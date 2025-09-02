<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class SidebarComposer
{
    public function compose(View $view)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Si es superadmin, mostrar todo el sidebar
            $isSuperAdmin = $user->hasRole('superadmin');
            
            // Obtener permisos del usuario
            $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
            
            // Definir elementos del sidebar con sus permisos requeridos
            $sidebarItems = [
                'inicio' => [
                    'show' => true, // Siempre visible para usuarios autenticados
                    'route' => route('dashboard'),
                    'icon' => 'bi-house-door-fill',
                    'label' => 'Inicio'
                ],
                'roles' => [
                    'show' => $isSuperAdmin || $this->hasAnyPermission($userPermissions, ['view_roles']),
                    'icon' => 'bi-shield-lock',
                    'label' => 'Roles y permisos',
                    'subitems' => [
                        'roles' => [
                            'show' => $isSuperAdmin || $this->hasPermission($userPermissions, 'view_roles'),
                            'route' => asset('role'),
                            'icon' => 'bi-bank',
                            'label' => 'Roles'
                        ],
                        'permisos' => [
                            'show' => $isSuperAdmin || $this->hasPermission($userPermissions, 'view_roles'), // Usar el mismo permiso
                            'route' => asset('permisos'),
                            'icon' => 'bi-journal-bookmark',
                            'label' => 'Permisos'
                        ]
                    ]
                ],
                'usuarios' => [
                    'show' => $isSuperAdmin || $this->hasPermission($userPermissions, 'view_usuarios'),
                    'route' => asset('usuario'),
                    'icon' => 'bi-person',
                    'label' => 'Usuarios'
                ],
                'otras_opciones' => [
                    'show' => $isSuperAdmin || $this->hasAnyPermission($userPermissions, ['view_institucion', 'view_especialidad', 'view_seccion', 'view_subarea']),
                    'icon' => 'bi-clipboard-fill',
                    'label' => 'Otras opciones',
                    'subitems' => [
                        'instituciones' => [
                            'show' => $isSuperAdmin || $this->hasPermission($userPermissions, 'view_institucion'),
                            'route' => asset('institucion'),
                            'icon' => 'bi-bank',
                            'label' => 'Institución'
                        ],
                        'especialidades' => [
                            'show' => $isSuperAdmin || $this->hasPermission($userPermissions, 'view_especialidad'),
                            'route' => asset('especialidad'),
                            'icon' => 'bi-journal-bookmark',
                            'label' => 'Especialidad'
                        ],
                        'secciones' => [
                            'show' => $isSuperAdmin || $this->hasPermission($userPermissions, 'view_seccion'),
                            'route' => asset('seccion'),
                            'icon' => 'bi-diagram-3',
                            'label' => 'Sección'
                        ],
                        'subareas' => [
                            'show' => $isSuperAdmin || $this->hasPermission($userPermissions, 'view_subarea'),
                            'route' => asset('subarea'),
                            'icon' => 'bi-diagram-2',
                            'label' => 'SubÁrea'
                        ]
                    ]
                ],
                'llaves' => [
                    'show' => $isSuperAdmin || $this->hasPermission($userPermissions, 'view_llaves'),
                    'route' => asset('llave'),
                    'icon' => 'bi-key-fill',
                    'label' => 'Llaves'
                ],
                'recintos' => [
                    'show' => $isSuperAdmin || $this->hasAnyPermission($userPermissions, ['view_tipo_recinto', 'view_estado_recinto', 'view_recintos']),
                    'icon' => 'bi-building-fill',
                    'label' => 'Recintos',
                    'subitems' => [
                        'tipo_recintos' => [
                            'show' => $isSuperAdmin || $this->hasPermission($userPermissions, 'view_tipo_recinto'),
                            'route' => asset('tipoRecinto'),
                            'icon' => 'bi-building-fill-gear',
                            'label' => 'Tipo de Recinto'
                        ],
                        'estado_recintos' => [
                            'show' => $isSuperAdmin || $this->hasPermission($userPermissions, 'view_estado_recinto'),
                            'route' => asset('estadoRecinto'),
                            'icon' => 'bi-building-fill-exclamation',
                            'label' => 'Estado de Recinto'
                        ],
                        'recintos' => [
                            'show' => $isSuperAdmin || $this->hasPermission($userPermissions, 'view_recintos'),
                            'route' => asset('recinto'),
                            'icon' => 'bi-building-fill-add',
                            'label' => 'Crear Recintos'
                        ]
                    ]
                ],
                'horarios' => [
                    'show' => $isSuperAdmin || $this->hasPermission($userPermissions, 'view_horario'),
                    'route' => asset('horario'),
                    'icon' => 'bi-calendar-week-fill',
                    'label' => 'Horarios'
                ],
                'qr_temporales' => [
                    'show' => $isSuperAdmin || $this->hasPermission($userPermissions, 'view_qr_temporales'),
                    'route' => route('admin.qr.index'),
                    'icon' => 'bi-qr-code-scan',
                    'label' => 'QR Temporales'
                ],
                'bitacora' => [
                    'show' => $isSuperAdmin || $this->hasPermission($userPermissions, 'view_bitacoras'),
                    'route' => route('bitacora.index'),
                    'icon' => 'bi-calendar-week-fill',
                    'label' => 'Bitácora'
                ],
                'reportes' => [
                    'show' => $isSuperAdmin || $this->hasPermission($userPermissions, 'view_reportes'),
                    'route' => route('evento.index'),
                    'icon' => 'bi-file-earmark-bar-graph-fill',
                    'label' => 'Reportes'
                ]
            ];
            
            // Determinar la ruta del dashboard
            $dashboardRoute = route('dashboard');
            
            $view->with([
                'sidebarItems' => $sidebarItems,
                'dashboardRoute' => $dashboardRoute
            ]);
        }
    }
    
    private function hasPermission($userPermissions, $permission)
    {
        return in_array($permission, $userPermissions);
    }
    
    private function hasAnyPermission($userPermissions, $permissions)
    {
        foreach ($permissions as $permission) {
            if (in_array($permission, $userPermissions)) {
                return true;
            }
        }
        return false;
    }
}
