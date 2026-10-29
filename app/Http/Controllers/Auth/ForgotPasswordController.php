<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request) {
        // Validar la solicitud
        $request->validate(['email' => 'required|email']);

        // Mismo mensaje exista o no el correo, para no revelar qué cuentas están registradas
        $mensajeEnviado = 'Si el correo está registrado, recibirás un enlace de restablecimiento.';

        // Verificar si el usuario existe y está activo
        $user = User::where('email', $request->email)->where('estatus', 1)->first();

        if (!$user) {
            return redirect()->route('password.request.custom')->with('status', $mensajeEnviado);
        }

        // Generar un token de restablecimiento
        $token = Str::random(60);

        // Sólo un enlace vigente por correo; el token se guarda con hash
        DB::table('password_resets')->where('email', $request->email)->delete();
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Generar la URL de restablecimiento
        $url = url('password/reset', $token) . '?email=' . urlencode($request->email);

        try {
            // Enviar el correo
            Mail::send('auth.passwords.email_reset_link', [
                'url' => $url
            ], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Restablecer Contraseña');
            });

            return redirect()->route('password.request.custom')
                            ->with('status', $mensajeEnviado);
        } catch (\Exception $e) {
            return redirect()->route('password.request.custom')
                            ->withErrors(['email' => 'No se pudo enviar el correo. Por favor, intenta más tarde.']);
        }
    }

}
