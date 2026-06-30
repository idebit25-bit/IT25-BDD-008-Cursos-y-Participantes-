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

        // Verificar si el correo existe en la base de datos
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Si el correo no existe
            return back()->withErrors([
                'email' => 'El correo electrónico no está registrado.',
            ])->withInput($request->only('email'));
        }

        // Intentar autenticar al usuario
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('Inicio');
        }

        // Si la contraseña es incorrecta
        return back()->withErrors([
            'password' => 'La contraseña es incorrecta.',
        ])->withInput($request->only('email'));
    }

}
