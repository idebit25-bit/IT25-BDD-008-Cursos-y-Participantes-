<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\RutaLocal;

class   RutaArchivosController extends Controller
{
    /**
     * Guarda una nueva ruta en la base de datos.
     */
    public function guardarRuta(Request $request)
    {
        try {
            // Validar la solicitud
            $request->validate([
                'nombreCarpeta' => 'required|string',
                'rutaCarpeta' => 'required|string',
            ]);

            // Limpiar y normalizar la ruta
            $rutaCarpeta = rtrim($request->rutaCarpeta, '/\\');
            $nombreCarpeta = trim($request->nombreCarpeta);
            $rutaCompleta = $rutaCarpeta . DIRECTORY_SEPARATOR . $nombreCarpeta;

            // Verificar si la ruta base existe
            if (!File::exists($rutaCarpeta)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La ruta base especificada no existe.'
                ], 400);
            }

            // Crear un nuevo registro en la base de datos
            $nuevaRuta = RutaLocal::create([
                'nombre_carpeta' => $nombreCarpeta,
                'ruta_nombre_carpeta' => $rutaCarpeta,
                'rutacompleta' => $rutaCompleta,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('ruta.archivos')->with('success', 'Carpeta creada y ruta guardada correctamente.');

        } catch (\Exception $e) {
            return redirect()->route('ruta.archivos')->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene la última ruta guardada en la base de datos.
     */
    public function obtenerRuta()
    {
        try {
            // Obtener la última ruta registrada
            $ultimaRuta = RutaLocal::orderBy('created_at', 'desc')->first();

            if (!$ultimaRuta) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay configuración guardada.'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'nombreCarpeta' => $ultimaRuta->nombre_carpeta,
                    'rutaCarpeta' => $ultimaRuta->ruta_nombre_carpeta,
                    'rutaCompleta' => $ultimaRuta->rutacompleta,
                    'timestamp' => $ultimaRuta->created_at,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifica si una ruta existe en el sistema.
     */
    public function verificarRuta(Request $request)
    {
        try {
            $ruta = $request->query('ruta');
            $existe = File::exists($ruta);

            return response()->json([
                'exists' => $existe,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exists' => false,
                'message' => 'Error al verificar la ruta: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Abre una carpeta específica en el sistema.
     */
    public function abrirCarpeta(Request $request)
    {
        try {
            // Obtener la ruta desde la solicitud
            $ruta = $request->input('ruta');

            // Validar que la ruta no esté vacía
            if (empty($ruta)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La ruta no puede estar vacía.'
                ], 400);
            }

            // Construir el comando para ejecutar el script Python
            $scriptPath = storage_path('app/public/abrir_carpeta.py');
            $comando = escapeshellcmd("python \"{$scriptPath}\" \"$ruta\"");

            // Ejecutar el comando
            exec($comando, $output, $return_var);

            // Verificar si el comando se ejecutó correctamente
            if ($return_var === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Carpeta abierta exitosamente.',
                    'output' => $output
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al abrir la carpeta.',
                    'output' => $output,
                    'return_var' => $return_var
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Obtiene la última ruta completa guardada en la base de datos.
     */
    public function obtenerUltimaRuta()
    {
        try {
            // Obtener la última ruta registrada
            $ultimaRuta = RutaLocal::orderBy('created_at', 'desc')->first();

            if (!$ultimaRuta) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay configuración guardada.'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $ultimaRuta->rutacompleta
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene todas las rutas guardadas en la base de datos.
     */
    public function obtenerTodasLasRutas()
    {
        try {
            // Obtener todas las rutas ordenadas por fecha de creación más reciente
            $rutas = RutaLocal::orderBy('created_at', 'desc')->get();

            // Verificar si hay rutas
            if ($rutas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay rutas configuradas.'
                ]);
            }

            // Transformar los datos para enviar
            $rutasFormateadas = $rutas->map(function($ruta) {
                return [
                    'nombre_carpeta' => $ruta->nombre_carpeta,
                    'rutacompleta' => $ruta->rutacompleta,
                    'ruta_nombre_carpeta' => $ruta->ruta_nombre_carpeta,
                    'created_at' => $ruta->created_at
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $rutasFormateadas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las rutas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function crearCarpeta(Request $request)
    {
        $request->validate(['tipo' => 'required|string']);
        $ultimaRuta = RutaLocal::latest()->firstOrFail();
        $rutaBase = $ultimaRuta->rutacompleta;

        // Crear carpeta específica para el tipo (ej: Instagram)
        $carpeta = ucfirst($request->tipo);
        $rutaCompleta = "$rutaBase/7- Flyers del Curso/$carpeta";

        if (File::exists($rutaCompleta)) {
            return response()->json([
                'success' => false,
                'message' => 'La carpeta ya existe.'
            ]);
        }

        File::makeDirectory($rutaCompleta, 0755, true);
        $ultimaRuta->update(["ruta$carpeta" => $rutaCompleta]);

        return response()->json(['success' => true]);
    }

}
