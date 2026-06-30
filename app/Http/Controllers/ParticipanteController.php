<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Participantes;
use App\Models\Cursos;
use App\Models\Inscripcion;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ParticipantesExport;
use Illuminate\Support\Facades\Log;
use App\Models\ParticipantActionLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ParticipanteController extends Controller
{
    /**
     * Mostrar la lista de participantes.
     */
public function index(Request $request)
{
    $cursos = Cursos::all();

    $query = Participantes::where('estatus', 1)->orderBy('id', 'asc');

    if ($request->has('search') && !empty($request->search)) {
        $query->where(function ($q) use ($request) {
            $q->where('nombre', 'like', '%' . $request->search . '%')
              ->orWhere('correo', 'like', '%' . $request->search . '%');
        });
    }

    // También puedes agregar otros filtros aquí si los tienes
    if ($request->filled('curso')) {
        $query->whereHas('cursos', function ($q) use ($request) {
            $q->where('cursos.id', $request->curso);
        });
    }

    if ($request->filled('estado_pago')) {
        $query->where('estado_pago', $request->estado_pago);
    }

    if ($request->filled('min_costo')) {
        $query->where('pago', '>=', $request->min_costo);
    }

    if ($request->filled('max_costo')) {
        $query->where('pago', '<=', $request->max_costo);
    }

    $participantes = $query->with('cursos')->paginate(10);

    return view('participantes.index', compact('cursos', 'participantes'));
}

public function papelera(Request $request)
{
    $cursos = Cursos::all();
    $query = Participantes::where('estatus', 0)->orderBy('updated_at', 'desc');

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('nombre', 'like', '%' . $request->search . '%')
              ->orWhere('correo', 'like', '%' . $request->search . '%');
        });
    }

    $participantes = $query->with('cursos')->paginate(10);
    return view('participantes.papelera', compact('cursos', 'participantes'));
}




    /**
     * Mostrar el formulario para crear un nuevo participante.
     */
    public function create()
    {
        // Obtener todos los cursos disponibles
        $cursos = Cursos::all();
        return view('participantes.create', compact('cursos'));
    }

    /**
     * Mostrar el formulario para editar un participante existente.
     */
    public function edit($id)
    {

        // Buscar el participante por su ID (clave primaria)
        $participante = Participantes::findOrFail($id);
        $cursos = Cursos::all(); // Obtener todos los cursos disponibles
        return view('participantes.edit', compact('participante', 'cursos'));
    }

    /**
     * Actualizar un participante existente.
     */
    public function update(Request $request, $id)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'N' => 'required|string|max:255', // Validar que N esté presente
            'NombredelPostulante' => 'required|string|max:255',
            'Correo' => 'required|email|max:255|unique:participantes,correo,' . $id,
            'Telefono' => 'required|string|size:10',
            'Edad' => 'required|integer|min:18|max:90',
            'Direccion' => 'required|string|max:255',
            'Escolaridad' => 'required|string|max:255',
            'Curp' => 'required|string|size:18|unique:participantes,curp,' . $id,
            'RazónSocial' => 'nullable|string|max:255',
            'Empresa' => 'required|string|max:255',
            'RFCEmpresa' => 'nullable|string|max:255',
            'Ocupacion' => 'nullable|string|max:255',
            'Puesto' => 'required|string|max:255',
            'Pago' => 'required|numeric|min:0',
            'EstadoDePago' => 'required|string|max:255',
            'FechadelCurso' => 'required|date',
            'cursos' => 'required|array|min:1',
        ], [
            'Edad.min' => 'La edad mínima permitida es de 18 años.',
            'Edad.max' => 'La edad máxima permitida es de 90 años.',
            'Telefono.size' => 'El teléfono debe tener exactamente 10 dígitos.',
            'Curp.size' => 'La CURP debe tener exactamente 18 caracteres.',
            'Curp.unique' => 'Esta CURP ya está registrada.',
        ]);

            Log::info('Datos validados:', $validated);  // Log para debug

            $participante = Participantes::findOrFail($id);

        // Mapear los datos validados a los nombres de columna reales de la base de datos
        $participanteData = [
            'nombre' => $validated['NombredelPostulante'],
            'correo' => $validated['Correo'],
            'telefono' => $validated['Telefono'],
            'edad' => $validated['Edad'],
            'direccion' => $validated['Direccion'],
            'escolaridad' => $validated['Escolaridad'],
            'curp' => $validated['Curp'],
            'razon_social' => $validated['RazónSocial'],
            'empresa' => $validated['Empresa'],
            'rfc_empresa' => $validated['RFCEmpresa'],
            'puesto' => $validated['Puesto'],
            'ocupacion' => $validated['Ocupacion'],
            'pago' => $validated['Pago'],
            'estado_pago' => $validated['EstadoDePago'],
            'fecha_curso' => $validated['FechadelCurso'],
            'N' => $validated['N'],
        ];

        // Actualizar los datos del participante
        $participante->update($participanteData);

        // Registrar la acción en el historial
        ParticipantActionLog::create([
            'participant_id' => $participante->id,
            'nombre_postulante' => $participante->nombre,
            'correo' => $participante->correo,
            'accion' => 'Editado',
            'user_id' => Auth::id(),
            'detalles' => 'Datos del participante actualizados.',
            'fecha_accion' => now(),
        ]);

        // Actualizar los cursos asociados al participante
        $participante->cursos()->sync($validated['cursos']);

        // Redireccionar con mensaje de éxito
        return redirect()
    ->route('participantes.index')
    ->with('success', 'Participante registrado exitosamente.');

}
    /**
     * Guardar un nuevo participante en la base de datos.
     */
    public function store(Request $request)
    {
     $validated = $request->validate([
         'N' => 'required|string|max:255',
         'NombredelPostulante' => 'required|string|max:255',
         'Correo' => 'required|email|max:255|unique:participantes,correo',
         'Telefono' => 'required|string|size:10', // Forzar exactamente 10
         'Edad' => 'required|integer|min:18|max:90',
         'Direccion' => 'required|string|max:255',
         'Escolaridad' => 'required|string|max:255',
         'Curp' => 'required|string|size:18|unique:participantes,curp',
         'RazónSocial' => 'nullable|string|max:255',
         'Empresa' => 'required|string|max:255',
         'RFCEmpresa' => 'nullable|string|max:255',
         'Puesto' => 'required|string|max:255',
         'Ocupacion' => 'nullable|string|max:255',
         'Pago' => 'required|numeric|min:0',
         'EstadoDePago' => 'required|string|max:255',
         'FechadelCurso' => 'required|date',
         'cursos' => 'required|array|min:1',
     ], [
         'Edad.min' => 'La edad mínima permitida es de 18 años.',
         'Edad.max' => 'La edad máxima permitida es de 90 años.',
         'Telefono.size' => 'El teléfono debe tener exactamente 10 dígitos.',
         'Curp.size' => 'La CURP debe tener exactamente 18 caracteres.',
         'Curp.unique' => 'Esta CURP ya está registrada.',
     ]);

    // Mapear los datos validados a los nombres de columna reales de la base de datos
    $participanteData = [
        'nombre' => $validated['NombredelPostulante'],
        'correo' => $validated['Correo'],
        'telefono' => $validated['Telefono'],
        'edad' => $validated['Edad'],
        'direccion' => $validated['Direccion'],
        'escolaridad' => $validated['Escolaridad'],
        'curp' => $validated['Curp'],
        'razon_social' => $validated['RazónSocial'],
        'empresa' => $validated['Empresa'],
        'rfc_empresa' => $validated['RFCEmpresa'],
        'puesto' => $validated['Puesto'],
        'ocupacion' => $validated['Ocupacion'],
        'pago' => $validated['Pago'] ?? 0,
        'estado_pago' => $validated['EstadoDePago'],
        'fecha_curso' => $validated['FechadelCurso'],
        'estatus' => 1,
        'N' => $validated['N'],
    ];

    // Guardar el participante
    $participante = Participantes::create($participanteData);

    // Registrar el log
    ParticipantActionLog::create([
        'participant_id' => $participante->id,
        'nombre_postulante' => $participante->nombre,
        'correo' => $participante->correo,
        'accion' => 'Creado',
        'user_id' => Auth::id(),
        'detalles' => 'Nuevo participante registrado.',
        'fecha_accion' => now(),
    ]);

    // Guardar las inscripciones
    foreach ($validated['cursos'] as $curso_id) {
        Inscripcion::create([
            'participante_id' => $participante->id,
            'curso_id' => $curso_id,
        ]);
    }

    return redirect()->route('participantes.index')
                     ->with('success', 'Participante registrado exitosamente.');
}

    /**
     * Eliminar un participante.
     */
    public function destroy($id)
    {
        $participante = Participantes::findOrFail($id);

        // Cambiar el estado a inactivo
        $participante->update(['estatus' => '0']);

        // Registrar la acción en el historial
        ParticipantActionLog::create([
            'participant_id' => $participante->id,
            'nombre_postulante' => $participante->nombre,
            'correo' => $participante->correo,
            'accion' => 'Eliminado',
            'user_id' => Auth::id(),
            'detalles' => 'Participante desactivado.',
            'fecha_accion' => now(),
        ]);

        return redirect()->route('participantes.index')->with('success', 'Participante enviado a la papelera.');
    }

    /**
     * Filtrar participantes según los criterios proporcionados.
     */
    public function filtrar(Request $request)
    {
        // Consulta base para participantes
        $query = Participantes::query();

        // Filtrar por Curso
        if ($request->has('curso') && $request->curso) {
            $query->whereHas('cursos', function ($q) use ($request) {
                $q->where('cursos.id', $request->curso);
            });
        }

        // Filtrar por Estado de Pago
        if ($request->has('estado_pago') && $request->estado_pago) {
            $query->where('estado_pago', $request->estado_pago);
        }

        // Filtrar por Rango de Costo (Mínimo)
        if ($request->has('min_costo') && $request->min_costo) {
            $minCosto = floatval(str_replace([',', '$'], '', $request->min_costo));
            $query->where('pago', '>=', $minCosto);
        }

        // Filtrar por Rango de Costo (Máximo)
        if ($request->has('max_costo') && $request->max_costo) {
            $maxCosto = floatval(str_replace([',', '$'], '', $request->max_costo));
            $query->where('pago', '<=', $maxCosto);
        }

        // Barra de Búsqueda General
        if ($request->has('busqueda') && $request->busqueda) {
            $busqueda = $request->busqueda;
            $query->where(function ($q) use ($busqueda) {
                $q->where('id', 'LIKE', '%' . $busqueda . '%')
                  ->orWhere('nombre', 'LIKE', '%' . $busqueda . '%')
                  ->orWhere('correo', 'LIKE', '%' . $busqueda . '%')
                  ->orWhere('telefono', 'LIKE', '%' . $busqueda . '%')
                  ->orWhere('curp', 'LIKE', '%' . $busqueda . '%')
                  ->orWhere('empresa', 'LIKE', '%' . $busqueda . '%');
            });
        }

        // Obtener resultados
        $participantes = $query->with('cursos')->get();

        // Obtener todos los cursos disponibles para el filtro
        $cursos = Cursos::all();

        // Pasar datos a la vista
        return view('participantes.index', compact('participantes', 'cursos'));
    }

    public function showDetails($id)
    {
        // Obtener el participante por su ID
        $participante = Participantes::with('cursos')->findOrFail($id);

        // Pasar los datos a la vista
        return view('participantes.detalles', compact('participante'));
    }

    public function downloadPdf($id)
    {
        // Obtener el participante por su ID
        $participante = Participantes::with('cursos')->findOrFail($id);

        // Cargar la vista PDF y pasar los datos
        $pdf = Pdf::loadView('participantes.pdf', compact('participante'));

        $filename = \Illuminate\Support\Str::slug($participante->NombredelPostulante ?: $participante->nombre) . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Obtener detalles de los cursos seleccionados (para AJAX).
     */
    public function getCursosDetalles(Request $request)
    {
        $ids = $request->input('ids', []);
        $cursos = Cursos::whereIn('id', $ids)->get(['id', 'nombre', 'fecha_inicio', 'fecha_termino', 'costo']);
        return response()->json($cursos);
    }

    public function exportarExcel()
    {
        $participantes = Participantes::with('cursos')->get();
        return Excel::download(new ParticipantesExport($participantes), 'participantes.xlsx');
    }

    public function exportarCsv()
    {
        $participantes = Participantes::with('cursos')->get();
        return Excel::download(new ParticipantesExport($participantes), 'participantes.csv');
    }

        public function activar($id)
        {
            try {
                $participante = Participantes::findOrFail($id);

                // Cambiar el estado a activo (estatus = 1)
                $participante->update(['estatus' => 1]);

                // Registrar la acción en el historial
                ParticipantActionLog::create([
                    'participant_id' => $participante->id,
                    'nombre_postulante' => $participante->nombre,
                    'correo' => $participante->correo,
                    'accion' => 'Activado',
                    'user_id' => Auth::id(),
                    'detalles' => 'Participante activado nuevamente.',
                    'fecha_accion' => now(),
                ]);

                return redirect()->route('participantes.index')->with('success', 'Participante activado exitosamente.');
            } catch (\Exception $e) {
                return back()->with('error', 'Error al activar el participante: ' . $e->getMessage());
            }
        }
        public function eliminarDefinitivo(Request $request, $id)
            {
                try {
                    // Validar la contraseña del usuario autenticado
                    if (!Hash::check($request->password, Auth::user()->password)) {
                        return back()->with('error', 'Contraseña incorrecta.');
                    }

                    // Buscar el participante por ID
                    $participante = Participantes::findOrFail($id);

                    // Guardar datos antes de eliminar para el log
                    $nombreLog = $participante->nombre;
                    $correoLog = $participante->correo;

                    // Eliminar el participante de la base de datos
                    $participante->delete();

                    // Registrar la acción en el historial
                    ParticipantActionLog::create([
                        'participant_id' => $id,
                        'nombre_postulante' => $nombreLog,
                        'correo' => $correoLog,
                        'accion' => 'Eliminado Definitivamente',
                        'user_id' => Auth::id(),
                        'detalles' => 'Participante eliminado definitivamente.',
                        'fecha_accion' => now(),
                    ]);

                    return redirect()->route('participantes.index')->with('success', 'Participante eliminado definitivamente.');
                } catch (\Exception $e) {
                    return back()->with('error', 'Error al eliminar el participante: ' . $e->getMessage());
                }
            }

}
