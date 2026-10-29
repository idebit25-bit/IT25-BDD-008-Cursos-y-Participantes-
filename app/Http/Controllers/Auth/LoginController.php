<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    public function login(Request $request)
    {
        // Validar los campos del formulario
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Intentar autenticar al usuario; sólo las cuentas activas (estatus = 1) pueden entrar
        if (Auth::attempt($credentials + ['estatus' => 1])) {
            $request->session()->regenerate();
            return redirect()->intended('Inicio');
        }

        // Mismo mensaje para correo inexistente, contraseña incorrecta o cuenta desactivada,
        // para no revelar qué correos están registrados
        return back()->withErrors([
            'email' => 'Las credenciales no son válidas o la cuenta está desactivada.',
        ])->withInput($request->only('email'));
    }

}
