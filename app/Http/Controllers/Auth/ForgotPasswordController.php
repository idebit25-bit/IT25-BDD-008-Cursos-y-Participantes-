<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request) {
        // Validar la solicitud
        $request->validate(['email' => 'required|email']);

        // Verificar si el usuario existe
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->route('password.request')
                            ->withErrors(['email' => 'No encontramos un usuario con esa dirección de correo.']);
        }

        // Generar un token de restablecimiento
        $token = Str::random(60);

        // Guardar el token en la base de datos
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
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

            return redirect()->route('password.request')
                            ->with('status', 'Se ha enviado un enlace de restablecimiento a tu correo.');
        } catch (\Exception $e) {
            return redirect()->route('password.request')
                            ->withErrors(['email' => 'No se pudo enviar el correo. Por favor, intenta más tarde.']);
        }
    }

}
