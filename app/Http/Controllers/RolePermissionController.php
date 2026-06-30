<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RolePermissionController extends Controller
{
    /**
     * Verificar permisos del usuario según su rol
     *
     * @return \Illuminate\Http\Response
     */
    public function getPermissions()
    {
        $user = Auth::user();
        $permissions = $this->getRolePermissions($user->puesto);

        return response()->json($permissions);
    }

    /**
     * Middleware para verificar si el usuario tiene permiso para cierta acción
     *
     * @param string $permission
     * @return \Closure
     */
    public function checkPermission($permission)
    {
        return function (Request $request, $next) use ($permission) {
            $user = Auth::user();
            $permissions = $this->getRolePermissions($user->puesto);

            if (!$permissions[$permission]) {
                if ($request->ajax()) {
                    return response()->json([
                        'error' => 'No tienes permiso para realizar esta acción'
                    ], 403);
                }

                return redirect()->back()->with('error', 'No tienes permiso para realizar esta acción');
            }

            return $next($request);
        };
    }

    /**
     * Obtiene permisos basados en el rol del usuario
     *
     * @param string $role
     * @return array
     */
    private function getRolePermissions($role)
    {
        $permissions = [
            'Programador' => [
                'puedeCrear' => true,
                'puedeEditar' => true,
                'puedeEliminar' => true,
                'puedeVerReportes' => true
            ],
            'Administrador' => [
                'puedeCrear' => true,
                'puedeEditar' => true,
                'puedeEliminar' => true,
                'puedeVerReportes' => true
            ],
            'Mantenimiento' => [
                'puedeCrear' => true,
                'puedeEditar' => true,
                'puedeEliminar' => false,
                'puedeVerReportes' => true
            ],
            'Operacion' => [
                'puedeCrear' => false,
                'puedeEditar' => false,
                'puedeEliminar' => false,
                'puedeVerReportes' => true
            ]
        ];

        return $permissions[$role] ?? [
            'puedeCrear' => false,
            'puedeEditar' => false,
            'puedeEliminar' => false,
            'puedeVerReportes' => false
        ];
    }

    /**
     * Verifica permisos para una vista específica
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkViewPermissions(Request $request)
    {
        $view = $request->input('view');
        $user = Auth::user();
        $permissions = $this->getRolePermissions($user->puesto);

        $viewPermissions = [
            'create' => $permissions['puedeCrear'],
            'edit' => $permissions['puedeEditar'],
            'delete' => $permissions['puedeEliminar'],
            'reports' => $permissions['puedeVerReportes']
        ];

        return response()->json([
            'view' => $view,
            'permissions' => $viewPermissions
        ]);
    }
}
