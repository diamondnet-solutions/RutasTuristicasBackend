<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    /**
     * Obtiene el menú dinámico basado en los permisos del usuario autenticado
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMenu(Request $request)
    {
        // Obtener el usuario actual
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data' => []
            ], 401);
        }

        // Obtener todos los permisos del usuario (incluyendo los heredados a través de roles)
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();

        // Verificar si el usuario administra emprendimientos
        $administraEmprendimientos = $user->administraEmprendimientos();

        // Definir la estructura del menú completo
        $fullMenu = $this->getFullMenuStructure($administraEmprendimientos);

        // Filtrar el menú según los permisos del usuario
        $filteredMenu = $this->filterMenuByPermissions($fullMenu, $permissions);

        return response()->json([
            'success' => true,
            'data' => $filteredMenu
        ]);
    }

    /**
     * Define la estructura completa del menú
     *
     * @param bool $incluyeMenuEmprendedor Si es true, incluye opciones específicas para emprendedores
     * @return array
     */
    private function getFullMenuStructure($incluyeMenuEmprendedor = false)
    {
        $menu = [
            [
                'id' => 'dashboard',
                'title' => 'Dashboard',
                'icon' => 'dashboard',
                'path' => '/dashboard',
                'permissions' => ['user_read'], // Permisos mínimos para ver el dashboard
            ],
            [
                'id' => 'users',
                'title' => 'Usuarios',
                'icon' => 'users',
                'path' => '/admin/users',
                'permissions' => ['user_read'],
                'children' => [
                    [
                        'id' => 'user-list',
                        'title' => 'Gestión de Usuarios',
                        'path' => '/admin/users',
                        'permissions' => ['user_read'],
                    ],
                    [
                        'id' => 'roles',
                        'title' => 'Roles',
                        'path' => '/admin/roles',
                        'permissions' => ['role_read'],
                    ],
                    [
                        'id' => 'permissions',
                        'title' => 'Permisos',
                        'path' => '/admin/permissions',
                        'permissions' => ['permission_read'],
                    ],
                ]
            ],
            [
                'id' => 'municipalidad',
                'title' => 'Municipalidad',
                'icon' => 'building',
                'path' => '/admin/municipalidad',
                'permissions' => ['municipalidad_read'],
            ],
            [
                'id' => 'emprendedores',
                'title' => 'Emprendedores',
                'icon' => 'store',
                'path' => '/admin/emprendedores',
                'permissions' => ['emprendedor_read'],
                'children' => [
                    [
                        'id' => 'emprendedor-list',
                        'title' => 'Gestión de Emprendedores',
                        'path' => '/admin/emprendedores',
                        'permissions' => ['emprendedor_read'],
                    ],
                    [
                        'id' => 'asociacion-list',
                        'title' => 'Gestión de Asociaciones',
                        'path' => '/admin/asociaciones',
                        'permissions' => ['asociacion_read'],
                    ],
                ]
            ],
            [
                'id' => 'lugares_turisticos',
                'title' => 'Lugares Turísticos',
                'icon' => 'map-marker',
                'path' => '/admin/lugares-turisticos',
                'permissions' => ['lugar_turistico_read'],
                'children' => [
                    [
                        'id' => 'lugar-turistico-list',
                        'title' => 'Gestión de Lugares',
                        'path' => '/admin/lugares-turisticos',
                        'permissions' => ['lugar_turistico_read'],
                    ],
                    [
                        'id' => 'tipo-lugar',
                        'title' => 'Tipos de Lugares',
                        'path' => '/admin/lugares-turisticos/tipos',
                        'permissions' => ['lugar_turistico_tipo_read'],
                    ],
                    [
                        'id' => 'rutas-turisticas',
                        'title' => 'Rutas Turísticas',
                        'path' => '/admin/lugares-turisticos/rutas',
                        'permissions' => ['lugar_turistico_ruta_read'],
                    ],
                ]
            ],
            [
                'id' => 'servicios',
                'title' => 'Servicios',
                'icon' => 'briefcase',
                'path' => '/admin/servicios',
                'permissions' => ['servicio_read'],
                'children' => [
                    [
                        'id' => 'servicio-list',
                        'title' => 'Gestión de Servicios',
                        'path' => '/admin/servicios',
                        'permissions' => ['servicio_read'],
                    ],
                    [
                        'id' => 'categorias',
                        'title' => 'Categorías',
                        'path' => '/admin/categorias',
                        'permissions' => ['categoria_read'],
                    ],
                ]
            ],
            [
                'id' => 'reservas',
                'title' => 'Reservas',
                'icon' => 'calendar',
                'path' => '/admin/reservas',
                'permissions' => ['user_read'], // Asumiendo que cualquier usuario puede ver reservas
                'children' => [
                    [
                        'id' => 'reserva-list',
                        'title' => 'Lista de Reservas',
                        'path' => '/admin/reservas',
                        'permissions' => ['user_read'],
                    ],
                    [
                        'id' => 'reserva-create',
                        'title' => 'Crear Reserva',
                        'path' => '/admin/reservas/create',
                        'permissions' => ['user_read'],
                    ],
                ]
            ],
            [
                'id' => 'reportes',
                'title' => 'reportes',
                'icon' => 'chart-bar',
                'path' => '/admin/reportes',
                'permissions' => ['reporte_read'],
                'children' => [
                    [
                        'id' => 'reporte-emprendedores',
                        'title' => 'Reporte de Emprendedores',
                        'path' => '/admin/reportes/emprendedores',
                        'permissions' => ['reporte_read'],
                    ],
                    [
                        'id' => 'reporte-asociaciones',
                        'title' => 'Reporte de Asociaciones',
                        'path' => '/admin/reportes/asociaciones',
                        'permissions' => ['reporte_read'],
                    ],
                    [
                        'id' => 'reporte-lugares',
                        'title' => 'Lugares Turísticos',
                        'path' => '/admin/reportes/lugares',
                        'permissions' => ['reporte_read'],
                    ],
                    [
                        'id' => 'reporte-reservas',
                        'title' => 'Reservas y Ventas',
                        'path' => '/admin/reportes/reservas',
                        'permissions' => ['reporte_read'],
                    ],
                    [
                        'id' => 'reporte-exportar',
                        'title' => 'Exportar Datos',
                        'path' => '/admin/reportes/exportar',
                        'permissions' => ['reporte_exportar']
                    ],

                ]
            ],
            [
                'id' => 'profile',
                'title' => 'Mi Perfil',
                'icon' => 'user',
                'path' => '/admin/profile',
                'permissions' => ['user_read'], // Todos los usuarios pueden ver su perfil
            ],
        ];

        // Si el usuario administra emprendimientos, añadir esas opciones al menú
        if ($incluyeMenuEmprendedor) {
            $menuEmprendedor = [
                'id' => 'mis-emprendimientos',
                'title' => 'Mis Emprendimientos',
                'icon' => 'shop',
                'path' => '/admin/mis-emprendimientos',
                'permissions' => ['user_read'], // Todos los usuarios pueden acceder a sus emprendimientos
                'children' => [
                    [
                        'id' => 'emprendimiento-list',
                        'title' => 'Lista de Emprendimientos',
                        'path' => '/admin/mis-emprendimientos',
                        'permissions' => ['user_read'],
                    ],
                    [
                        'id' => 'emprendimiento-servicios',
                        'title' => 'Mis Servicios',
                        'path' => '/admin/mis-emprendimientos/servicios',
                        'permissions' => ['user_read', 'servicio_read'],
                    ],
                    [
                        'id' => 'emprendimiento-reservas',
                        'title' => 'Mis Reservas',
                        'path' => '/admin/mis-emprendimientos/reservas',
                        'permissions' => ['user_read'],
                    ],
                    [
                        'id' => 'emprendimiento-estadisticas',
                        'title' => 'Estadísticas',
                        'path' => '/admin/mis-emprendimientos/estadisticas',
                        'permissions' => ['user_read'],
                    ],
                    [
                        'id' => 'emprendimiento-administradores',
                        'title' => 'Administradores',
                        'path' => '/admin/mis-emprendimientos/administradores',
                        'permissions' => ['user_read'],
                    ],
                ]
            ];

            // Insertar después del dashboard
            array_splice($menu, 1, 0, [$menuEmprendedor]);
        }

        return $menu;
    }

    /**
     * Filtra el menú según los permisos del usuario
     *
     * @param array $menu
     * @param array $userPermissions
     * @return array
     */
    private function filterMenuByPermissions($menu, $userPermissions)
    {
        $filteredMenu = [];

        foreach ($menu as $item) {
            // Verificar si el usuario tiene al menos uno de los permisos requeridos para este elemento
            $hasPermission = count(array_intersect($item['permissions'], $userPermissions)) > 0;

            // Si tiene permiso, procesar este ítem del menú
            if ($hasPermission) {
                $menuItem = [
                    'id' => $item['id'],
                    'title' => $item['title'],
                    'icon' => $item['icon'],
                    'path' => $item['path'],
                ];

                // Si tiene hijos, filtrarlos también
                if (isset($item['children']) && !empty($item['children'])) {
                    $filteredChildren = [];

                    foreach ($item['children'] as $child) {
                        $hasChildPermission = count(array_intersect($child['permissions'], $userPermissions)) > 0;

                        if ($hasChildPermission) {
                            $filteredChildren[] = [
                                'id' => $child['id'],
                                'title' => $child['title'],
                                'path' => $child['path'],
                            ];
                        }
                    }

                    // Solo añadir children si hay elementos
                    if (!empty($filteredChildren)) {
                        $menuItem['children'] = $filteredChildren;
                    }
                }

                $filteredMenu[] = $menuItem;
            }
        }

        return $filteredMenu;
    }
}
