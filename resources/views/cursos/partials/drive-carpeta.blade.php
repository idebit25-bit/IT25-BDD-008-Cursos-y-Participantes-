{{-- Carpeta del curso en Drive (App\Services\DriveCarpetas). Se crea al guardar el paso 1. --}}
@php
    $driveCarpetas = app(\App\Services\DriveCarpetas::class);
    $puedeCrearCarpeta = auth()->user()->puesto !== 'Operacion' && $driveCarpetas->configurado();
@endphp

@if($curso->drive_carpeta_url)
    <a href="{{ $curso->drive_carpeta_url }}" target="_blank" rel="noopener" class="btn btn-outline-success shadow-sm">
        <i class="fab fa-google-drive me-2"></i> Carpeta en Drive
    </a>
@elseif($puedeCrearCarpeta)
    <form action="{{ route('cursos.drive.carpeta', $curso) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-outline-secondary shadow-sm" title="La carpeta no se pudo crear al registrar el curso">
            <i class="fab fa-google-drive me-2"></i> Crear carpeta en Drive
        </button>
    </form>
@endif
