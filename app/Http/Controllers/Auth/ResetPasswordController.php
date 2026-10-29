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
    private const MINUTOS_VIGENCIA_TOKEN = 60;

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

        // Verificar el token (guardado con hash) y su vigencia
        $resetRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        $tokenValido = $resetRecord
            && Hash::check($request->token, $resetRecord->token)
            && Carbon::parse($resetRecord->created_at)->addMinutes(self::MINUTOS_VIGENCIA_TOKEN)->isFuture();

        $user = $tokenValido ? User::where('email', $request->email)->where('estatus', 1)->first() : null;

        if (!$user) {
            $mensaje = 'Este enlace de restablecimiento es inválido o ya caducó. Solicita uno nuevo.';
            if ($request->expectsJson()) {
                return response()->json(['errors' => ['email' => [$mensaje]]], 422);
            }
            return back()->withErrors(['email' => $mensaje]);
        }

        // Actualizar la contraseña
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
        if ($request->expectsJson()) {
            return response()->json(['status' => 'Tu contraseña ha sido restablecida con éxito.']);
        }
        return redirect()->route('login')->with('status', 'Tu contraseña ha sido restablecida con éxito.');
    }
    protected function redirectTo()
    {
        return route('login');
    }
}
