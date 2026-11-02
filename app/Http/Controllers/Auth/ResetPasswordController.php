<?php
namespace App\Http\Controllers\Auth;

use  Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordChangedAdminNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    // Vigencia del enlace de restablecimiento (también se muestra en el correo)
    public const MINUTOS_VIGENCIA_TOKEN = 5;

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(['token' => $token, 'email' => $request->email]);
    }

    public function reset(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'email' => 'required|email',
            'password' => array_merge(['required'], User::reglasContrasena()),
            'token' => 'required',
        ], User::mensajesContrasena() + [
            'token.required' => 'El enlace de restablecimiento no es válido. Solicita uno nuevo.',
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

        if (Hash::check($request->password, $user->password)) {
            $mensaje = 'La nueva contraseña debe ser distinta a la actual.';
            if ($request->expectsJson()) {
                return response()->json(['errors' => ['password' => [$mensaje]]], 422);
            }
            return back()->withErrors(['password' => $mensaje]);
        }

        // Actualizar la contraseña y cerrar cualquier sesión abierta con la anterior
        $user->password = Hash::make($request->password);
        $user->cerrarSesiones();
        $user->save();

        // El enlace se invalida antes de enviar correos, que pueden tardar
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Disparar el evento PasswordReset
        event(new PasswordReset($user));

        // Obtener la IP y la hora actual
        $ip = $request->ip();
        $time = Carbon::now()->setTimezone('America/Mexico_City')->toDateTimeString(); // Fecha y hora en tu zona horaria

        // Avisar al administrador; si el correo falla, la contraseña ya quedó cambiada
        $adminEmail = config('mail.admin_address');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new PasswordChangedAdminNotification($user, $time, $ip));
            } catch (\Throwable $e) {
                Log::error('No se pudo enviar el aviso de cambio de contraseña: ' . $e->getMessage());
            }
        }

        // Avisar también al dueño de la cuenta, por si no fue quien la cambió
        try {
            Mail::send('emails.password_changed_user', ['user' => $user, 'time' => $time], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Tu contraseña fue cambiada - Gestor Instituto I-DEB');
            });
        } catch (\Throwable $e) {
            Log::error('No se pudo avisar al usuario del cambio de contraseña: ' . $e->getMessage());
        }

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
