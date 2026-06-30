<?php
namespace App\Http\Controllers\Auth;

use  Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordChangedAdminNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(['token' => $token, 'email' => $request->email]);
    }

    public function reset(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'token' => 'required',
        ]);

        // Verificar el token
        $resetRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Este enlace de restablecimiento es inválido.']);
        }

        // Actualizar la contraseña
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Disparar el evento PasswordReset
        event(new PasswordReset($user));

        // Obtener la IP y la hora actual
        $ip = $request->ip();
        $time = Carbon::now()->setTimezone('America/Mexico_City')->toDateTimeString(); // Fecha y hora en tu zona horaria

        // Enviar el correo al administrador
        $adminEmail = '2123200418@soy.utj.edu.mx'; // Reemplaza con el correo del administrador
        Mail::to($adminEmail)->send(new PasswordChangedAdminNotification($user, $time, $ip));

        // Eliminar el registro del token
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Redirigir al usuario con un mensaje de éxito
        return redirect()->route('login')->with('status', 'Tu contraseña ha sido restablecida con éxito.');
    }
    protected function redirectTo()
    {
        return route('login');
    }
}
