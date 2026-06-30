@extends('home')
@section('title', '- Editar Usuario')
@section('nav')

<style>
    .edit-container {
        margin-top: 100px;
        padding-bottom: 50px;
    }
    .edit-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .edit-header {
        background: linear-gradient(135deg, #000000 0%, #333333 100%);
        padding: 30px;
        color: white;
        position: relative;
    }
    .back-button {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: white;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        background: rgba(255,255,255,0.1);
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        text-decoration: none;
    }
    .back-button:hover {
        background: rgba(255,255,255,0.2);
        color: white;
        transform: translateY(-50%) translateX(-5px);
    }
    .form-section-title {
        font-size: 0.85rem;
        font-weight: bold;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
        color: #666;
    }
    .form-control {
        border-left: none;
        padding: 12px;
    }
    .form-control:focus {
        box-shadow: none;
        border-color: #ced4da;
    }
    .form-control[readonly] {
        background-color: #fcfcfc;
    }
    .editing-indicator {
        font-size: 0.75rem;
        font-weight: bold;
        background-color: #e3f2fd;
        color: #0d47a1;
        border: none;
    }
    .botones-container {
        padding: 20px 0;
        display: flex;
        justify-content: center;
        gap: 15px;
    }
    .btn-action {
        border-radius: 30px !important;
        padding: 10px 25px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }
</style>

<div class="container edit-container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card edit-card">
                <div class="edit-header text-center">
                    <a href="{{ auth()->id() == $user->id ? route('profile') : route('users.index') }}" class="back-button" title="Regresar">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="mb-0" style="font-weight: 300;">Editar Perfil</h2>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('users.update', $user->id) }}" id="editForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-12">
                                <h5 class="form-section-title">Información Personal</h5>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="name" class="form-label text-muted small fw-bold">Nombre</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required readonly>
                                    <span class="input-group-text d-none editing-indicator">Modificado</span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="apellido" class="form-label text-muted small fw-bold">Apellido</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                    <input type="text" name="apellido" id="apellido" class="form-control" value="{{ old('apellido', $user->apellido) }}" required readonly>
                                    <span class="input-group-text d-none editing-indicator">Modificado</span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="email" class="form-label text-muted small fw-bold">Correo Electrónico</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required readonly>
                                    <span class="input-group-text d-none editing-indicator">Modificado</span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="telefono" class="form-label text-muted small fw-bold">Teléfono</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono', $user->telefono) }}" readonly>
                                    <span class="input-group-text d-none editing-indicator">Modificado</span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="edad" class="form-label text-muted small fw-bold">Edad</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="number" name="edad" id="edad" class="form-control" value="{{ old('edad', $user->edad) }}" min="18" max="90" readonly>
                                    <span class="input-group-text d-none editing-indicator">Modificado</span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="puesto" class="form-label text-muted small fw-bold">Puesto</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                    <input type="text" name="puesto" id="puesto" class="form-control" value="{{ old('puesto', $user->puesto) }}" readonly>
                                    <span class="input-group-text d-none editing-indicator">Modificado</span>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <h5 class="form-section-title">Seguridad</h5>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="password" class="form-label text-muted small fw-bold">Nueva Contraseña</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Dejar en blanco para mantener" readonly>
                                    <span class="input-group-text d-none editing-indicator">Modificado</span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="password_confirmation" class="form-label text-muted small fw-bold">Confirmar Contraseña</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" readonly>
                                    <span class="input-group-text d-none editing-indicator">Modificado</span>
                                </div>
                            </div>
                        </div>

                        <div class="botones-container mt-4">
                            <button type="button" id="toggleEditButton" class="btn btn-primary btn-action shadow-sm">
                                <i class="fas fa-edit mr-2"></i> Editar
                            </button>
                            <button type="submit" class="btn btn-success btn-action shadow-sm" id="saveButton" disabled>
                                <i class="fas fa-save mr-2"></i> Guardar
                            </button>
                            <button type="button" id="cancelButton" class="btn btn-danger btn-action shadow-sm" disabled>
                                <i class="fas fa-times mr-2"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleEditButton = document.getElementById('toggleEditButton');
        const cancelButton = document.getElementById('cancelButton');
        const saveButton = document.getElementById('saveButton');
        const formInputs = document.querySelectorAll('#editForm .form-control');
        const originalValues = {};
        let isEditing = false;

        // Almacenar valores originales
        formInputs.forEach(input => {
            originalValues[input.id] = input.value;
        });

        // Función para habilitar edición
        toggleEditButton.addEventListener('click', function () {
            if (!isEditing) {
                // Habilitar edición
                formInputs.forEach(input => {
                    input.removeAttribute('readonly');
                    input.addEventListener('input', showEditingIndicator);
                    // Añadir un efecto visual suave al habilitar
                    input.parentElement.classList.add('border-primary');
                });
                toggleEditButton.style.display = 'none'; // Ocultar botón editar
                saveButton.removeAttribute('disabled');
                cancelButton.removeAttribute('disabled');
                isEditing = true;
            }
        });

        // Cancelar edición y restaurar valores originales
        cancelButton.addEventListener('click', function () {
            formInputs.forEach(input => {
                input.value = originalValues[input.id];
                input.setAttribute('readonly', true);
                hideEditingIndicator(input);
                input.parentElement.classList.remove('border-primary');
            });
            toggleEditButton.style.display = 'inline-block'; // Mostrar de nuevo botón editar
            saveButton.setAttribute('disabled', true);
            cancelButton.setAttribute('disabled', true);
            isEditing = false;
        });

        // Mostrar "Modificado" cuando el usuario altera un campo
        function showEditingIndicator(event) {
            const input = event.target;
            const indicator = input.nextElementSibling;
            if (indicator && indicator.classList.contains('editing-indicator')) {
                indicator.classList.remove('d-none');
            }
        }

        // Ocultar "Modificado" cuando se desactiva la edición
        function hideEditingIndicator(input) {
            const indicator = input.nextElementSibling;
            if (indicator && indicator.classList.contains('editing-indicator')) {
                indicator.classList.add('d-none');
            }
        }
    });
</script>
@endsection
