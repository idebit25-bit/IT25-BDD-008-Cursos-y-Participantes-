@extends('home')
@section('title', '- Papelera de Usuarios')

@section('content')
<style>
    .users-container {
        padding: 40px 20px;
    }
    .table-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .table thead th {
        background-color: #212529 !important;
        color: white !important;
        padding: 15px;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
    }
    .table td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f5;
    }
    
    /* Botones uniformes */
    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
        height: 34px;
        min-width: 120px;
        margin: 2px;
        border: none;
        color: white !important;
    }
    .btn-action:hover {
        transform: translateY(-1px);
        opacity: 0.9;
    }

    .btn-success-modern { background-color: #28a745; }
    .btn-dark-modern { background-color: #212529; }
    .btn-secondary-modern { background-color: #6c757d; }

    tr.inactive-row {
        background-color: #fdfdfd;
    }
    .puesto-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        background-color: #fff1f1;
        border: 1px solid #ffcccc;
        color: #c82333;
    }
</style>

<div class="container-fluid users-container">
    <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h1 class="h3 mb-0" style="font-weight: 300; color: #0a0a0a;">Papelera de Usuarios</h1>
            <a href="{{ route('users.index') }}" class="btn-action btn-secondary-modern shadow-sm" style="min-width: 180px; height: 40px;">
                <i class="fas fa-arrow-left me-2"></i> Volver a Usuarios
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Puesto</th>
                        <th>Fecha Baja</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="inactive-row">
                            <td class="text-center text-muted">#{{ $user->id }}</td>
                            <td class="fw-bold">{{ $user->name }} {{ $user->apellido }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="puesto-badge">{{ $user->puesto }}</span></td>
                            <td class="small text-muted">{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="d-flex justify-content-center flex-wrap">
                                    <form action="{{ route('users.activar', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-action btn-success-modern" title="Reactivar">
                                            <i class="fas fa-undo me-1"></i> Reactivar
                                        </button>
                                    </form>

                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿ELIMINAR DEFINITIVAMENTE? Esta acción no se puede deshacer.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action btn-dark-modern" title="Borrar Definitivamente">
                                            <i class="fas fa-trash-alt me-1"></i> Borrar Def.
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">La papelera se encuentra vacía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
