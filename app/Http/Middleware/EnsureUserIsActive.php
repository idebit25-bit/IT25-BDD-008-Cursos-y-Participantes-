<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    // Cierra la sesión de cuentas enviadas a la papelera (estatus = 0)
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && (int) $user->estatus !== 1) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Tu cuenta está desactivada.'], 403);
            }

            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta está desactivada. Contacta a un administrador.',
            ]);
        }

        return $next($request);
    }
}
