<?php

namespace App\Services;

use App\Models\Cursos;
use App\Models\Setting;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Log;

/**
 * Crea y mantiene en Google Drive una carpeta por curso:
 * carpeta base del instituto → curso inicial → subcurso.
 *
 * Si Drive no está configurado o falla, el curso se guarda igual y el error queda en el log.
 */
class DriveCarpetas
{
    private const TIPO_CARPETA = 'application/vnd.google-apps.folder';

    private ?Drive $servicio = null;

    // Acepta la URL de una carpeta (…/folders/<id>, …?id=<id>) o el ID directamente
    public static function extraerId(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }
        if (preg_match('#/folders/([A-Za-z0-9_-]{10,})#', $url, $m) || preg_match('#[?&]id=([A-Za-z0-9_-]{10,})#', $url, $m)) {
            return $m[1];
        }
        return preg_match('#^[A-Za-z0-9_-]{10,}$#', $url) ? $url : null;
    }

    public static function urlCarpeta(string $id): string
    {
        return 'https://drive.google.com/drive/folders/' . $id;
    }

    public function carpetaBaseUrl(): ?string
    {
        return Setting::first()?->drive_carpeta_url ?: null;
    }

    public function carpetaBaseId(): ?string
    {
        return self::extraerId($this->carpetaBaseUrl()) ?? (config('services.google_drive.carpeta_id') ?: null);
    }

    public function credencialesConfiguradas(): bool
    {
        $config = config('services.google_drive');
        return (!empty($config['service_account']) && is_file($config['service_account']))
            || (!empty($config['client_id']) && !empty($config['client_secret']) && !empty($config['refresh_token']));
    }

    public function configurado(): bool
    {
        return $this->credencialesConfiguradas() && $this->carpetaBaseId() !== null;
    }

    // Correo de la cuenta de servicio, con el que hay que compartir la carpeta base
    public function correoCuentaServicio(): ?string
    {
        $ruta = config('services.google_drive.service_account');
        if (!$ruta || !is_file($ruta)) {
            return null;
        }
        return json_decode((string) file_get_contents($ruta), true)['client_email'] ?? null;
    }

    public static function nombreCarpeta(Cursos $curso): string
    {
        $nombre = trim($curso->nomenclatura . ' - ' . $curso->nombre, ' -');
        // Drive admite casi cualquier carácter, pero se quitan los que estorban al sincronizar con Windows
        return trim(preg_replace('#[\x00-\x1F\x5C/:*?"<>|]+#', ' ', $nombre));
    }

    /**
     * Crea la carpeta del curso si no existe o la renombra si cambió la nomenclatura o el nombre.
     * Devuelve la URL de la carpeta, o null si no se pudo.
     */
    public function asegurarCarpetaCurso(Cursos $curso): ?string
    {
        // Los subcursos recién iniciados tienen una nomenclatura temporal hasta guardar el paso 1
        if (!$this->configurado() || str_starts_with((string) $curso->nomenclatura, 'TEMP-')) {
            return null;
        }

        try {
            // Un subcurso va dentro de la carpeta de su curso inicial; sin ella no se crea suelto en la base
            $padreId = $this->carpetaBaseId();
            if ($curso->parent_id && $curso->cursoPadre) {
                if (!$this->asegurarCarpetaCurso($curso->cursoPadre)) {
                    return null;
                }
                $padreId = $curso->cursoPadre->drive_carpeta_id;
            }

            $nombre = self::nombreCarpeta($curso);
            $carpeta = $curso->drive_carpeta_id ? $this->buscarCarpeta($curso->drive_carpeta_id) : null;

            if (!$carpeta) {
                $carpeta = $this->drive()->files->create(new DriveFile([
                    'name' => $nombre,
                    'mimeType' => self::TIPO_CARPETA,
                    'parents' => [$padreId],
                ]), ['fields' => 'id, name', 'supportsAllDrives' => true]);
            } elseif ($carpeta->getName() !== $nombre) {
                $this->drive()->files->update($carpeta->getId(), new DriveFile(['name' => $nombre]), ['supportsAllDrives' => true]);
            }

            $url = self::urlCarpeta($carpeta->getId());
            if ($curso->drive_carpeta_id !== $carpeta->getId() || $curso->drive_carpeta_url !== $url) {
                $curso->forceFill(['drive_carpeta_id' => $carpeta->getId(), 'drive_carpeta_url' => $url])->saveQuietly();
            }

            return $url;
        } catch (\Throwable $e) {
            Log::error("No se pudo crear la carpeta de Drive del curso {$curso->id}: " . $e->getMessage());
            return null;
        }
    }

    // Comprueba que la carpeta guardada siga existiendo (no se haya borrado a mano en Drive)
    private function buscarCarpeta(string $id): ?DriveFile
    {
        try {
            $carpeta = $this->drive()->files->get($id, ['fields' => 'id, name, trashed', 'supportsAllDrives' => true]);
            return $carpeta->getTrashed() ? null : $carpeta;
        } catch (\Google\Service\Exception $e) {
            if ($e->getCode() === 404) {
                return null;
            }
            throw $e;
        }
    }

    private function drive(): Drive
    {
        if ($this->servicio) {
            return $this->servicio;
        }

        $config = config('services.google_drive');
        $client = new Client();
        $client->setScopes([Drive::DRIVE]);

        if (!empty($config['service_account']) && is_file($config['service_account'])) {
            $client->setAuthConfig($config['service_account']);
        } else {
            $client->setClientId($config['client_id']);
            $client->setClientSecret($config['client_secret']);
            $token = $client->fetchAccessTokenWithRefreshToken($config['refresh_token']);
            if (isset($token['error'])) {
                throw new \RuntimeException('Google rechazó el token: ' . ($token['error_description'] ?? $token['error']));
            }
        }

        return $this->servicio = new Drive($client);
    }
}
