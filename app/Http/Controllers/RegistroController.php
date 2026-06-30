<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participantes;
use App\Models\Cursos;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\DB;

class RegistroController extends Controller
{
    public function index()
    {
        $cursos = Cursos::all();
        return view('registro.index', compact('cursos'));
    }

    public function store(Request $request)
    {
        $rules = [
            'NombredelPostulante' => 'required|string|max:255',
            'Correo' => 'required|email|max:255|unique:participantes,correo',
            'Telefono' => 'required|string|size:10',
            'Edad' => 'required|integer|min:18|max:90',
            'Direccion' => 'required|string|max:255',
            'Escolaridad' => 'required|string|max:255',
            'Curp' => 'required|string|size:18|unique:participantes,curp',
            'RazónSocial' => 'required|string|max:200',
            'Empresa' => 'required|string|max:255',
            'RFCEmpresa' => 'required|string|max:100',
            'Ocupacion' => 'required|string|max:255',
            'Puesto' => 'required|string|max:255',
            'EstadoDePago' => 'required|string|max:255',
            'cursos' => 'required|array|min:1',
        ];

        $messages = [
            'Edad.min' => 'La edad mínima permitida es de 18 años.',
            'Edad.max' => 'La edad máxima permitida es de 90 años.',
            'Telefono.size' => 'El teléfono debe tener exactamente 10 dígitos.',
            'Curp.size' => 'La CURP debe tener exactamente 18 caracteres.',
            'Curp.unique' => 'Esta CURP ya está registrada.',
        ];

    if (in_array($request->EstadoDePago, ['Pagado', 'Anticipo'])) {
        $rules['Pago'] = ['required', 'numeric', 'min:0'];
    } else {
        $rules['Pago'] = 'nullable|numeric|min:0';
    }

    $validated = $request->validate($rules, $messages);

    // Mapear datos a nombres de columna reales
    $participante = new Participantes();
    $participante->nombre = $validated['NombredelPostulante'];
    $participante->correo = $validated['Correo'];
    $participante->telefono = $validated['Telefono'];
    $participante->edad = $validated['Edad'];
    $participante->direccion = $validated['Direccion'];
    $participante->escolaridad = $validated['Escolaridad'];
    $participante->curp = $validated['Curp'];
    $participante->razon_social = $validated['RazónSocial'];
    $participante->empresa = $validated['Empresa'];
    $participante->rfc_empresa = $validated['RFCEmpresa'];
    $participante->puesto = $validated['Puesto'];
    $participante->ocupacion = $validated['Ocupacion'];
    $participante->estado_pago = $validated['EstadoDePago'];
    $participante->pago = $request->Pago ?? 0;
    $participante->estatus = 1;

    // Nota: La columna 'N' no existe en la base de datos según el esquema actual.
    // Si necesitas guardar el código IC-XXXX, deberías añadir la columna 'N' a la tabla.
    // Por ahora, usaremos el ID autoincremental para las inscripciones.

    $inscripciones = [];
    foreach ($request->cursos as $curso_id) {
        $curso = Cursos::findOrFail($curso_id);
        $participante->fecha_curso = $curso->fecha_inicio;

        $inscripciones[] = [
            'curso_id' => $curso_id
        ];
    }

    $participante->save();

    foreach ($inscripciones as $inscripcionData) {
        $inscripcion = new Inscripcion();
        $inscripcion->participante_id = $participante->id;
        $inscripcion->curso_id = $inscripcionData['curso_id'];
        $inscripcion->save();
    }

    return redirect()->route('participantes.index')->with('success', 'Participante registrado exitosamente.');
   }

}
