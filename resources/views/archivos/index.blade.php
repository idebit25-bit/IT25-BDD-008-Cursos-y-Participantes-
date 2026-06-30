@php
    use Illuminate\Support\Facades\Storage;
@endphp
@extends('home')
@section('title', '- Ruta de Archivos')
@section('content')

<style>
    .container-custom {
        max-width: 1000px;
        margin: 20px auto;
        padding: 20px;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border: none;
        margin-bottom: 30px;
    }
    .card-header {
        background-color: #f8f9fa;
        font-weight: bold;
        border-bottom: 1px solid #eee;
        border-radius: 12px 12px 0 0 !important;
    }
    .section-title {
        color: #0d6efd;
        margin-bottom: 20px;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
    }

    /* Uniformidad de bordes redondeados sutiles */
    .btn, .form-control, .card, .card-header, .list-group-item {
        border-radius: 6px !important;
    }
</style>

<div class="container-custom py-4">
    <h1 class="text-center mb-4">Gestión de Archivos</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="show" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="show" aria-label="Close"></button>
        </div>
    @endif


    <!-- SECCIÓN : Gestor de Archivos Virtuales -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-file-alt me-2"></i> Gestor de Archivos del Servidor
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 border-end">
                    <h5 class="section-title">Subir Nuevo Archivo</h5>
                    <form action="{{ route('archivos.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="file" name="archivo" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fas fa-upload me-1"></i> Subir al Servidor</button>
                    </form>

                    <h5 class="section-title mt-4">Crear Nueva Carpeta</h5>
                    <form action="{{ route('archivos.create-folder') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="nombre_carpeta" class="form-control" placeholder="Nombre de la carpeta" required>
                        </div>
                        <button type="submit" class="btn btn-info text-white"><i class="fas fa-folder-plus me-1"></i> Crear Carpeta</button>
                    </form>
                </div>

                <div class="col-md-6 px-4">
                    <h5 class="section-title">Explorador de Archivos ({{ $carpeta }})</h5>
                    
                    <h6>Carpetas:</h6>
                    <div class="list-group mb-3">
                        @forelse ($carpetas as $f)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-folder text-warning me-2"></i>{{ basename($f) }}</span>
                                <div class="btn-group">
                                    <a href="{{ route('archivos.open-folder', ['carpeta' => basename($f)]) }}" class="btn btn-sm btn-outline-primary">Abrir</a>
                                    <form action="{{ route('archivos.delete-folder', ['carpeta' => basename($f)]) }}" method="POST" onsubmit="return confirm('¿Eliminar carpeta y todo su contenido?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="small text-muted">No hay carpetas.</p>
                        @endforelse
                    </div>

                    <h6>Archivos:</h6>
                    <div class="list-group">
                        @forelse ($archivos as $a)
                            @php
                                $extension = strtolower(pathinfo($a, PATHINFO_EXTENSION));
                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            @endphp
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    @if($isImage)
                                        <div class="me-3" style="width: 50px; height: 50px; overflow: hidden; border: 1px solid #ddd;">
                                            <img src="{{ Storage::url($a) }}" alt="Previsualización" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    @else
                                        <i class="fas fa-file-code text-secondary me-3" style="font-size: 1.5rem;"></i>
                                    @endif
                                    <span class="text-truncate" style="max-width: 180px;" title="{{ basename($a) }}">
                                        {{ basename($a) }}
                                    </span>
                                </div>
                                <div class="btn-group">
                                    <a href="{{ route('archivos.download', ['archivo' => basename($a)]) }}" class="btn btn-sm btn-outline-success" title="Descargar">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <form action="{{ route('archivos.delete', ['archivo' => basename($a)]) }}" method="POST" onsubmit="return confirm('¿Eliminar este archivo?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="small text-muted">No hay archivos en esta ubicación.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
