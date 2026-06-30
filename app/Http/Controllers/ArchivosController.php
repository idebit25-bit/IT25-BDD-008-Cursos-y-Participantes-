<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchivosController extends Controller
{
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
        $request->validate([
            'archivo' => 'required|file',
        ]);

        try {
            $carpeta = 'mi_carpeta';
            $archivo = $request->file('archivo');
            $archivo->storeAs($carpeta, $archivo->getClientOriginalName());

            return redirect()->route('ruta.archivos')->with('success', 'Archivo subido correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('ruta.archivos')->with('error', 'Error al subir el archivo: ' . $e->getMessage());
        }
    }

    // Método para descargar archivos
    public function download($archivo)
    {
        $carpeta = 'mi_carpeta';
        $ruta = $carpeta . '/' . $archivo;

        if (Storage::exists($ruta)) {
            return Storage::download($ruta);
        }

        return redirect()->route('ruta.archivos')->with('error', 'El archivo no existe.');
    }

    // Método para eliminar archivos
    public function delete($archivo)
    {
        $carpeta = 'mi_carpeta';
        $ruta = $carpeta . '/' . $archivo;

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
            'nombre_carpeta' => 'required|string',
        ]);

        $carpeta = 'mi_carpeta/' . $request->nombre_carpeta;

        if (!Storage::exists($carpeta)) {
            Storage::makeDirectory($carpeta);
            return redirect()->route('ruta.archivos')->with('success', 'Carpeta creada correctamente.');
        }

        return redirect()->route('ruta.archivos')->with('error', 'La carpeta ya existe.');
    }

    // Método para eliminar carpetas
    public function deleteFolder($carpeta)
    {
        $ruta = 'mi_carpeta/' . $carpeta;

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
        $ruta = 'mi_carpeta/' . ($carpeta ?? '');

        // Limpia la ruta de caracteres especiales adicionales
        $ruta = rtrim($ruta, '/');

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
        $request->validate([
            'archivo' => 'required|file',
        ]);

        try {
            $carpeta = urldecode($carpeta);
            $ruta = 'mi_carpeta/' . $carpeta;
            $archivo = $request->file('archivo');
            $archivo->storeAs($ruta, $archivo->getClientOriginalName(), 'public'); // Usa el disco 'public'

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
        $ruta = 'mi_carpeta/' . $carpeta . '/' . $request->nombre_subcarpeta;

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
        $ruta = 'mi_carpeta/' . $carpeta . '/' . $archivo;

        if (Storage::exists($ruta)) {
            return Storage::download($ruta);
        }

        return redirect()->route('archivos.open-folder', ['carpeta' => $carpeta])->with('error', 'El archivo no existe.');
    }

    // Método para eliminar archivos de una carpeta específica
    public function deleteFromFolder($carpeta, $archivo)
    {
        $ruta = 'mi_carpeta/' . $carpeta . '/' . $archivo;

        if (Storage::exists($ruta)) {
            Storage::delete($ruta);
            return redirect()->route('archivos.open-folder', ['carpeta' => $carpeta])->with('success', 'Archivo eliminado correctamente.');
        }

        return redirect()->route('archivos.open-folder', ['carpeta' => $carpeta])->with('error', 'El archivo no existe.');
    }
}
