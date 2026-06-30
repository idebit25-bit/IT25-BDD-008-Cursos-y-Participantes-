<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\CourseActionLog;
use Illuminate\Support\Facades\Auth;
use App\Models\Cursos;
use App\Models\Setting;
use App\Models\ParticipantActionLog;

class ConfigController extends Controller
{
    public function index()
    {

        // Obtener la conexión de base de datos actual
        $cursos = Cursos::all(); // Obtener todos los cursos
        $participantLogs = ParticipantActionLog::all(); // Historial de participantes
        $currentDbConnection = Config::get('database.default');
        $setting = Setting::first();
        $currentLogo = $setting && $setting->logo ? asset('storage/' . $setting->logo) : asset('images/default-logo.png');


        // Obtener todos los registros de logs con relaciones
        $logs = CourseActionLog::with(['curso', 'user'])
            ->orderBy('fecha_accion', 'desc')
            ->take(10)
            ->get();

        // Pasar los logs a la vista
        return view('configuraciones.index', compact('logs', 'currentDbConnection', 'currentLogo','cursos', 'participantLogs'));
    }

     public function Cursos_Acciones()
    {

        // Obtener la conexión de base de datos actual
        $cursos = Cursos::all(); // Obtener todos los cursos
        $participantLogs = ParticipantActionLog::all(); // Historial de participantes
        $currentDbConnection = Config::get('database.default');
        $setting = Setting::first();
        $currentLogo = $setting && $setting->logo ? asset('storage/' . $setting->logo) : asset('images/default-logo.png');


        // Obtener todos los registros de logs con relaciones
        $logs = CourseActionLog::with(['curso', 'user'])
            ->orderBy('fecha_accion', 'desc')
            ->get();

        // Pasar los logs a la vista
        return view('/configuraciones/logs', compact('logs', 'currentDbConnection', 'currentLogo','cursos', 'participantLogs'));
    }

    public function store(Request $request)
    {
        try {
            // Validar los datos del formulario
            $validated = $request->validate([
                'nombre_configuracion' => 'required|string|max:255',
                'valor_configuracion' => 'required|string|max:255',
            ]);

            // Guardar los datos (aquí puedes agregar la lógica para guardar en la base de datos)
            // Ejemplo: Configuracion::create($validated);

            // Redirigir con mensaje de éxito
            return redirect()->route('configuraciones.index')
                ->with('success', 'Configuración guardada exitosamente');
        } catch (\Exception $e) {
            // Redirigir con mensaje de error en caso de excepción
            return back()
                ->with('error', 'Error al guardar la configuración: ' . $e->getMessage());
        }
    }


    private function updateEnv($data = [])
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            $str = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $str);
        }

        file_put_contents($envFile, $str);
    }


    public function exportCursos()
    {
        // Obtener los datos de la tabla "cursos"
        $cursos = DB::table('cursos')->get();

        // Crear una respuesta de flujo para descargar el archivo SQL
        return response()->streamDownload(function () use ($cursos) {
            $output = fopen('php://output', 'w');

            // Escribir el encabezado del archivo SQL
            fwrite($output, "-- Exportación de la tabla 'cursos'\n");
            fwrite($output, "-- Fecha: " . now() . "\n\n");

            // Generar los comandos INSERT
            foreach ($cursos as $curso) {
                $columns = implode("`, `", array_keys((array) $curso));
                $values = implode("', '", array_map(fn($value) => addslashes($value), (array) $curso));
                fwrite($output, "INSERT INTO `cursos` (`{$columns}`) VALUES ('{$values}');\n");
            }

            fclose($output);
        }, 'export_cursos.sql');
    }

    public function exportParticipantes()
    {
        // Obtener los datos de la tabla "participantes"
        $participantes = DB::table('participantes')->get();

        // Crear una respuesta de flujo para descargar el archivo SQL
        return response()->streamDownload(function () use ($participantes) {
            $output = fopen('php://output', 'w');

            // Escribir el encabezado del archivo SQL
            fwrite($output, "-- Exportación de la tabla 'participantes'\n");
            fwrite($output, "-- Fecha: " . now() . "\n\n");

            // Generar los comandos INSERT
            foreach ($participantes as $participante) {
                $columns = implode("`, `", array_keys((array) $participante));
                $values = implode("', '", array_map(fn($value) => addslashes($value), (array) $participante));
                fwrite($output, "INSERT INTO `participantes` (`{$columns}`) VALUES ('{$values}');\n");
            }

            fclose($output);
        }, 'export_participantes.sql');
    }

    public function openInVsCode()
    {
        // Obtener la ruta base del proyecto
        $projectPath = base_path();

        // Comando para abrir VS Code con la carpeta del proyecto
        $command = "code " . escapeshellarg($projectPath);

        // Ejecutar el comando en el sistema operativo
        try {
            exec($command);
            return redirect()->back()->with('success', 'Visual Studio Code se ha abierto con la carpeta del proyecto.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se pudo abrir Visual Studio Code: ' . $e->getMessage());
        }
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validar que sea una imagen válida
        ]);

        // Subir el archivo al directorio de almacenamiento
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');

            // Guardar la ruta del logo en la base de datos
            $setting = \App\Models\Setting::firstOrNew(['id' => 1]);
            $setting->logo = $path;
            $setting->save();

            return redirect()->back()->with('success', 'Logo actualizado correctamente.');
        }

        return redirect()->back()->with('error', 'No se pudo actualizar el logo.');
    }

    public function updateLogoFromList(Request $request)
    {
        $request->validate([
            'selected_logo' => 'required|string', // Validar que se haya seleccionado un logo
        ]);

        // Obtener el nombre del logo seleccionado
        $selectedLogo = $request->input('selected_logo');

        // Construir la ruta relativa dentro de storage/app/public
        $logoPath = 'logos/' . $selectedLogo;

        // Verificar que el archivo exista en la carpeta de logos
        if (!file_exists(storage_path('app/public/' . $logoPath))) {
            return redirect()->back()->with('error', 'El logo seleccionado no existe.');
        }

        // Guardar la ruta del logo en la base de datos
        $setting = \App\Models\Setting::firstOrNew(['id' => 1]);
        $setting->logo = $logoPath; // Guardar la ruta relativa
        $setting->save();

        return redirect()->back()->with('success', 'Logo actualizado correctamente.');
    }

    public function mostrarLogs()
    {
        // Obtener todos los registros de logs con relaciones
        $logs = CourseActionLog::with(['curso', 'user'])->get();

        // Pasar los logs a la vista
        return view('configuraciones.show-log.blade', compact('logs'));
    }

    /**
     * Mostrar los detalles de un log específico.
     */
    public function mostrarDetallesLog($id)
    {
        // Buscar el registro por ID
        $log = CourseActionLog::with(['curso', 'user'])->findOrFail($id);

        // Pasar el log a la vista
        return view('configuraciones.show-log.blade', compact('log'));
    }

            /**
             * Activar un curso eliminado.
             */

        public function activarCurso($id)
        {
            try {
                // Buscar el curso por ID
                $curso = Cursos::findOrFail($id);

                // Actualizar el campo 'status' a 1 (activo)
                $curso->update(['status' => 1]);

                // Redirigir con mensaje de éxito
                return redirect()->route('configuraciones.index')
                    ->with('success', 'Curso activado exitosamente');
            } catch (\Exception $e) {
                // Redirigir con mensaje de error en caso de excepción
                return back()
                    ->with('error', 'Error al activar el curso: ' . $e->getMessage());
            }
        }

        /**
         * Eliminar definitivamente un curso.
         */
        public function eliminarDefinitivo($id)
        {
            try {
                // Buscar el curso por ID
                $curso = Cursos::findOrFail($id);

                // Eliminar el curso
                $curso->delete();

                // Redirigir con mensaje de éxito
                return redirect()->route('configuraciones.index')
                    ->with('success', 'Curso eliminado definitivamente');
            } catch (\Exception $e) {
                // Redirigir con mensaje de error en caso de excepción
                return back()
                    ->with('error', 'Error al eliminar el curso: ' . $e->getMessage());
            }
        }

        /**
         * Mostrar detalles de un registro de log.
         */
        public function showLog($id)
        {
            // Buscar el registro por ID
            $log = CourseActionLog::with(['curso', 'user'])->findOrFail($id);

            // Pasar el log a la vista principal de configuraciones
            return view('configuraciones.show-log', compact('log'));
        }

            public function show($id)
            {
                // Buscar el curso por ID
                $curso = Cursos::findOrFail($id);

                // Pasar el curso a la vista
                return view('cursos.show', compact('curso'));
            }


            public function desactivar($id)
{
    $curso = Cursos::findOrFail($id);
    $curso->status = 0;  // Cambiar a desactivado
    $curso->save();

    // Opcional: registrar acción en logs (como 'Eliminado')
    CourseActionLog::create([
        'curso_id' => $curso->id,
        'nombre_curso' => $curso->NombredelCurso,
        'user_id' => Auth::id(),
        'accion' => 'Eliminado',
        'detalles' => 'Curso desactivado por el usuario.',
        'fecha_accion' => now(),
    ]);

    return redirect()->back()->with('success', 'Curso desactivado correctamente.');
}




}
