@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalles del Registro</h1>

    <div>
        <strong>ID:</strong> {{ $log->id }}
    </div>
    <div>
        <strong>Nombre del Curso:</strong> {{ $log->nombre_curso }}
    </div>
    <div>
        <strong>Acción:</strong> {{ $log->accion }}
    </div>
    <div>
        <strong>Usuario:</strong> {{ $log->user?->name }}
    </div>
    <div>
        <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($log->fecha_accion)->format('d/m/Y h:i A') }}
    </div>
    <div>
        <strong>Detalles:</strong> {{ $log->detalles }}
    </div>

    <a href="{{ route('configuraciones.course-action-logs.index') }}" class="btn btn-secondary mt-3">
        Volver al Listado
    </a>
</div>
@endsection
