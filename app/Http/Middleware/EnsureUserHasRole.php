<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasRole
{
    // Uso en rutas: ->middleware('role:Programador,Administrador')
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->puesto, $roles, true)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'No tienes permiso para realizar esta acción.'], 403);
            }

            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return $next($request);
    }
}
