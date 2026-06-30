@extends('home')
@section('title', '- Historial de Acciones')
@section('nav')

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<link rel="icon" type="image/x-icon" href="{{ asset('images/Logoibeb.ico') }}">

@section('content')
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<style>
    h1 { text-align: center; margin-bottom: 20px; }
    .container { max-width: 1200px; margin: 0 auto; padding: 20px; }

    /* --- ESTILOS DE BOTONES UNIFORMES --- */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        padding: 8px 20px;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease;
        border: none;
        height: 38px; /* Altura uniforme */
        min-width: 130px; /* Ancho mínimo para consistencia */
        margin: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        color: white !important;
        text-decoration: none !important;
    }

    .btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .btn-success { background-color: #28a745 !important; }
    .btn-danger { background-color: #dc3545 !important; }
    .btn-warning { background-color: #ffc107 !important; color: #212529 !important; }
    .btn-info { background-color: #17a2b8 !important; }
    .btn-primary { background-color: #007bff !important; }
    .btn-secondary { background-color: #6c757d !important; }

    table thead th {
        background-color: #000 !important;
        color: #fff !important;
        text-align: center;
        padding: 12px !important;
    }

    /* Personalización de DataTables para que coincida con el estilo */
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0;
        padding: 6px 12px;
        border: 1px solid #ddd;
        margin-left: 10px;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 0;
        padding: 4px 8px;
        border: 1px solid #ddd;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #000 !important;
        color: white !important;
        border-radius: 0;
        border: none;
    }
</style>

<div class="container">
    <h1 class="mt-4">Historial Completo de Acciones</h1>

    <!-- Botón de Regresar Centrado -->
    <div class="text-center mb-5">
        <a href="/configuraciones" class="btn btn-secondary" style="min-width: 250px; height: 45px;">
            <i class="fas fa-arrow-left me-2"></i> Volver a Configuración
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle" id="logsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Curso</th>
                    <th>Acción</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <td class="text-center">{{ $log->id }}</td>
                        <td class="fw-bold">{{ $log->nombre_curso }}</td>
                        <td class="text-center"><span class="badge bg-light text-dark border">{{ $log->accion }}</span></td>
                        <td>{{ $log->user?->name ?: 'Sistema' }}</td>
                        <td class="text-center small">{{ \Carbon\Carbon::parse($log->fecha_accion)->format('d/m/Y h:i A') }}</td>
                        <td>
                            <div class="d-flex justify-content-center flex-wrap">
                                @if ($log->curso)
                                    <a href="{{ route('cursos.show', $log->curso->id) }}" class="btn btn-info">Detalles</a>
                                @else
                                    <span class="text-muted p-2 small">Eliminado</span>
                                @endif

                                @php
                                    $accion = strtolower($log->accion);
                                    $status = $log->curso ? (int)$log->curso->status : null;
                                @endphp

                                @if (($accion === 'eliminado' || $accion === 'finalizado') && $log->curso)
                                    @if ($status === 0)
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDelLog{{ $log->id }}">Borrar Def.</button>
                                    @endif
                                @endif
                            </div>

                            @if($log->curso && $status === 0)
                                <div class="modal fade" id="confirmDelLog{{ $log->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header"><h5 class="modal-title">Seguridad</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                            <form action="{{ route('cursos.eliminar-definitivo', $log->curso->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <div class="modal-body text-start">
                                                    <p>¿Eliminar permanentemente <strong>{{ $log->nombre_curso }}</strong>?</p>
                                                    <label class="mb-2">Contraseña:</label>
                                                    <input type="password" name="password" class="form-control" required>
                                                </div>
                                                <div class="modal-footer"><button type="submit" class="btn btn-danger">Confirmar</button></div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#logsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            order: [[0, 'desc']],
            pageLength: 25,
            dom: '<"d-flex justify-content-between mb-3"lf>rt<"d-flex justify-content-between mt-3"ip>'
        });
    });
</script>
@endsection
