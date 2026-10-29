<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchivosController extends Controller
{
    // Tipos de archivo que se pueden subir (se excluyen ejecutables, HTML, SVG y scripts)
    public const EXTENSIONES_PERMITIDAS = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv',
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mp3', 'zip', 'rar',
    ];

    // Construye una ruta dentro de mi_carpeta; rechaza '..', rutas absolutas y caracteres de control
    private function rutaSegura(?string ...$partes): string
    {
        $segmentos = ['mi_carpeta'];
        foreach ($partes as $parte) {
            foreach (preg_split('#[/\x5C]+#', (string) $parte, -1, PREG_SPLIT_NO_EMPTY) as $segmento) {
                if ($segmento === '.' || $segmento === '..' || preg_match('/[\x00-\x1F:]/', $segmento)) {
                    abort(400, 'Ruta no válida.');
                }
                $segmentos[] = $segmento;
            }
        }
        return implode('/', $segmentos);
    }

    private function reglasArchivo(): array
    {
        return ['archivo' => 'required|file|max:51200|extensions:' . implode(',', self::EXTENSIONES_PERMITIDAS)];
    }

    private function nombreArchivo($archivo): string
    {
        $nombre = basename($archivo->getClientOriginalName());
        $nombre = preg_replace('/[\x00-\x1F\/\x5C:*?"<>|]/', '_', $nombre);
        return ltrim($nombre, '.') ?: 'archivo.' . strtolower($archivo->getClientOriginalExtension());
    }

    // Método para mostrar los archivos
    public function index()
    {
        $carpeta = 'mi_carpeta';

        if (!Storage::exists($carpeta)) {
            Storage::makeDirectory($carpeta);
        }

        $archivos = Storage::files($carpeta);
        $carpetas = Storage::directories($carpeta);
        
        // Obtener rutas locales físicas configuradas
        $rutasLocales = \App\Models\RutaLocal::orderBy('created_at', 'desc')->get();

        return view('archivos.index', compact('archivos', 'carpetas', 'carpeta', 'rutasLocales'));
    }

    // Método para subir archivos
    public function upload(Request $request)
    {
        $request->validate($this->reglasArchivo());

        try {
            $carpeta = 'mi_carpeta';
            $archivo = $request->file('archivo');
            $archivo->storeAs($carpeta, $this->nombreArchivo($archivo));

            return redirect()->route('ruta.archivos')->with('success', 'Archivo subido correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('ruta.archivos')->with('error', 'Error al subir el archivo: ' . $e->getMessage());
        }
    }

    // Método para descargar archivos
    public function download($archivo)
    {
        $ruta = $this->rutaSegura($archivo);

        if (Storage::exists($ruta)) {
            return Storage::download($ruta);
        }

        return redirect()->route('ruta.archivos')->with('error', 'El archivo no existe.');
    }

    // Método para eliminar archivos
    public function delete($archivo)
    {
        $ruta = $this->rutaSegura($archivo);

        if (Storage::exists($ruta)) {
            Storage::delete($ruta);
            return redirect()->route('ruta.archivos')->with('success', 'Archivo eliminado correctamente.');
        }

        return redirect()->route('ruta.archivos')->with('error', 'El archivo no existe.');
    }

    // Método para crear carpetas
    public function createFolder(Request $request)
    {
        $request->validate([
            'nombre_carpeta' => 'required|string|max:255',
        ]);

        $carpeta = $this->rutaSegura($request->nombre_carpeta);

        if (!Storage::exists($carpeta)) {
            Storage::makeDirectory($carpeta);
            return redirect()->route('ruta.archivos')->with('success', 'Carpeta creada correctamente.');
        }

        return redirect()->route('ruta.archivos')->with('error', 'La carpeta ya existe.');
    }

    // Método para eliminar carpetas
    public function deleteFolder($carpeta)
    {
        $ruta = $this->rutaSegura($carpeta);
        if ($ruta === 'mi_carpeta') {
            abort(400, 'Ruta no válida.');
        }

        if (Storage::exists($ruta)) {
            Storage::deleteDirectory($ruta);
            return redirect()->route('ruta.archivos')->with('success', 'Carpeta eliminada correctamente.');
        }

        return redirect()->route('ruta.archivos')->with('error', 'La carpeta no existe.');
    }

    // Método para abrir una carpeta y ver su contenido
    public function openFolder($carpeta = null)
    {
        // Decodifica la ruta completa
        $carpeta = $carpeta ? urldecode($carpeta) : null;

        // Construye la ruta completa
        $ruta = $this->rutaSegura($carpeta);

        if (!Storage::exists($ruta)) {
            return redirect()->route('ruta.archivos')->with('error', 'La carpeta no existe.');
        }

        $archivos = Storage::files($ruta);
        $subcarpetas = Storage::directories($ruta);

        return view('archivos.folder', compact('archivos', 'subcarpetas', 'carpeta', 'ruta'));
    }

    // Método para subir archivos a una carpeta específica
    public function uploadToFolder(Request $request, $carpeta)
    {
        $request->validate($this->reglasArchivo());

        $carpeta = urldecode($carpeta);
        $ruta = $this->rutaSegura($carpeta);

        try {
            $archivo = $request->file('archivo');
            $archivo->storeAs($ruta, $this->nombreArchivo($archivo), 'public'); // Usa el disco 'public'

            return redirect()->route('archivos.open-folder', ['carpeta' => $carpeta])
                ->with('success', 'Archivo subido correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('archivos.open-folder', ['carpeta' => $carpeta])
                ->with('error', 'Error al subir el archivo: ' . $e->getMessage());
        }
    }
    // Método para crear subcarpetas
    public function createSubfolder(Request $request, $carpeta)
    {
        $request->validate([
            'nombre_subcarpeta' => 'required|string|max:255',
        ]);

        $carpeta = urldecode($carpeta);
        $ruta = $this->rutaSegura($carpeta, $request->nombre_subcarpeta);

        if (!Storage::exists($ruta)) {
            Storage::makeDirectory($ruta);
            return redirect()->route('archivos.open-folder', ['carpeta' => $carpeta])
                ->with('success', 'Subcarpeta creada correctamente.');
        }

        return redirect()->route('archivos.open-folder', ['carpeta' => $carpeta])
            ->with('error', 'La subcarpeta ya existe.');
    }

    // Método para descargar archivos de una carpeta específica
    public function downloadFromFolder($carpeta, $archivo)
    {
        $ruta = $this->rutaSegura($carpeta, $archivo);

        if (Storage::exists($ruta)) {
            return Storage::download($ruta);
        }

        return redirect()->route('archivos.open-folder', ['carpeta' => $carpeta])->with('error', 'El archivo no existe.');
    }

    // Método para eliminar archivos de una carpeta específica
    public function deleteFromFolder($carpeta, $archivo)
    {
        $ruta = $this->rutaSegura($carpeta, $archivo);

        if (Storage::exists($ruta)) {
            Storage::delete($ruta);
            return redirect()->route('archivos.open-folder', ['carpeta' => $carpeta])->with('success', 'Archivo eliminado correctamente.');
        }

        return redirect()->route('archivos.open-folder', ['carpeta' => $carpeta])->with('error', 'El archivo no existe.');
    }
}
