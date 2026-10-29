<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Google\Client;
use Google\Service\Drive;
use Masbug\Flysystem\GoogleDriveAdapter;
use League\Flysystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Fortify y Jetstream vienen como dependencias, pero el sistema usa su propio
        // login y recuperación de contraseña (routes/web.php). Sus rutas automáticas
        // (/register, /reset-password, /user/...) quedaban expuestas sin los controles del proyecto.
        \Laravel\Fortify\Fortify::ignoreRoutes();
        \Laravel\Jetstream\Jetstream::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Máximo 5 intentos de inicio de sesión por minuto para cada correo + IP
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by(Str::lower((string) $request->input('email')) . '|' . $request->ip())
                ->response(function () {
                    return back()->withErrors([
                        'email' => 'Demasiados intentos de inicio de sesión. Espera un minuto e inténtalo de nuevo.',
                    ])->withInput(request()->only('email'));
                });
        });

        try {
            Storage::extend('google', function($app, $config) {
                $client = new Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                
                try {
                    $token = $client->fetchAccessTokenWithRefreshToken($config['refreshToken']);
                    if (isset($token['error'])) {
                        // Si hay error, lo lanzamos para que se vea en la consola
                        throw new \Exception('Google Auth Error: ' . ($token['error_description'] ?? $token['error']));
                    }
                    $client->setAccessToken($token);
                } catch (\Exception $e) {
                    throw new \Exception('Falla en autenticación de Google: ' . $e->getMessage());
                }
                
                $service = new Drive($client);
                $adapter = new GoogleDriveAdapter($service, $config['folderId']);
                $driver = new Filesystem($adapter);

                return new FilesystemAdapter($driver, $adapter);
            });
        } catch (\Exception $e) {
            // Silenciosamente fallar si no hay configuración
        }
    }
}
