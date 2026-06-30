<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Google\Client;
use Google\Service\Drive;
use Masbug\Flysystem\GoogleDriveAdapter;
use League\Flysystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
