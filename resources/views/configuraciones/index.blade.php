@extends('home')
@section('title', '- Configuraciones')
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
    h2 { text-align: center; margin-top: 40px; margin-bottom: 20px; }
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
        height: 38px;
        min-width: 130px;
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
        font-size: 13px;
        text-transform: uppercase;
    }

    /* Columnas uniformes */
    .col-id { width: 60px; text-align: center; }
    .col-accion { width: 130px; text-align: center; }
    .col-usuario { width: 160px; }
    .col-fecha { width: 160px; text-align: center; }
    .col-ops { width: 320px; text-align: center; }

    /* --- ESTILO MODERNO PARA LOGO --- */
    .logo-upload-container {
        background: white;
        border: 2px dashed #ddd;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        margin-bottom: 20px;
    }
    .logo-upload-container:hover {
        border-color: #007bff;
        background: #f8f9ff;
    }
    .logo-upload-container i {
        font-size: 40px;
        color: #007bff;
        margin-bottom: 10px;
    }
    .logo-upload-container input[type="file"] {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        opacity: 0;
        cursor: pointer;
    }
</style>

<div class="container">
    <h1>Configuraciones</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h2>Historial de Acciones de cursos</h2>
    <div class="text-center mb-4">
        <a href="/configuraciones/logs" class="btn btn-success" style="min-width: 250px;">
            <i class="fas fa-list me-2"></i> Ver todas las acciones de los cursos
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th>Curso</th>
                    <th class="col-accion">Acción</th>
                    <th class="col-usuario">Usuario</th>
                    <th class="col-fecha">Fecha</th>
                    <th class="col-ops">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td class="text-center text-muted">#{{ $log->id }}</td>
                        <td>{{ $log->nombre_curso }}</td>
                        <td class="text-center"><span class="badge bg-light text-dark border w-100" style="padding: 8px;">{{ $log->accion }}</span></td>
                        <td>{{ $log->user?->name ?: 'Sistema' }}</td>
                        <td class="text-center small">{{ \Carbon\Carbon::parse($log->fecha_accion)->format('d/m/Y H:i') }}</td>
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
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDelCourse{{ $log->id }}">Borrar Def.</button>
                                    @endif
                                @endif
                            </div>

                            @if($log->curso && $status === 0)
                                <div class="modal fade" id="confirmDelCourse{{ $log->id }}" tabindex="-1">
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
                @empty
                    <tr><td colspan="6" class="text-center py-4">No hay registros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h2>Historial de Acciones de Participantes</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th>Participante</th>
                    <th class="col-accion">Acción</th>
                    <th class="col-usuario">Usuario</th>
                    <th class="col-fecha">Fecha</th>
                    <th class="col-ops">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($participantLogs as $log)
                    <tr>
                        <td class="text-center text-muted">#{{ $log->id }}</td>
                        <td>{{ $log->nombre_postulante }}</td>
                        <td class="text-center"><span class="badge bg-light text-dark border w-100" style="padding: 8px;">{{ $log->accion }}</span></td>
                        <td>{{ $log->user?->name ?: 'Sistema' }}</td>
                        <td class="text-center small">{{ \Carbon\Carbon::parse($log->fecha_accion)->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="d-flex justify-content-center flex-wrap">
                                @if ($log->participant)
                                    <a href="{{ route('participantes.detalles', ['id' => $log->participant_id]) }}" class="btn btn-info" target="_blank">Detalles</a>
                                @else
                                    <span class="text-muted p-2 small">Eliminado</span>
                                @endif

                                @if ($log->accion === 'Eliminado' && isset($log->participant) && $log->participant->estatus == 0)
                                    <form action="{{ route('participantes.activar', ['id' => $log->participant_id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success">Activar</button>
                                    </form>
                                @endif

                                @if ($log->accion === 'Eliminado' && isset($log->participant))
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDelPart{{ $log->id }}">Borrar Def.</button>
                                    <div class="modal fade" id="confirmDelPart{{ $log->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header"><h5 class="modal-title">Seguridad</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                <form action="{{ route('participantes.eliminar-definitivo', ['id' => $log->participant_id]) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <div class="modal-body text-start">
                                                        <p>¿Eliminar permanentemente a <strong>{{ $log->nombre_postulante }}</strong>?</p>
                                                        <label class="mb-2">Contraseña:</label>
                                                        <input type="password" name="password" class="form-control" required>
                                                    </div>
                                                    <div class="modal-footer"><button type="submit" class="btn btn-danger">Confirmar</button></div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4">No hay registros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="row g-4 justify-content-center mb-5">
        <div class="col-md-4">
            <a href="{{ route('exportar.cursos') }}" class="btn btn-primary w-100" style="height: 45px;">
                <i class="fas fa-file-export me-2"></i> Exportar Cursos (SQL)
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('exportar.participantes') }}" class="btn btn-success w-100" style="height: 45px;">
                <i class="fas fa-file-export me-2"></i> Exportar Participantes (SQL)
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('abrir.vscode') }}" class="btn btn-info w-100" style="height: 45px;">
                <i class="fas fa-code me-2"></i> Abrir en VS Code
            </a>
        </div>
    </div>

    <h2>Configuración del Logo</h2>
    <div class="card p-4 shadow-sm mb-4">
        <form action="{{ route('configuraciones.updateLogo') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="logo-upload-container">
                <i class="fas fa-arrow-up"></i>
                <div class="fw-bold text-muted">Haz clic aquí o arrastra para subir el nuevo logo</div>
                <div class="small text-muted">(PNG, JPG, JPEG, GIF)</div>
                <input type="file" name="logo" required onchange="this.form.submit()">
            </div>
            <button type="submit" class="btn btn-primary w-100">Actualizar Logo</button>
        </form>
    </div>

    <h2>Seleccionar Logo desde la Lista</h2>
    <div class="card p-4 shadow-sm mb-5">
        <form action="{{ route('configuraciones.updateLogoFromList') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <select name="selected_logo" class="form-select">
                    @php
                        $logos = glob(storage_path('app/public/logos/*.{jpg,jpeg,png,gif}'), GLOB_BRACE);
                        $logoNames = array_map(fn($path) => basename($path), $logos);
                    @endphp
                    @foreach ($logoNames as $logo)
                        <option value="{{ $logo }}" {{ (isset($currentLogo) && strpos($currentLogo, $logo) !== false) ? 'selected' : '' }}>{{ $logo }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Aplicar Selección</button>
        </form>
    </div>
</div>
@endsection
