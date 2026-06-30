<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RutaCursosController extends Controller
{
    public function guardar(Request $request)
    {
        $request->validate([
            'nombreCarpeta' => 'required|string',
            'rutaCarpeta' => 'required|string',
        ]);

        // Guardar la configuración en la base de datos o en un archivo
        // Ejemplo: Guardar en un archivo JSON
        $config = [
            'nombreCarpeta' => $request->nombreCarpeta,
            'rutaCarpeta' => $request->rutaCarpeta,
        ];

        Storage::put('ruta_cursos.json', json_encode($config));

        return response()->json(['message' => 'Ruta guardada correctamente.']);
    }

}
