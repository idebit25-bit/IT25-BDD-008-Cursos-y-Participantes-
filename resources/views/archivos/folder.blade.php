@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Contenido de la Carpeta: {{ $carpeta }}</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Formulario para subir archivos -->
    <form action="{{ route('archivos.upload-to-folder', ['carpeta' => $carpeta]) }}" method="POST" enctype="multipart/form-data" class="mb-4">
        @csrf
        <div class="form-group">
            <input type="file" name="archivo" class="form-control-file" required>
        </div>
        <button type="submit" class="btn btn-primary">Subir Archivo</button>
    </form>

    <!-- Formulario para crear subcarpetas -->
    <form action="{{ route('archivos.create-subfolder', ['carpeta' => $carpeta]) }}" method="POST" class="mb-4">
        @csrf
        <div class="form-group">
            <input type="text" name="nombre_subcarpeta" class="form-control" placeholder="Nombre de la subcarpeta" required>
        </div>
        <button type="submit" class="btn btn-info">Crear Subcarpeta</button>
    </form>

    <!-- Lista de archivos -->
    <h2>Archivos</h2>
    @if (count($archivos) > 0)
        <div class="list-group mb-4">
            @foreach ($archivos as $archivo)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    @if (in_array(pathinfo($archivo, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        <img src="{{ Storage::url($archivo) }}" alt="{{ basename($archivo) }}" style="max-width: 100px; max-height: 100px;">
                    @else
                        <span>{{ basename($archivo) }}</span>
                    @endif
                    <div>
                        <a href="{{ route('archivos.download', ['archivo' => basename($archivo)]) }}" class="btn btn-success btn-sm">Descargar</a>
                        <form action="{{ route('archivos.delete', ['archivo' => basename($archivo)]) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p>No hay archivos en esta carpeta.</p>
    @endif

    <!-- Lista de subcarpetas -->
    <h2>Subcarpetas</h2>
    @if (count($subcarpetas) > 0)
        <div class="list-group mb-4">
            @foreach ($subcarpetas as $subcarpeta)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="{{ route('archivos.open-folder', ['carpeta' => rawurlencode($carpeta . '/' . basename($subcarpeta))]) }}">
                        {{ basename($subcarpeta) }}
                    </a>
                    <form action="{{ route('archivos.delete-folder', ['carpeta' => $carpeta . '/' . basename($subcarpeta)]) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                    </form>
                </div>
            @endforeach
        </div>
    @else
        <p>No hay subcarpetas en esta carpeta.</p>
    @endif

    <!-- Breadcrumb para navegar entre carpetas -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('ruta.archivos') }}">Inicio</a></li>
            @php
                $rutaActual = '';
                $partesRuta = explode('/', $carpeta);
            @endphp
            @foreach ($partesRuta as $parte)
                @php
                    $rutaActual .= $parte . '/';
                @endphp
                <li class="breadcrumb-item">
                    <a href="{{ route('archivos.open-folder', ['carpeta' => rtrim($rutaActual, '/')]) }}">
                        {{ $parte }}
                    </a>
                </li>
            @endforeach
        </ol>
    </nav>

    <!-- Botón para volver a la carpeta anterior -->
    @php
        $carpetaPadre = dirname($carpeta);
    @endphp
    @if ($carpetaPadre === null || $carpetaPadre === '.')
        <a href="{{ route('ruta.archivos') }}" class="btn btn-secondary">Volver</a>
    @else
        <a href="{{ route('archivos.open-folder', ['carpeta' => $carpetaPadre]) }}" class="btn btn-secondary">Volver</a>
    @endif
</div>

<script>
    document.querySelectorAll('a[href*="open-folder"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const path = this.getAttribute('href');
            window.location.href = decodeURIComponent(path);
        });
    });
</script>
@endsection
