<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Cursos;
use App\Models\CursoModalidad;
use App\Models\CursoRecurso;
use App\Models\CursoEvaluacion;
use App\Models\CursoCertificacion;
use App\Models\CursoPaso;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CursosExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\CourseActionLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Inscripcion;
use App\Models\Participantes;
use App\Services\DriveCarpetas;

class CursoController extends Controller
{
    /**
     * Mostrar la lista de cursos (activos e inactivos).
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'instructor' => 'nullable|string|max:255',
            'costo_min' => 'nullable|numeric|min:0',
            'costo_max' => 'nullable|numeric|min:0'.($request->filled('costo_min') ? '|gte:costo_min' : ''),
            'viewOption' => 'nullable|in:default,drive',
        ]);
        $vista = $request->query('viewOption', 'default');
        $search = $request->get('search');
        $instructor = $request->get('instructor');
        $ordenes = [
            'id_asc' => ['nomenclatura', 'asc'],
            'id_desc' => ['nomenclatura', 'desc'],
            'nombre_asc' => ['nombre', 'asc'],
            'nombre_desc' => ['nombre', 'desc'],
        ];
        $orden = $request->query('orden', $request->session()->get('cursos.orden', 'id_asc'));
        if (!is_string($orden) || !array_key_exists($orden, $ordenes)) $orden = 'id_asc';
        $request->session()->put('cursos.orden', $orden);

        $query = Cursos::whereNull('parent_id')->whereIn('status', [0, 1]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('nomenclatura', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($instructor) {
            $query->where('instructor_responsable', $instructor);
        }
        if ($request->filled('costo_min')) $query->where('costo', '>=', $request->input('costo_min'));
        if ($request->filled('costo_max')) $query->where('costo', '<=', $request->input('costo_max'));
        if ($vista === 'drive') $query->with(['recursos', 'evaluaciones', 'certificaciones']);

        // Ordenar antes de paginar mantiene la secuencia en búsquedas y al regresar.
        [$columna, $direccion] = $ordenes[$orden];
        $cursos = $query->orderBy($columna, $direccion)->orderBy('id', 'asc')
            ->paginate(5)->withQueryString()->appends(['orden' => $orden]);
        $subcursos = Cursos::whereNotNull('parent_id')->get(); 

        return view('cursos.index', compact('cursos', 'subcursos', 'orden', 'vista'));
    }

    public function obtenerSubcursos($cursoId, Request $request)
    {
        try {
            $subcursos = Cursos::where('parent_id', $cursoId)->get()->map(function($sub) {
                return [
                    'id' => $sub->id,
                    'nomenclatura' => $sub->Nomenclatura ?: $sub->nomenclatura,
                    'nombre' => $sub->NombredelCurso ?: $sub->nombre,
                    'descripcion' => $sub->DescripciondeCurso ?: $sub->descripcion,
                    'duracion' => $sub->Duracioncurso ?: $sub->duracion,
                    'costo' => number_format((float)($sub->CostodelCurso ?: $sub->costo), 2),
                    'fecha_inicio' => $sub->FechadeInicio ? \Carbon\Carbon::parse($sub->FechadeInicio)->format('d/m/Y') : '-',
                    'fecha_termino' => $sub->FechadeTermino ? \Carbon\Carbon::parse($sub->FechadeTermino)->format('d/m/Y') : '-',
                    'instructor_responsable' => $sub->InstructorResponsable ?: $sub->instructor_responsable,
                    'estatus_progreso' => $sub->estatus_progreso,
                    'status' => $sub->status,
                ];

            });
            return response()->json($subcursos);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function papelera()
    {
        $cursos = Cursos::whereNull('parent_id')->where('status', 2)->orderBy('updated_at', 'desc')->get();
        $subcursos = Cursos::whereNotNull('parent_id')->where('status', 2)->orderBy('updated_at', 'desc')->get();
        
        return view('cursos.papelera', compact('cursos', 'subcursos'));
    }

    public function destroy(string $id)
    {
        try {
            $curso = Cursos::findOrFail($id);
            $curso->update(['status' => 2]);

            CourseActionLog::create([
                'curso_id' => $curso->id,
                'nombre_curso' => $curso->nombre,
                'user_id' => Auth::id(),
                'accion' => 'Eliminado',
                'detalles' => 'Curso enviado a la papelera.',
                'fecha_accion' => now(),
            ]);

            return redirect()->route('cursos.index')->with('success', 'Curso enviado a la papelera.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al enviar a la papelera: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $curso = Cursos::findOrFail($id);
            $newStatus = ($curso->status == 1) ? 0 : 1;
            $curso->update(['status' => $newStatus]);
            
            $accion = $newStatus == 1 ? 'Activado' : 'Suspendido';

            CourseActionLog::create([
                'curso_id' => $curso->id,
                'nombre_curso' => $curso->nombre,
                'user_id' => Auth::id(),
                'accion' => $accion,
                'detalles' => "Curso $accion por el usuario.",
                'fecha_accion' => now(),
            ]);

            return redirect()->route('cursos.index')->with('success', "Curso $accion exitosamente.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar el estado del curso: ' . $e->getMessage());
        }
    }

    public function activarCurso($id)
    {
        try {
            $curso = Cursos::findOrFail($id);
            $curso->update(['status' => 1]);

            CourseActionLog::create([
                'curso_id' => $curso->id,
                'nombre_curso' => $curso->nombre,
                'user_id' => Auth::id(),
                'accion' => 'Activado',
                'detalles' => 'Curso reactivado por el usuario.',
                'fecha_accion' => now(),
            ]);

            return redirect()->route('cursos.index')->with('success', 'Curso reactivado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al activar el curso: ' . $e->getMessage());
        }
    }

    public function eliminarDefinitivo(Request $request, $id)
    {
        try {
            if (!Hash::check($request->password, Auth::user()->password)) {
                return back()->with('error', 'Contraseña incorrecta.');
            }

            if (Auth::user()?->puesto != 'Administrador') {
                return back()->with('error', 'No tienes permisos para realizar esta acción.');
            }

            $curso = Cursos::findOrFail($id);
            $nombre = $curso->nombre;
            
            $curso->modalidades()->delete();
            $curso->recursos()->delete();
            $curso->evaluaciones()->delete();
            $curso->certificaciones()->delete();
            $curso->pasos()->delete();
            
            $curso->delete();

            return redirect()->route('cursos.index')->with('success', "El curso '$nombre' ha sido eliminado permanentemente.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el curso: ' . $e->getMessage());
        }
    }

    public function cancelarCreacion()
    {
        $cursoId = session('curso_id');
        if ($cursoId) {
            $curso = Cursos::find($cursoId);
            if ($curso) {
                if (str_starts_with($curso->nomenclatura, 'TEMP-')) {
                    $curso->delete();
                }
            }
            session()->forget(['curso_id', 'cursos_paso1', 'cursos_paso2', 'cursos_paso3', 'cursos_paso4', 'cursos_paso5', 'cursos_paso6']);
        }
        return redirect()->route('cursos.index')->with('info', 'Creación de curso cancelada.');
    }

    public function show(Cursos $curso)
    {
        return view('cursos.show', compact('curso'));
    }

    // Reintento manual cuando la carpeta no se pudo crear al guardar el paso 1
    public function crearCarpetaDrive(Cursos $curso, DriveCarpetas $drive)
    {
        if (!$drive->configurado()) {
            return back()->with('error', 'Drive no está configurado. Un administrador debe capturar la carpeta base en Configuraciones.');
        }

        return $drive->asegurarCarpetaCurso($curso)
            ? back()->with('success', 'Carpeta de Drive lista.')
            : back()->with('error', 'No se pudo crear la carpeta en Drive. Revisa el registro de errores.');
    }

    public function edit(Cursos $curso)
    {
        $coloresPorPaso = $this->calcularProgresoPaso($curso);
        return view('cursos.edit', compact('curso', 'coloresPorPaso'));
    }

    public function iniciarCurso()
    {
        try {
            $this->limpiarBorradoresSesion();

            $tempId = Str::random(8);
            $curso = Cursos::create([
                'nomenclatura' => 'TEMP-' . $tempId,
                'nombre' => 'Borrador Curso ' . date('Y-m-d H:i'),
                'status' => 1,
            ]);

            session(['curso_id' => $curso->id]);
            session()->forget('nomenclatura_generada');

            return redirect()->route('curso.paso1')->with('success', 'Formulario de creación iniciado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al iniciar el curso: ' . $e->getMessage());
        }
    }

    public function iniciarSubcursos($id)
    {
        try {
            $this->limpiarBorradoresSesion();

            $parent = Cursos::findOrFail($id);
            $tempId = Str::random(8);
            
            $subcurso = Cursos::create([
                'nomenclatura' => 'TEMP-SUB-' . $tempId,
                'nombre' => 'Subcurso de ' . $parent->nombre . ' - ' . date('Y-m-d H:i'),
                'parent_id' => $parent->id,
                'status' => 1,
            ]);

            session(['curso_id' => $subcurso->id]);
            session()->forget('nomenclatura_generada');

            return redirect()->route('curso.paso1')->with('success', 'Formulario de subcurso iniciado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al iniciar el subcurso: ' . $e->getMessage());
        }
    }

    private function limpiarBorradoresSesion()
    {
        $oldId = session('curso_id');
        if ($oldId) {
            $oldCurso = Cursos::find($oldId);
            if ($oldCurso && str_starts_with($oldCurso->nomenclatura, 'TEMP-')) {
                $oldCurso->delete();
            }
            session()->forget(['curso_id', 'cursos_paso1', 'cursos_paso2', 'cursos_paso3', 'cursos_paso4', 'cursos_paso5', 'cursos_paso6']);
        }
    }

    // ============================================
    // FUNCIÓN AUXILIAR PARA GUARDAR ARCHIVOS
    // ============================================
    public function verArchivoCurso(Cursos $curso, string $filename)
    {
        $filename = basename($filename);
        $path = storage_path('app/public/cursos/' . $curso->id . '/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        // Sólo PDF e imágenes se muestran en el navegador; el resto se descarga
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $headers = ['X-Content-Type-Options' => 'nosniff'];
        if (!in_array($extension, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            return response()->download($path, $filename, $headers);
        }

        return response()->file($path, $headers);
    }

    // Valida la extensión y genera un nombre seguro para un documento de curso
    private function nombreArchivoCurso($file, string $inputName): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ArchivosController::EXTENSIONES_PERMITIDAS, true)) {
            throw ValidationException::withMessages([
                $inputName => 'Tipo de archivo no permitido. Formatos aceptados: ' . implode(', ', ArchivosController::EXTENSIONES_PERMITIDAS) . '.',
            ]);
        }

        $nombreBase = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $nombreLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nombreBase);
        return time() . '_' . uniqid() . '_' . $nombreLimpio . '.' . $extension;
    }

    private function guardarArchivosCurso($curso, $request, $archivosMap)
    {
        $cursoPath = storage_path('app/public/cursos/' . $curso->id);
        if (!file_exists($cursoPath)) {
            mkdir($cursoPath, 0777, true);
        }

        foreach ($archivosMap as $inputName => $tipoRecurso) {
            if ($request->hasFile($inputName) && $request->file($inputName)->isValid()) {
                $file = $request->file($inputName);
                $fileName = $this->nombreArchivoCurso($file, $inputName);

                $file->move($cursoPath, $fileName);
                $rutaPublica = 'cursos/' . $curso->id . '/' . $fileName;
                
                CursoRecurso::updateOrCreate(
                    ['curso_id' => $curso->id, 'tipo_recurso' => $tipoRecurso],
                    ['url' => $rutaPublica]
                );
            }
        }
    }

    // El estado DC3 se calcula (Cursos::estadoDc3); sólo se guarda la excepción
    // "este curso no otorga DC3". Valores manuales anteriores se descartan.
    private function guardarExcepcionDc3(Cursos $curso, bool $noOtorga): void
    {
        if ($noOtorga) {
            CursoCertificacion::updateOrCreate(
                ['curso_id' => $curso->id, 'tipo_certificacion' => 'dc3'],
                ['nombre' => Cursos::DC3_NO_SE_ENTREGA]
            );
        } else {
            $curso->certificaciones()->where('tipo_certificacion', 'dc3')->delete();
        }
    }

    // ============================================
    // PASO 1
    // ============================================
    public function crearPaso1()
    {
        $cursoId = session('curso_id');
        $curso = Cursos::find($cursoId);
        $datosPadre = null;
        return view('cursos.paso1', compact('curso', 'datosPadre'));
    }

    public function guardarPaso1(Request $request)
    {
        $cursoId = session('curso_id');
        $validated = $request->validate([
            'Nomenclatura' => 'required|string|max:255|unique:cursos,nomenclatura,' . $cursoId,
            'NombredelCurso' => 'required|string|max:255',
            'DescripciondeCurso' => 'nullable|string|max:2000',
            'CostodelCurso' => 'nullable|numeric',
            'InstructorResponsable' => 'nullable|string|max:255',
            'FechadeInicio' => 'nullable|date',
            'FechadeTermino' => 'nullable|date|after_or_equal:FechadeInicio',
            'Duracioncurso' => 'nullable|string|max:255',
        ]);

        $curso = Cursos::findOrFail($cursoId);

        $curso->update([
            'nomenclatura' => $validated['Nomenclatura'],
            'nombre' => $validated['NombredelCurso'] ?? '',
            'descripcion' => $validated['DescripciondeCurso'] ?? '',
            'costo' => $validated['CostodelCurso'] ?? 0,
            'instructor_responsable' => $validated['InstructorResponsable'] ?? '',
            'fecha_inicio' => $validated['FechadeInicio'] ?? null,
            'fecha_termino' => $validated['FechadeTermino'] ?? null,
            'duracion' => $validated['Duracioncurso'] ?? '',
        ]);

        // Carpeta del curso en Drive con su nomenclatura y nombre (si Drive está configurado)
        app(DriveCarpetas::class)->asegurarCarpetaCurso($curso);

        session(['cursos_paso1' => $validated]);
        return redirect()->route('curso.paso2');
    }

    // ============================================
    // PASO 2
    // ============================================
    public function mostrarPaso2() 
    { 
        return view('cursos.paso2', ['curso' => Cursos::find(session('curso_id'))]); 
    }
    
    public function guardarPaso2(Request $request)
    {
        $v = $request->validate([
            'modalidad' => 'nullable|in:virtual,presencial,mixto',
            'sin_fecha' => 'nullable|boolean'
        ]);
        
        $curso = Cursos::findOrFail(session('curso_id'));
        
        $curso->update([
            'modalidad' => $v['modalidad'] ?? null,
            'sin_fecha' => $v['sin_fecha'] ?? false,
        ]);

        if (($v['sin_fecha'] ?? false)) {
            $curso->update([
                'fecha_inicio' => null,
                'fecha_termino' => null,
            ]);
        }
        
        if (!empty($v['modalidad'])) {
            CursoModalidad::updateOrCreate(
                ['curso_id' => $curso->id, 'modalidad' => $v['modalidad']],
                ['curso_id' => $curso->id, 'modalidad' => $v['modalidad']]
            );
        } else {
            $curso->modalidades()->delete();
        }
        
        session(['cursos_paso2' => $v]);
        
        return redirect()->route('curso.paso3');
    }

    // ============================================
    // PASO 3
    // ============================================
    public function mostrarPaso3() 
    { 
        $curso = Cursos::find(session('curso_id'));
        $recursos = [];
        
        if ($curso) {
            $recursosDb = $curso->recursos()->get()->keyBy('tipo_recurso');
            
            $recursos['sin_fecha'] = $recursosDb->get('sin_fecha');
            $recursos['facebook'] = $recursosDb->get('facebook');
            $recursos['linkedin'] = $recursosDb->get('linkedin');
            $recursos['instagram'] = $recursosDb->get('instagram');
            
            $recursos['sin_fecha_archivo'] = $recursosDb->get('sin_fecha_archivo');
            $recursos['facebook_archivo'] = $recursosDb->get('facebook_archivo');
            $recursos['linkedin_archivo'] = $recursosDb->get('linkedin_archivo');
            $recursos['instagram_archivo'] = $recursosDb->get('instagram_archivo');
        }
        
        return view('cursos.paso3', compact('curso', 'recursos')); 
    }
    
    public function guardarPaso3(Request $request) 
    {
        $v = $request->validate([
            'SinFecha' => 'nullable|string',
            'DriveSinFecha' => 'nullable|string',
            'Facebook' => 'nullable|string',
            'DriveFacebook' => 'nullable|string',
            'Linkedin' => 'nullable|string',
            'DriveLinkedin' => 'nullable|string',
            'Instagram' => 'nullable|string',
            'DriveInstagram' => 'nullable|string',
        ]);
        
        $curso = Cursos::findOrFail(session('curso_id'));

        $recursosMap = [
            'sin_fecha' => ['url' => $v['SinFecha'] ?? null, 'drive_url' => $v['DriveSinFecha'] ?? null],
            'facebook' => ['url' => $v['Facebook'] ?? null, 'drive_url' => $v['DriveFacebook'] ?? null],
            'linkedin' => ['url' => $v['Linkedin'] ?? null, 'drive_url' => $v['DriveLinkedin'] ?? null],
            'instagram' => ['url' => $v['Instagram'] ?? null, 'drive_url' => $v['DriveInstagram'] ?? null],
        ];

        foreach ($recursosMap as $tipo => $data) {
            if (!empty($data['url']) || !empty($data['drive_url'])) {
                CursoRecurso::updateOrCreate(
                    ['curso_id' => $curso->id, 'tipo_recurso' => $tipo],
                    $data
                );
            }
        }

        $this->guardarArchivosCurso($curso, $request, [
            'archivoSinFecha' => 'sin_fecha_archivo',
            'archivoFacebook' => 'facebook_archivo',
            'archivoLinkedIn' => 'linkedin_archivo',
            'archivoInstagram' => 'instagram_archivo',
        ]);
        
        session(['cursos_paso3' => $v]);
        return redirect()->route('curso.paso4');
    }

    // ============================================
    // PASO 4
    // ============================================
    public function mostrarPaso4() 
    { 
        $curso = Cursos::find(session('curso_id'));
        $recursos = [];
        
        if ($curso) {
            $recursosDb = $curso->recursos()->get()->keyBy('tipo_recurso');
            
            $recursos['temario'] = $recursosDb->get('temario');
            $recursos['itinerario'] = $recursosDb->get('itinerario');
            $recursos['planeacion'] = $recursosDb->get('planeacion');
            
            $recursos['temario_archivo'] = $recursosDb->get('temario_archivo');
            $recursos['itinerario_archivo'] = $recursosDb->get('itinerario_archivo');
            $recursos['planeacion_archivo'] = $recursosDb->get('planeacion_archivo');
        }
        
        return view('cursos.paso4', compact('curso', 'recursos')); 
    }
    
    public function guardarPaso4(Request $request) 
    {
        $v = $request->validate([
            'Temario' => 'nullable|string',
            'DriveTemario' => 'nullable|string',
            'Itinerario' => 'nullable|string',
            'DriveItinerario' => 'nullable|string',
            'Planeacion' => 'nullable|string',
            'DrivePlaneacion' => 'nullable|string',
        ]);
        
        $curso = Cursos::findOrFail(session('curso_id'));

        $recursosMap = [
            'temario' => ['url' => $v['Temario'] ?? null, 'drive_url' => $v['DriveTemario'] ?? null],
            'itinerario' => ['url' => $v['Itinerario'] ?? null, 'drive_url' => $v['DriveItinerario'] ?? null],
            'planeacion' => ['url' => $v['Planeacion'] ?? null, 'drive_url' => $v['DrivePlaneacion'] ?? null],
        ];

        foreach ($recursosMap as $tipo => $data) {
            if (!empty($data['url']) || !empty($data['drive_url'])) {
                CursoRecurso::updateOrCreate(
                    ['curso_id' => $curso->id, 'tipo_recurso' => $tipo],
                    $data
                );
            }
        }

        $this->guardarArchivosCurso($curso, $request, [
            'archivoTemario' => 'temario_archivo',
            'archivoItinerario' => 'itinerario_archivo',
            'archivoPlaneacion' => 'planeacion_archivo',
        ]);
        
        session(['cursos_paso4' => $v]);
        return redirect()->route('curso.paso5');
    }

    // ============================================
    // PASO 5
    // ============================================
    public function mostrarPaso5() 
    { 
        $curso = Cursos::find(session('curso_id'));
        $recursos = [];
        
        if ($curso) {
            $recursosDb = $curso->recursos()->get()->keyBy('tipo_recurso');
            
            $recursos['digital'] = $recursosDb->get('digital');
            $recursos['presentacion'] = $recursosDb->get('presentacion');
            $recursos['impreso'] = $recursosDb->get('impreso');
            
            $recursos['digital_archivo'] = $recursosDb->get('digital_archivo');
            $recursos['presentacion_archivo'] = $recursosDb->get('presentacion_archivo');
            $recursos['impreso_archivo'] = $recursosDb->get('impreso_archivo');
        }
        
        return view('cursos.paso5', compact('curso', 'recursos')); 
    }
    
    public function guardarPaso5(Request $request) 
    {
        $v = $request->validate([
            'Digital' => 'nullable|string',
            'DriveDigital' => 'nullable|string',
            'Impreso_Presentable' => 'nullable|string',
            'DriveImpreso' => 'nullable|string',
        ]);
        
        $curso = Cursos::findOrFail(session('curso_id'));

        $recursosMap = [
            'digital' => ['url' => $v['Digital'] ?? null, 'drive_url' => $v['DriveDigital'] ?? null],
            'impreso' => ['url' => $v['Impreso_Presentable'] ?? null, 'drive_url' => $v['DriveImpreso'] ?? null],
        ];

        foreach ($recursosMap as $tipo => $data) {
            if (!empty($data['url']) || !empty($data['drive_url'])) {
                CursoRecurso::updateOrCreate(
                    ['curso_id' => $curso->id, 'tipo_recurso' => $tipo],
                    $data
                );
            } else {
                $curso->recursos()->where('tipo_recurso', $tipo)->delete();
            }
        }

        $this->guardarArchivosCurso($curso, $request, [
            'archivoDigital' => 'digital_archivo',
            'archivoImpreso' => 'impreso_archivo',
        ]);
        
        session(['cursos_paso5' => $v]);
        return redirect()->route('curso.paso6');
    }

    // ============================================
    // PASO 6
    // ============================================
    public function mostrarPaso6() 
    { 
        $curso = Cursos::find(session('curso_id'));
        $recursos = [];
        $evaluaciones = [];
        
        if ($curso) {
            $recursosDb = $curso->recursos()->get()->keyBy('tipo_recurso');
            $evaluacionesDb = $curso->evaluaciones()->get()->keyBy('tipo_evaluacion');
            $certificacionesDb = $curso->certificaciones()->get()->keyBy('tipo_certificacion');
            
            $recursos['presentacion'] = $recursosDb->get('presentacion');
            $recursos['presentacion_archivo'] = $recursosDb->get('presentacion_archivo');
            
            $evaluaciones['diagnostica'] = $evaluacionesDb->get('diagnostica');
            $evaluaciones['satisfaccion'] = $evaluacionesDb->get('satisfaccion');
            $evaluaciones['final'] = $evaluacionesDb->get('final');
            
        }
        $estadoDc3 = $curso?->estadoDc3();
        
        return view('cursos.paso6', compact('curso', 'recursos', 'evaluaciones', 'estadoDc3')); 
    }
    
    public function guardarPaso6(Request $request) 
    {
        $v = $request->validate([
            'Presentacion' => 'nullable|string',
            'DrivePresentacion' => 'nullable|string',
            'EvaluacionDiagnostica' => 'nullable|string',
            'DriveEvaluacionDiagnostica' => 'nullable|string',
            'EvaluacionSatisfaccion' => 'nullable|string',
            'DriveEvaluacionSatisfaccion' => 'nullable|string',
            'EvaluacionFinal' => 'nullable|string',
            'DriveEvaluacionFinal' => 'nullable|string',
            'DC3NoOtorga' => 'nullable|boolean',
        ]);

        $curso = Cursos::findOrFail(session('curso_id'));

        // Guardar Presentación
        if (!empty($v['Presentacion']) || !empty($v['DrivePresentacion'])) {
            CursoRecurso::updateOrCreate(
                ['curso_id' => $curso->id, 'tipo_recurso' => 'presentacion'],
                [
                    'url' => $v['Presentacion'] ?? null,
                    'drive_url' => $v['DrivePresentacion'] ?? null
                ]
            );
        } else {
            $curso->recursos()->where('tipo_recurso', 'presentacion')->delete();
        }

        // Guardar archivo de presentación
        if ($request->hasFile('archivoPresentacion') && $request->file('archivoPresentacion')->isValid()) {
            $file = $request->file('archivoPresentacion');
            $fileName = $this->nombreArchivoCurso($file, 'archivoPresentacion');
            $cursoPath = storage_path('app/public/cursos/' . $curso->id);
            if (!file_exists($cursoPath)) {
                mkdir($cursoPath, 0777, true);
            }
            $file->move($cursoPath, $fileName);
            $rutaPublica = 'cursos/' . $curso->id . '/' . $fileName;
            
            CursoRecurso::updateOrCreate(
                ['curso_id' => $curso->id, 'tipo_recurso' => 'presentacion_archivo'],
                ['url' => $rutaPublica]
            );
        }

        // Guardar evaluaciones
        $evaluacionesMap = [
            'diagnostica' => [
                'url' => $v['EvaluacionDiagnostica'] ?? null,
                'drive_url' => $v['DriveEvaluacionDiagnostica'] ?? null,
            ],
            'satisfaccion' => [
                'url' => $v['EvaluacionSatisfaccion'] ?? null,
                'drive_url' => $v['DriveEvaluacionSatisfaccion'] ?? null,
            ],
            'final' => [
                'url' => $v['EvaluacionFinal'] ?? null,
                'drive_url' => $v['DriveEvaluacionFinal'] ?? null,
            ],
        ];

        foreach ($evaluacionesMap as $tipo => $data) {
            if (!empty($data['url']) || !empty($data['drive_url'])) {
                CursoEvaluacion::updateOrCreate(
                    ['curso_id' => $curso->id, 'tipo_evaluacion' => $tipo],
                    $data
                );
            } else {
                $curso->evaluaciones()->where('tipo_evaluacion', $tipo)->delete();
            }
        }

        $this->guardarExcepcionDc3($curso, $request->boolean('DC3NoOtorga'));
        
        session(['cursos_paso6' => $v]);
        return redirect()->route('curso.paso7')->with('success', 'Paso 6 guardado correctamente');
    }

    // ============================================
    // PASO 7
    // ============================================
    public function mostrarPaso7() 
    { 
        $curso = Cursos::find(session('curso_id'));
        $datos = $curso
            ? $this->datosPaso7($curso)
            : ['certificaciones' => [], 'recursos' => [], 'fechaRegistro' => null, 'stpsHeredadoDe' => null, 'puedeUsarStpsInicial' => false];

        return view('cursos.paso7', array_merge(['curso' => $curso], $datos));
    }

    // Datos del paso 7. Si el subcurso hereda STPS, se muestran los del curso inicial
    // (sólo lectura); UDEMY siempre es del propio curso.
    private function datosPaso7(Cursos $curso): array
    {
        $stpsHeredadoDe = $curso->heredaStps() ? $curso->cursoPadre : null;
        $origenStps = $stpsHeredadoDe ?? $curso;

        $certificacionesDb = $origenStps->certificaciones()->get()->keyBy('tipo_certificacion');
        $recursosStpsDb = $origenStps->recursos()->get()->keyBy('tipo_recurso');

        return [
            'certificaciones' => [
                'dc5' => $certificacionesDb->get('dc5'),
                'certificado_comprobacion' => $certificacionesDb->get('certificado_comprobacion'),
                'carta_poder' => $certificacionesDb->get('carta_poder'),
            ],
            'fechaRegistro' => $certificacionesDb->get('fecha_registro'),
            'recursos' => [
                'dc5_archivo' => $recursosStpsDb->get('dc5_archivo'),
                'certificado_archivo' => $recursosStpsDb->get('certificado_archivo'),
                'carta_poder_archivo' => $recursosStpsDb->get('carta_poder_archivo'),
                'udemy' => $curso->recursos()->where('tipo_recurso', 'udemy')->first(),
            ],
            'stpsHeredadoDe' => $stpsHeredadoDe,
            // Subcurso con datos propios cuyo curso inicial ya tiene el alta
            'puedeUsarStpsInicial' => !$stpsHeredadoDe && $curso->parent_id && $curso->cursoPadre?->tieneAltaStps(),
        ];
    }

    // Descarta la documentación STPS propia del subcurso para usar la del curso inicial
    public function usarStpsCursoInicial(Cursos $curso)
    {
        if (!$curso->parent_id || !$curso->cursoPadre?->tieneAltaStps()) {
            return back()->with('error', 'El curso inicial todavía no tiene el alta STPS.');
        }

        $curso->certificaciones()->whereIn('tipo_certificacion', Cursos::STPS_CERTIFICACIONES)->delete();
        $curso->recursos()->whereIn('tipo_recurso', Cursos::STPS_RECURSOS)->delete();

        CourseActionLog::create([
            'curso_id' => $curso->id,
            'nombre_curso' => $curso->nombre,
            'user_id' => Auth::id(),
            'accion' => 'Editado',
            'detalles' => 'El subcurso ahora usa la documentación STPS del curso inicial.',
            'fecha_accion' => now(),
        ]);

        return back()->with('success', 'El subcurso ahora usa la documentación STPS del curso inicial.');
    }
    
    public function guardarPaso7(Request $request) 
    {
        $v = $request->validate([
            'FechaRegistroSTPS' => 'nullable|date',
            'FormatoDC5' => 'nullable|string',
            'FormatoDC5TieneFirma' => 'nullable|string',
            'CertificadoComprobacion' => 'nullable|string',
            'DriveCertificadoComprobacion' => 'nullable|string',
            'CartaPoderTieneFirma' => 'nullable|string',
            'DriveCartaPoder' => 'nullable|string',
            'UDEMY' => 'nullable|string',
        ]);
        
        $curso = Cursos::findOrFail(session('curso_id'));

        $dc5Firma = ($v['FormatoDC5TieneFirma'] ?? 'No') === 'Si';
        $cartaFirma = ($v['CartaPoderTieneFirma'] ?? 'No') === 'Si';
        $udemyBool = ($v['UDEMY'] ?? 'No se ha prellenado') === 'Prellenado';

        // Si el subcurso hereda STPS del curso inicial, sólo se guarda UDEMY
        if (!$curso->heredaStps()) {
            // Guardar FECHA en curso_certificaciones
            if (!empty($v['FechaRegistroSTPS'])) {
                CursoCertificacion::updateOrCreate(
                    ['curso_id' => $curso->id, 'tipo_certificacion' => 'fecha_registro'],
                    ['fecha_registro' => $v['FechaRegistroSTPS']]
                );
            } else {
                $curso->certificaciones()->where('tipo_certificacion', 'fecha_registro')->delete();
            }

            // Guardar certificaciones
            $certificacionesMap = [
                'dc5' => ['nombre' => $v['FormatoDC5'] ?? null, 'tiene_firma' => $dc5Firma],
                'certificado_comprobacion' => [
                    'nombre' => $v['CertificadoComprobacion'] ?? null,
                    'drive_url' => $v['DriveCertificadoComprobacion'] ?? null
                ],
                'carta_poder' => [
                    'drive_url' => $v['DriveCartaPoder'] ?? null,
                    'tiene_firma' => $cartaFirma
                ],
            ];

            foreach ($certificacionesMap as $tipo => $data) {
                if (!empty($data['nombre']) || !empty($data['drive_url']) || isset($data['tiene_firma'])) {
                    CursoCertificacion::updateOrCreate(
                        ['curso_id' => $curso->id, 'tipo_certificacion' => $tipo],
                        $data
                    );
                }
            }

            // Guardar archivos
            $this->guardarArchivosCurso($curso, $request, [
                'archivoDC5' => 'dc5_archivo',
                'archivoCertificado' => 'certificado_archivo',
                'archivoCartaPoder' => 'carta_poder_archivo',
            ]);
        }

        // Guardar UDEMY
        if ($udemyBool) {
            CursoRecurso::updateOrCreate(
                ['curso_id' => $curso->id, 'tipo_recurso' => 'udemy'],
                ['url' => 'https://www.udemy.com/']
            );
        } else {
            $curso->recursos()->where('tipo_recurso', 'udemy')->delete();
        }

        CourseActionLog::create([
            'curso_id' => $curso->id,
            'nombre_curso' => $curso->nombre,
            'user_id' => Auth::id(),
            'accion' => 'Finalizado',
            'detalles' => 'Proceso de creación de curso completado.',
            'fecha_accion' => now(),
        ]);

        session()->forget(['curso_id', 'cursos_paso1', 'cursos_paso2', 'cursos_paso3', 'cursos_paso4', 'cursos_paso5', 'cursos_paso6']);
        return redirect()->route('cursos.index')->with('success', 'Curso creado con éxito.');
    }

    // ============================================
    // EDICIÓN POR PASOS
    // ============================================
    public function editPaso(Cursos $curso, $paso)
    {
        $recursos = [];
        $evaluaciones = [];
        $certificaciones = [];
        $fechaRegistro = null;
        
        $recursosDb = $curso->recursos()->get()->keyBy('tipo_recurso');
        $evaluacionesDb = $curso->evaluaciones()->get()->keyBy('tipo_evaluacion');
        $certificacionesDb = $curso->certificaciones()->get()->keyBy('tipo_certificacion');
        
        switch($paso) {
            case 1:
                return view('cursos.edit-paso1', compact('curso'));
                
            case 2:
                return view('cursos.edit-paso2', compact('curso'));
                
            case 3:
                $recursos['sin_fecha'] = $recursosDb->get('sin_fecha');
                $recursos['facebook'] = $recursosDb->get('facebook');
                $recursos['linkedin'] = $recursosDb->get('linkedin');
                $recursos['instagram'] = $recursosDb->get('instagram');
                $recursos['sin_fecha_archivo'] = $recursosDb->get('sin_fecha_archivo');
                $recursos['facebook_archivo'] = $recursosDb->get('facebook_archivo');
                $recursos['linkedin_archivo'] = $recursosDb->get('linkedin_archivo');
                $recursos['instagram_archivo'] = $recursosDb->get('instagram_archivo');
                return view('cursos.edit-paso3', compact('curso', 'recursos'));
                
            case 4:
                $recursos['temario'] = $recursosDb->get('temario');
                $recursos['itinerario'] = $recursosDb->get('itinerario');
                $recursos['planeacion'] = $recursosDb->get('planeacion');
                $recursos['temario_archivo'] = $recursosDb->get('temario_archivo');
                $recursos['itinerario_archivo'] = $recursosDb->get('itinerario_archivo');
                $recursos['planeacion_archivo'] = $recursosDb->get('planeacion_archivo');
                return view('cursos.edit-paso4', compact('curso', 'recursos'));
                
            case 5:
                $recursos['digital'] = $recursosDb->get('digital');
                $recursos['presentacion'] = $recursosDb->get('presentacion');
                $recursos['impreso'] = $recursosDb->get('impreso');
                $recursos['digital_archivo'] = $recursosDb->get('digital_archivo');
                $recursos['presentacion_archivo'] = $recursosDb->get('presentacion_archivo');
                $recursos['impreso_archivo'] = $recursosDb->get('impreso_archivo');
                return view('cursos.edit-paso5', compact('curso', 'recursos'));
                
            case 6:
                $recursos['presentacion'] = $recursosDb->get('presentacion');
                $recursos['presentacion_archivo'] = $recursosDb->get('presentacion_archivo');
                $evaluaciones['diagnostica'] = $evaluacionesDb->get('diagnostica');
                $evaluaciones['satisfaccion'] = $evaluacionesDb->get('satisfaccion');
                $evaluaciones['final'] = $evaluacionesDb->get('final');
                $estadoDc3 = $curso->estadoDc3();
                return view('cursos.edit-paso6', compact('curso', 'recursos', 'evaluaciones', 'estadoDc3'));
                
            case 7:
                return view('cursos.edit-paso7', array_merge(['curso' => $curso], $this->datosPaso7($curso)));
                
            default:
                return redirect()->route('cursos.index');
        }
    }

    public function updatePaso(Request $request, Cursos $curso, $paso)
    {
        if ($paso == 1) {
            $request->validate([
                'Nomenclatura' => 'required|string|max:255|unique:cursos,nomenclatura,' . $curso->id,
                'NombredelCurso' => 'required|string|max:255',
            ]);
            
            $data = [
                'nomenclatura' => $request->Nomenclatura,
                'nombre' => $request->NombredelCurso ?? '',
                'descripcion' => $request->DescripciondeCurso ?? '',
                'costo' => $request->CostodelCurso ?? 0,
                'instructor_responsable' => $request->InstructorResponsable ?? '',
                'fecha_inicio' => $request->FechadeInicio ?? null,
                'fecha_termino' => $request->FechadeTermino ?? null,
                'duracion' => $request->Duracioncurso ?? '',
            ];
            $curso->update($data);
            app(DriveCarpetas::class)->asegurarCarpetaCurso($curso);
        }

        if ($paso == 2) {
            $request->validate([
                'modalidad' => 'nullable|in:virtual,presencial,mixto',
                'sin_fecha' => 'nullable|boolean'
            ]);
            
            $curso->update([
                'modalidad' => $request->modalidad ?? null,
                'sin_fecha' => $request->sin_fecha ?? false,
            ]);

            if ($request->sin_fecha) {
                $curso->update(['fecha_inicio' => null, 'fecha_termino' => null]);
            }

            if (!empty($request->modalidad)) {
                CursoModalidad::updateOrCreate(
                    ['curso_id' => $curso->id, 'modalidad' => $request->modalidad],
                    ['curso_id' => $curso->id, 'modalidad' => $request->modalidad]
                );
            } else {
                $curso->modalidades()->delete();
            }
        }

        if ($paso == 3) {
            $recursosMap = [
                'sin_fecha' => ['url' => $request->SinFecha, 'drive_url' => $request->DriveSinFecha],
                'facebook' => ['url' => $request->Facebook, 'drive_url' => $request->DriveFacebook],
                'linkedin' => ['url' => $request->Linkedin, 'drive_url' => $request->DriveLinkedin],
                'instagram' => ['url' => $request->Instagram, 'drive_url' => $request->DriveInstagram],
            ];

            foreach ($recursosMap as $tipo => $data) {
                if (!empty($data['url']) || !empty($data['drive_url'])) {
                    CursoRecurso::updateOrCreate(
                        ['curso_id' => $curso->id, 'tipo_recurso' => $tipo],
                        $data
                    );
                } else {
                    $curso->recursos()->where('tipo_recurso', $tipo)->delete();
                }
            }

            $this->guardarArchivosCurso($curso, $request, [
                'archivoSinFecha' => 'sin_fecha_archivo',
                'archivoFacebook' => 'facebook_archivo',
                'archivoLinkedIn' => 'linkedin_archivo',
                'archivoInstagram' => 'instagram_archivo',
            ]);
        }

        if ($paso == 4) {
            $recursosMap = [
                'temario' => ['url' => $request->Temario, 'drive_url' => $request->DriveTemario],
                'itinerario' => ['url' => $request->Itinerario, 'drive_url' => $request->DriveItinerario],
                'planeacion' => ['url' => $request->Planeacion, 'drive_url' => $request->DrivePlaneacion],
            ];

            foreach ($recursosMap as $tipo => $data) {
                if (!empty($data['url']) || !empty($data['drive_url'])) {
                    CursoRecurso::updateOrCreate(
                        ['curso_id' => $curso->id, 'tipo_recurso' => $tipo],
                        $data
                    );
                } else {
                    $curso->recursos()->where('tipo_recurso', $tipo)->delete();
                }
            }

            $this->guardarArchivosCurso($curso, $request, [
                'archivoTemario' => 'temario_archivo',
                'archivoItinerario' => 'itinerario_archivo',
                'archivoPlaneacion' => 'planeacion_archivo',
            ]);
        }

        if ($paso == 5) {
            $recursosMap = [
                'digital' => ['url' => $request->Digital, 'drive_url' => $request->DriveDigital],
                'impreso' => ['url' => $request->Impreso_Presentable, 'drive_url' => $request->DriveImpreso],
            ];

            foreach ($recursosMap as $tipo => $data) {
                if (!empty($data['url']) || !empty($data['drive_url'])) {
                    CursoRecurso::updateOrCreate(
                        ['curso_id' => $curso->id, 'tipo_recurso' => $tipo],
                        $data
                    );
                } else {
                    $curso->recursos()->where('tipo_recurso', $tipo)->delete();
                }
            }

            $this->guardarArchivosCurso($curso, $request, [
                'archivoDigital' => 'digital_archivo',
                'archivoImpreso' => 'impreso_archivo',
            ]);
        }

        if ($paso == 6) {
            // Guardar Presentacion
            if (!empty($request->Presentacion) || !empty($request->DrivePresentacion)) {
                CursoRecurso::updateOrCreate(
                    ['curso_id' => $curso->id, 'tipo_recurso' => 'presentacion'],
                    ['url' => $request->Presentacion, 'drive_url' => $request->DrivePresentacion]
                );
            } else {
                $curso->recursos()->where('tipo_recurso', 'presentacion')->delete();
            }

            // Guardar archivo de presentacion
            if ($request->hasFile('archivoPresentacion') && $request->file('archivoPresentacion')->isValid()) {
                $file = $request->file('archivoPresentacion');
                $fileName = $this->nombreArchivoCurso($file, 'archivoPresentacion');
                $cursoPath = storage_path('app/public/cursos/' . $curso->id);
                if (!file_exists($cursoPath)) {
                    mkdir($cursoPath, 0777, true);
                }
                $file->move($cursoPath, $fileName);
                $rutaPublica = 'cursos/' . $curso->id . '/' . $fileName;
                
                CursoRecurso::updateOrCreate(
                    ['curso_id' => $curso->id, 'tipo_recurso' => 'presentacion_archivo'],
                    ['url' => $rutaPublica]
                );
            }

            // Guardar evaluaciones
            $evaluacionesMap = [
                'diagnostica' => [
                    'url' => $request->EvaluacionDiagnostica,
                    'drive_url' => $request->DriveEvaluacionDiagnostica,
                ],
                'satisfaccion' => [
                    'url' => $request->EvaluacionSatisfaccion,
                    'drive_url' => $request->DriveEvaluacionSatisfaccion,
                ],
                'final' => [
                    'url' => $request->EvaluacionFinal,
                    'drive_url' => $request->DriveEvaluacionFinal,
                ],
            ];

            foreach ($evaluacionesMap as $tipo => $data) {
                if (!empty($data['url']) || !empty($data['drive_url'])) {
                    CursoEvaluacion::updateOrCreate(
                        ['curso_id' => $curso->id, 'tipo_evaluacion' => $tipo],
                        $data
                    );
                } else {
                    $curso->evaluaciones()->where('tipo_evaluacion', $tipo)->delete();
                }
            }

            $this->guardarExcepcionDc3($curso, $request->boolean('DC3NoOtorga'));
        }

        if ($paso == 7) {
            $dc5Firma = ($request->FormatoDC5TieneFirma ?? 'No') === 'Si';
            $cartaFirma = ($request->CartaPoderTieneFirma ?? 'No') === 'Si';
            $udemyBool = ($request->UDEMY ?? 'No se ha prellenado') === 'Prellenado';

            // Si el subcurso hereda STPS del curso inicial, sólo se guarda UDEMY
            if (!$curso->heredaStps()) {
                // Guardar FECHA en curso_certificaciones
                if (!empty($request->FechaRegistroSTPS)) {
                    CursoCertificacion::updateOrCreate(
                        ['curso_id' => $curso->id, 'tipo_certificacion' => 'fecha_registro'],
                        ['fecha_registro' => $request->FechaRegistroSTPS]
                    );
                } else {
                    $curso->certificaciones()->where('tipo_certificacion', 'fecha_registro')->delete();
                }

                $certificacionesMap = [
                    'dc5' => ['nombre' => $request->FormatoDC5, 'tiene_firma' => $dc5Firma],
                    'certificado_comprobacion' => [
                        'nombre' => $request->CertificadoComprobacion,
                        'drive_url' => $request->DriveCertificadoComprobacion
                    ],
                    'carta_poder' => [
                        'drive_url' => $request->DriveCartaPoder,
                        'tiene_firma' => $cartaFirma
                    ],
                ];

                foreach ($certificacionesMap as $tipo => $data) {
                    if (!empty($data['nombre']) || !empty($data['drive_url']) || isset($data['tiene_firma'])) {
                        CursoCertificacion::updateOrCreate(
                            ['curso_id' => $curso->id, 'tipo_certificacion' => $tipo],
                            $data
                        );
                    } else {
                        $curso->certificaciones()->where('tipo_certificacion', $tipo)->delete();
                    }
                }

                $this->guardarArchivosCurso($curso, $request, [
                    'archivoDC5' => 'dc5_archivo',
                    'archivoCertificado' => 'certificado_archivo',
                    'archivoCartaPoder' => 'carta_poder_archivo',
                ]);
            }

            if ($udemyBool) {
                CursoRecurso::updateOrCreate(
                    ['curso_id' => $curso->id, 'tipo_recurso' => 'udemy'],
                    ['url' => 'https://www.udemy.com/']
                );
            } else {
                $curso->recursos()->where('tipo_recurso', 'udemy')->delete();
            }
        }

        return redirect()->route('cursos.edit', $curso->id)->with('success', "Paso $paso actualizado.");
    }

    // ============================================
    // PROGRESO
    // ============================================
    private function calcularProgresoPaso(Cursos $curso): array
    {
        $progreso = [];
        
        $pasosCampos = [
            1 => ['nomenclatura', 'nombre', 'descripcion', 'costo', 'instructor_responsable', 'fecha_inicio', 'fecha_termino', 'duracion'],
            2 => ['modalidad'],
            3 => ['recursos' => ['sin_fecha', 'facebook', 'linkedin', 'instagram']],
            4 => ['recursos' => ['temario', 'itinerario', 'planeacion']],
            5 => ['recursos' => ['digital', 'presentacion', 'impreso']],
            6 => ['evaluaciones' => ['diagnostica', 'satisfaccion', 'final'], 'recursos' => ['presentacion'], 'certificaciones' => ['dc3']],
            7 => ['certificaciones' => ['fecha_registro', 'dc5', 'certificado_comprobacion', 'carta_poder'], 'recursos' => ['udemy']],
        ];

        foreach ($pasosCampos as $paso => $campos) {
            $llenos = 0;
            $total = 0;

            foreach ($campos as $key => $campo) {
                if (is_array($campo)) {
                    foreach ($campo as $subCampo) {
                        $total++;
                        if ($this->campoEstaLleno($curso, $key, $subCampo)) {
                            $llenos++;
                        }
                    }
                } else {
                    $total++;
                    if ($this->campoDirectoLleno($curso, $campo)) {
                        $llenos++;
                    }
                }
            }

            $porcentaje = ($total > 0) ? round(($llenos / $total) * 100) : 0;
            
            if ($porcentaje == 100) {
                $progreso[$paso] = ['class' => 'btn-success', 'texto' => 'Completado'];
            } elseif ($porcentaje >= 30) {
                $progreso[$paso] = ['class' => 'btn-warning', 'texto' => 'En progreso'];
            } else {
                $progreso[$paso] = ['class' => 'btn-danger', 'texto' => 'Incompleto'];
            }
        }
        
        return $progreso;
    }

    private function campoDirectoLleno($curso, $campo)
    {
        $valor = $curso->$campo;
        // Un curso gratuito tiene el costo capturado, aunque sea cero.
        if ($campo === 'costo') {
            return $valor !== null && $valor !== '';
        }
        
        if (in_array($campo, ['sin_fecha'])) {
            return $valor == 1 || $valor === true;
        }
        if (in_array($campo, ['fecha_inicio', 'fecha_termino'])) {
            return !empty($valor) && $valor !== '0000-00-00';
        }
        if ($campo === 'modalidad') {
            return !empty($valor);
        }
        return !empty($valor) && $valor !== '' && $valor !== null && $valor !== '0';
    }

    private function campoEstaLleno($curso, $tipo, $subCampo)
    {
        if ($tipo === 'recursos') {
            $recurso = $curso->recursos()->where('tipo_recurso', $subCampo)->first();
            return $recurso && (!empty($recurso->url) || !empty($recurso->drive_url));
        }
        if ($tipo === 'evaluaciones') {
            $evaluacion = $curso->evaluaciones()->where('tipo_evaluacion', $subCampo)->first();
            return $evaluacion && !empty($evaluacion->url);
        }
        if ($tipo === 'certificaciones') {
            // La documentación STPS heredada cuenta como capturada en el subcurso
            if (in_array($subCampo, Cursos::STPS_CERTIFICACIONES, true)) {
                $curso = $curso->cursoStps();
            }
            $certificacion = $curso->certificaciones()->where('tipo_certificacion', $subCampo)->first();
            if ($subCampo === 'dc3') {
                // El DC3 se calcula: cuenta como completo cuando ya no está pendiente
                return $curso->estadoDc3()['estado'] !== Cursos::DC3_PENDIENTE;
            }
            return $certificacion && (!empty($certificacion->nombre) || !empty($certificacion->drive_url) || !empty($certificacion->fecha_registro));
        }
        return false;
    }

    // ============================================
    // FINALIZACIÓN FORZADA
    // ============================================
    public function finalizacionForzada(Request $request)
    {
        $curso = Cursos::find(session('curso_id'));
        
        if ($curso) {
            // Verificar que tenga al menos Nomenclatura y Nombre
            if (empty($curso->nomenclatura) || empty($curso->nombre)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El curso debe tener al menos Nomenclatura y Nombre para finalizar.'
                ], 400);
            }
            
            // Guardar log
            CourseActionLog::create([
                'curso_id' => $curso->id, 
                'nombre_curso' => $curso->nombre, 
                'user_id' => Auth::id(), 
                'accion' => 'Forzado', 
                'detalles' => 'Finalización forzada del curso.',
                'fecha_accion' => now()
            ]);
            
            // Limpiar sesión
            session()->forget(['curso_id', 'cursos_paso1', 'cursos_paso2', 'cursos_paso3', 'cursos_paso4', 'cursos_paso5', 'cursos_paso6']);
            
            return response()->json([
                'success' => true,
                'message' => 'Curso finalizado correctamente'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Curso no encontrado'
        ], 404);
    }

    // ============================================
    // OTROS MÉTODOS
    // ============================================
    public function getFechaInicio($id) 
    { 
        return response()->json(['fecha_inicio' => Cursos::findOrFail($id)->fecha_inicio]); 
    }
    
    public function exportarExcel() 
    { 
        return Excel::download(new CursosExport(Cursos::where('status', 1)->get()), 'cursos.xlsx'); 
    }
    
    public function exportarCsv() 
    { 
        return Excel::download(new CursosExport(Cursos::where('status', 1)->get()), 'cursos.csv'); 
    }
    
    public function rutas() 
    { 
        return view('cursos.ruta'); 
    }
    
    public function prepararRutaSubcurso() 
    { 
        return back()->with('error', 'Función no disponible en el hosting actual.'); 
    }
}
