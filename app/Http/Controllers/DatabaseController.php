<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseController extends Controller
{
    // Exportar la base de datos
    public function export()
    {
        $backupFileName = 'backup-' . date('Y-m-d_H-i-s') . '.sql';
        $filePath = storage_path('app/' . $backupFileName);

        $command = sprintf(
            '"%s" --user=%s --password=%s --host=%s %s > "%s"',
            env('MYSQLDUMP_PATH', 'mysqldump'), // Usa el path definido o el comando por defecto
            env('DB_USERNAME'),
            env('DB_PASSWORD'),
            env('DB_HOST'),
            env('DB_DATABASE'),
            $filePath
        );

        // Ejecutar el comando y capturar el resultado
        $result = shell_exec($command);

        if (!file_exists($filePath)) {
            return back()->with('error', 'No se pudo exportar la base de datos. Verifica las credenciales y permisos.');
        }

        return response()->download($filePath)->deleteFileAfterSend(true);
    }


    // Importar la base de datos
    public function import(Request $request)
    {
        $request->validate([
            'database_file' => 'required|file',
        ]);

        $file = $request->file('database_file');
        $filePath = $file->getRealPath();

        $command = sprintf(
            '"%s" --user=%s --password=%s --host=%s %s < "%s"',
            env('MYSQL_PATH', 'mysql'),
            env('DB_USERNAME'),
            env('DB_PASSWORD'),
            env('DB_HOST'),
            env('DB_DATABASE'),
            $filePath
        );

        // Ejecutar el comando
        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return back()->with('error', 'No se pudo importar la base de datos. Asegúrese de que el archivo SQL sea válido.');
        }

        return back()->with('success', 'Base de datos importada correctamente.');
    }
}
