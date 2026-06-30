@extends('layouts.app')
@section('content')

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #dee2e6;
        border-radius: 6px !important;
        padding: 2px 5px;
        min-height: 40px;
        background-color: #fff;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #333 !important;
        border: none !important;
        color: white !important;
        border-radius: 4px !important;
        padding: 4px 10px 4px 25px !important;
        margin-top: 5px !important;
        position: relative !important;
        font-size: 0.85rem;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff !important;
        margin-right: 0 !important;
        position: absolute !important;
        left: 5px !important;
        border: none !important;
        background: transparent !important;
        font-weight: bold;
        top: 50%;
        transform: translateY(-50%);
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        background: transparent !important;
        color: #ff4d4d !important;
    }
    .select2-container--default .select2-search--inline .select2-search__field {
        margin-top: 5px !important;
    }
    .edit-container {
        padding: 50px 0;
    }
    .edit-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        background: white;
    }
    .edit-header {
        background: linear-gradient(135deg, #000000 0%, #333333 100%);
        padding: 30px;
        color: white;
        text-align: center;
        position: relative;
    }
    .edit-header h2 {
        font-weight: 300;
        letter-spacing: 2px;
    }
    .back-arrow {
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
    .back-arrow:hover {
        background: rgba(255,255,255,0.2);
        color: white;
        transform: translateY(-50%) translateX(-5px);
    }
    .form-section-title {
        font-size: 0.85rem;
        font-weight: bold;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        display: flex;
        align-items: center;
    }
    .form-section-title i {
        margin-right: 10px;
        color: #333;
    }
    .form-section-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 30px;
        border: 1px solid #e9ecef;
    }
    .form-label {
        font-weight: 700;
        color: #495057;
        font-size: 0.75rem;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    .input-group-text {
        background-color: #ffffff;
        color: #6c757d;
        border-right: none;
    }
    .form-control, .form-select {
        border-left: none;
        padding: 10px 15px;
    }
    .form-control[readonly], .form-select[disabled] {
        background-color: #fcfcfc;
        color: #6c757d;
    }
    .input-group:focus-within {
        box-shadow: 0 0 0 0.25rem rgba(0, 0, 0, 0.05);
        border-radius: 0.375rem;
    }
    .editing-indicator {
        font-size: 0.7rem;
        font-weight: bold;
        background-color: #e7f1ff !important;
        color: #0d6efd !important;
        border-left: 1px solid #dee2e6 !important;
    }
    .botones-container {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
    }
    .btn-action-edit {
        border-radius: 6px !important;
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
        <div class="col-lg-10">
            <div class="card edit-card">
                <div class="edit-header">
                    <a href="{{ route('participantes.index') }}" class="back-arrow" title="Regresar">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="mb-0">Editar Participante</h2>
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

                    <form action="{{ route('participantes.update', ['id' => $participante->id]) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')

                        <!-- Sección 1: Datos Personales -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="form-section-title"><i class="fas fa-id-card"></i> Información Personal</h5>
                                <div class="form-section-card shadow-sm">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label for="N" class="form-label">N° nomenclatura</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                <input type="text" class="form-control" id="N" name="N" value="{{ old('N', $participante->N) }}" required readonly>
                                                <span class="input-group-text d-none editing-indicator">Modificado</span>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <label for="NombredelPostulante" class="form-label">Nombre del Postulante</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input type="text" class="form-control" id="NombredelPostulante" name="NombredelPostulante" value="{{ old('NombredelPostulante', $participante->NombredelPostulante) }}" required readonly>
                                                <span class="input-group-text d-none editing-indicator">Modificado</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="Correo" class="form-label">Correo Electrónico</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                <input type="email" class="form-control" id="Correo" name="Correo" value="{{ old('Correo', $participante->Correo) }}" required readonly>
                                                <span class="input-group-text d-none editing-indicator">Modificado</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="Telefono" class="form-label">Teléfono</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                <input type="text" class="form-control" id="Telefono" name="Telefono" 
                                                       value="{{ old('Telefono', $participante->Telefono) }}" 
                                                       maxlength="10" pattern="\d{10}" title="El teléfono debe tener 10 dígitos numéricos" required readonly>
                                                <span class="input-group-text d-none editing-indicator">Modificado</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="Edad" class="form-label">Edad</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-birthday-cake"></i></span>
                                                <input type="number" class="form-control" id="Edad" name="Edad" value="{{ old('Edad', $participante->Edad) }}" min="18" max="90" required readonly>
                                                <span class="input-group-text d-none editing-indicator">Modificado</span>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="Direccion" class="form-label">Dirección</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                <input type="text" class="form-control" id="Direccion" name="Direccion" value="{{ old('Direccion', $participante->Direccion) }}" required readonly>
                                                <span class="input-group-text d-none editing-indicator">Modificado</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="Curp" class="form-label">CURP</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                                                <input type="text" class="form-control" id="Curp" name="Curp" 
                                                       value="{{ old('Curp', $participante->Curp) }}" 
                                                       maxlength="18" minlength="18" style="text-transform: uppercase;" required readonly>
                                                <span class="input-group-text d-none editing-indicator">Modificado</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="Escolaridad" class="form-label">Escolaridad</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                                                <select class="form-select" id="Escolaridad" name="Escolaridad" required disabled>
                                                    <option value="Primaria" {{ $participante->Escolaridad == 'Primaria' ? 'selected' : '' }}>Primaria</option>
                                                    <option value="Secundaria" {{ $participante->Escolaridad == 'Secundaria' ? 'selected' : '' }}>Secundaria</option>
                                                    <option value="Preparatoria" {{ $participante->Escolaridad == 'Preparatoria' ? 'selected' : '' }}>Preparatoria</option>
                                                    <option value="Licenciatura" {{ $participante->Escolaridad == 'Licenciatura' ? 'selected' : '' }}>Licenciatura</option>
                                                    <option value="Maestría" {{ $participante->Escolaridad == 'Maestría' ? 'selected' : '' }}>Maestría</option>
                                                    <option value="Doctorado" {{ $participante->Escolaridad == 'Doctorado' ? 'selected' : '' }}>Doctorado</option>
                                                    <option value="Otro" {{ !in_array($participante->Escolaridad, ['Primaria', 'Secundaria', 'Preparatoria', 'Licenciatura', 'Maestría', 'Doctorado']) ? 'selected' : '' }}>Otro</option>
                                                </select>
                                                <span class="input-group-text d-none editing-indicator">Modificado</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="Ocupacion" class="form-label">Ocupación</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                                <input type="text" class="form-control" id="Ocupacion" name="Ocupacion" value="{{ old('Ocupacion', $participante->Ocupacion) }}" required readonly>
                                                <span class="input-group-text d-none editing-indicator">Modificado</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección 2: Información de Empresa -->
                            <div class="col-12">
                                <h5 class="form-section-title"><i class="fas fa-building"></i> Información de Empresa</h5>
                                <div class="form-section-card shadow-sm">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="Empresa" class="form-label">Nombre de la Empresa</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-industry"></i></span>
                                                <input type="text" class="form-control" id="Empresa" name="Empresa" value="{{ old('Empresa', $participante->Empresa) }}" required readonly>
                                                <span class="input-group-text d-none editing-indicator">Modificado</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="Puesto" class="form-label">Puesto en la Empresa</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                <input type="text" class="form-control" id="Puesto" name="Puesto" value="{{ old('Puesto', $participante->Puesto) }}" required readonly>
                                                <span class="input-group-text d-none editing-indicator">Modificado</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="RazónSocial" class="form-label">Razón Social</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-file-invoice"></i></span>
                                                <input type="text" class="form-control" id="RazónSocial" name="RazónSocial" value="{{ old('RazónSocial', $participante->RazónSocial) }}" readonly>
                                                <span class="input-group-text d-none editing-indicator">Modificado</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="RFCEmpresa" class="form-label">RFC Empresa</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-fingerprint"></i></span>
                                                <input type="text" class="form-control" id="RFCEmpresa" name="RFCEmpresa" value="{{ old('RFCEmpresa', $participante->RFCEmpresa) }}" readonly>
                                                <span class="input-group-text d-none editing-indicator">Modificado</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección 3: Inscripción y Pago -->
                            <div class="col-12">
                                <h5 class="form-section-title"><i class="fas fa-money-check-alt"></i> Inscripción y Pago</h5>
                                <div class="form-section-card shadow-sm">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label for="cursos" class="form-label">Cursos Inscritos</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-book"></i></span>
                                                <select class="form-select select2-multiple" id="cursos" name="cursos[]" multiple required disabled>
                                                    @foreach ($cursos as $curso)
                                                        <option value="{{ $curso->id }}"
                                                                data-fecha="{{ $curso->FechadeInicio }}"
                                                                data-costo="{{ $curso->costo }}"
                                                                {{ in_array($curso->id, $participante->cursos->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                            {{ $curso->NombredelCurso }} ({{ $curso->FechadeInicio }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="input-group-text d-none editing-indicator">Modificado</span>
                                            </div>
                                            <small class="text-muted mt-2 d-block">Puedes seleccionar varios cursos de la lista.</small>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="FechadelCurso" class="form-label">Fecha del Curso</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                <input type="date" class="form-control" id="FechadelCurso" name="FechadelCurso" value="{{ old('FechadelCurso', $participante->FechadelCurso) }}" required readonly>
                                                <span class="input-group-text d-none editing-indicator">Modificado</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="Pago" class="form-label">Monto de Pago</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                                <input type="number" step="0.01" min="0" class="form-control" id="Pago" name="Pago" value="{{ old('Pago', $participante->Pago ?: '0.00') }}" readonly>
                                                <span class="input-group-text d-none editing-indicator">Modificado</span>
                                            </div>
                                            <small id="totalSugerido" class="text-primary fw-bold mt-1 d-block" style="display: none;">
                                                Total sugerido: $<span id="sumaCostos">0.00</span>
                                            </small>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="EstadoDePago" class="form-label">Estado de Pago</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                                <select class="form-select" id="EstadoDePago" name="EstadoDePago" required disabled>
                                                    <option value="Pagado" {{ old('EstadoDePago', $participante->EstadoDePago) == 'Pagado' ? 'selected' : '' }}>Pagado</option>
                                                    <option value="Pendiente" {{ old('EstadoDePago', $participante->EstadoDePago) == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                                    <option value="Anticipo" {{ old('EstadoDePago', $participante->EstadoDePago) == 'Anticipo' ? 'selected' : '' }}>Anticipo</option>
                                                    <option value="Cancelado" {{ old('EstadoDePago', $participante->EstadoDePago) == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                                                </select>
                                                <span class="input-group-text d-none editing-indicator">Modificado</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="botones-container pb-3">
                            <button type="button" id="toggleEditButton" class="btn btn-primary btn-action-edit shadow-sm">
                                <i class="fas fa-edit me-2"></i> Editar
                            </button>
                            <button type="submit" class="btn btn-success btn-action-edit shadow-sm" id="saveButton" disabled>
                                <i class="fas fa-save me-2"></i> Guardar Cambios
                            </button>
                            <button type="button" id="cancelButton" class="btn btn-danger btn-action-edit shadow-sm" disabled>
                                <i class="fas fa-times me-2"></i> Cancelar
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
        const formInputs = document.querySelectorAll('#editForm .form-control, #editForm .form-select');
        const originalValues = {};
        let isEditing = false;
        const estadoPagoSelect = document.getElementById('EstadoDePago');
        const pagoInput = document.getElementById('Pago');
        const cursosSelect = document.getElementById('cursos');
        const fechaCursoInput = document.getElementById('FechadelCurso');

        // Almacenar valores originales
        formInputs.forEach(input => {
            if (input.tagName === 'SELECT' && input.multiple) {
                originalValues[input.id] = Array.from(input.selectedOptions).map(option => option.value);
            } else {
                originalValues[input.id] = input.value;
            }
        });

        // Inicializar Select2
        const selectCursos = $('#cursos').select2({
            placeholder: "Seleccione uno o más cursos",
            width: 'resolve'
        });

        // Calcular total inicial
        $(cursosSelect).trigger('change');

        // Función para habilitar edición
        toggleEditButton.addEventListener('click', function () {
            if (!isEditing) {
                formInputs.forEach(input => {
                    input.removeAttribute('readonly');
                    input.removeAttribute('disabled');
                    input.addEventListener('input', showEditingIndicator);
                    input.addEventListener('change', showEditingIndicator);
                });
                
                // Habilitar Select2
                $('#cursos').prop('disabled', false).trigger('change');

                toggleEditButton.style.display = 'none';
                saveButton.removeAttribute('disabled');
                cancelButton.removeAttribute('disabled');
                isEditing = true;
            }
        });

        // Cancelar edición y restaurar valores originales
        cancelButton.addEventListener('click', function () {
            formInputs.forEach(input => {
                if (input.tagName === 'SELECT' && input.multiple) {
                    const values = originalValues[input.id];
                    $(input).val(values).trigger('change');
                } else {
                    input.value = originalValues[input.id];
                }
                input.setAttribute('readonly', true);
                if (input.tagName === 'SELECT') input.setAttribute('disabled', true);
                hideEditingIndicator(input);
            });

            // Deshabilitar Select2
            $('#cursos').prop('disabled', true).trigger('change');

            toggleEditButton.style.display = 'inline-block';
            saveButton.setAttribute('disabled', true);
            cancelButton.setAttribute('disabled', true);
            isEditing = false;
        });

        function showEditingIndicator(event) {
            const input = event.target;
            const group = input.closest('.input-group');
            if (group) {
                const indicator = group.querySelector('.editing-indicator');
                if (indicator) indicator.classList.remove('d-none');
            }
        }

        function hideEditingIndicator(input) {
            const group = input.closest('.input-group');
            if (group) {
                const indicator = group.querySelector('.editing-indicator');
                if (indicator) indicator.classList.add('d-none');
            }
        }

        estadoPagoSelect.addEventListener('change', function () {
            if (estadoPagoSelect.value === 'Cancelado') {
                pagoInput.value = '0.00';
            }
        });

        cursosSelect.addEventListener('change', function () {
            const selectedOptions = Array.from(cursosSelect.selectedOptions);
            let totalSuma = 0;

            if (selectedOptions.length > 0) {
                const firstSelectedOption = selectedOptions[0];
                const fecha = firstSelectedOption.dataset.fecha;
                if (fecha) {
                    fechaCursoInput.value = fecha;
                }

                selectedOptions.forEach(option => {
                    totalSuma += option.dataset.costo ? parseFloat(option.dataset.costo) : 0;
                });

                document.getElementById('sumaCostos').textContent = totalSuma.toFixed(2);
                document.getElementById('totalSugerido').style.display = 'block';
                
                if (isEditing) {
                    pagoInput.value = totalSuma.toFixed(2);
                }
            } else {
                document.getElementById('totalSugerido').style.display = 'none';
            }
        });
    });
</script>
@endsection
