@extends('layouts.app')

@section('content')
<style>
    .step-container {
        padding: 50px 0;
        background-color: #f4f7f6;
        min-height: calc(100vh - 80px);
    }
    .step-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        background: white;
    }
    .step-header {
        background: linear-gradient(135deg, #000000 0%, #333333 100%);
        padding: 30px;
        color: white;
        text-align: center;
    }
    .step-header h2 {
        font-weight: 300;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin: 0;
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
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .input-group-text {
        background-color: #ffffff;
        color: #6c757d;
        border-right: none;
    }
    .form-control {
        border-left: none;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        box-shadow: none;
        border-color: #dee2e6;
    }
    
    /* ============================================
       ESTILOS PARA VALIDACIÓN DE CAMPOS
       ============================================ */
    
    /* Campo COMPLETO (verde) */
    .form-control.validado-completo {
        border-color: #28a745 !important;
        background-color: #f0fff4 !important;
        border-left: 4px solid #28a745 !important;
        box-shadow: 0 0 0 1px rgba(40, 167, 69, 0.1);
    }
    
    /* Campo INCOMPLETO (amarillo) */
    .form-control.validado-incompleto {
        border-color: #ffc107 !important;
        background-color: #fffef0 !important;
        border-left: 4px solid #ffc107 !important;
        box-shadow: 0 0 0 1px rgba(255, 193, 7, 0.1);
    }
    
    /* Campo VACÍO (rojo) */
    .form-control.validado-vacio {
        border-color: #dc3545 !important;
        background-color: #fff5f5 !important;
        border-left: 4px solid #dc3545 !important;
        box-shadow: 0 0 0 1px rgba(220, 53, 69, 0.1);
        animation: pulse-red 2s ease-in-out infinite;
    }
    
    /* Indicadores de estado (círculos junto al label) */
    .estado-indicador {
        display: inline-block;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        transition: all 0.4s ease;
        flex-shrink: 0;
        border: 2px solid #e9ecef;
    }
    
    .estado-indicador.estado-verde {
        background-color: #28a745;
        border-color: #28a745;
        box-shadow: 0 0 12px rgba(40, 167, 69, 0.4);
    }
    
    .estado-indicador.estado-amarillo {
        background-color: #ffc107;
        border-color: #ffc107;
        box-shadow: 0 0 12px rgba(255, 193, 7, 0.4);
    }
    
    .estado-indicador.estado-rojo {
        background-color: #dc3545;
        border-color: #dc3545;
        box-shadow: 0 0 12px rgba(220, 53, 69, 0.4);
        animation: pulse-red 2s ease-in-out infinite;
    }
    
    /* Animación de pulso para estado rojo */
    @keyframes pulse-red {
        0% { box-shadow: 0 0 12px rgba(220, 53, 69, 0.4); }
        50% { box-shadow: 0 0 25px rgba(220, 53, 69, 0.8); }
        100% { box-shadow: 0 0 12px rgba(220, 53, 69, 0.4); }
    }
    
    /* Badge de estado general */
    #estadoBadge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.5s ease;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
    }
    
    #estadoBadge .estado-indicador {
        width: 16px;
        height: 16px;
        border: none;
    }
    
    /* ============================================
       ESTILOS PARA BOTONES
       ============================================ */
    
    .botones-container {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    
    .btn-custom {
        border-radius: 6px;
        padding: 12px 35px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }
    
    /* Botón Guardar en VERDE */
    .btn-guardar-verde {
        background-color: #28a745 !important;
        border: 2px solid #28a745 !important;
        color: #ffffff !important;
    }
    .btn-guardar-verde:hover {
        background-color: #218838 !important;
        border-color: #1e7e34 !important;
        color: #ffffff !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3) !important;
    }
    .btn-guardar-verde i {
        color: #ffffff !important;
    }
    
    /* Botón Finalización Forzada (AMARILLO) */
    .btn-forzar {
        background-color: #ffc107 !important;
        border: 2px solid #ffc107 !important;
        color: #212529 !important;
    }
    .btn-forzar:hover {
        background-color: #e0a800 !important;
        border-color: #e0a800 !important;
        color: #212529 !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3) !important;
    }
    
    /* Contador de campos */
    .contador-campos {
        text-align: center;
        margin-top: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        font-size: 0.9rem;
        color: #495057;
    }
    .contador-campos span {
        font-weight: 700;
    }
    .contador-campos .completos { color: #28a745; }
    .contador-campos .incompletos { color: #ffc107; }
    .contador-campos .vacios { color: #dc3545; }
</style>

<div class="step-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card step-card">
                    <div class="step-header">
                        <h2>Paso 1: Datos del Curso</h2>
                        <p class="mb-0 mt-2 opacity-75">Información básica y general</p>
                        
                        <!-- Indicador de estado general -->
                        <div id="estadoGeneral" class="mt-3">
                            <span id="estadoBadge">
                                <span class="estado-indicador" id="indicadorGeneral"></span>
                                <span id="textoEstado">Verificando campos...</span>
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('curso.paso1.guardar') }}" method="POST" id="formularioCurso">
                            @csrf
                            <div class="form-section-card shadow-sm">
                                <div class="row g-3">
                                    <!-- Campo 1: Nomenclatura - OBLIGATORIO -->
                                    <div class="col-md-6">
                                        <label for="Nomenclatura" class="form-label">
                                            Nomenclatura del Curso *
                                            <span class="estado-indicador" id="estado-Nomenclatura"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                            <input type="text" name="Nomenclatura" id="Nomenclatura" 
                                                class="form-control @error('Nomenclatura') is-invalid @enderror"
                                                placeholder="Ej: CUR-2024-001" 
                                                value="{{ old('Nomenclatura', $curso->nomenclatura ?? '') }}" 
                                                required
                                                oninput="validarCampo(this)"
                                                onchange="validarCampo(this)">
                                            @error('Nomenclatura')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Campo 2: Nombre del Curso - OBLIGATORIO -->
                                    <div class="col-md-6">
                                        <label for="NombredelCurso" class="form-label">
                                            Nombre del Curso *
                                            <span class="estado-indicador" id="estado-NombredelCurso"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-book"></i></span>
                                            <input type="text" name="NombredelCurso" id="NombredelCurso"
                                                class="form-control @error('NombredelCurso') is-invalid @enderror" 
                                                placeholder="Ingrese el nombre" 
                                                value="{{ old('NombredelCurso', $curso->NombredelCurso ?? '') }}" 
                                                required
                                                oninput="validarCampo(this)"
                                                onchange="validarCampo(this)">
                                            @error('NombredelCurso')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Campo 3: Descripción - NO OBLIGATORIO -->
                                    <div class="col-12">
                                        <label for="DescripciondeCurso" class="form-label">
                                            Descripción
                                            <span class="estado-indicador" id="estado-DescripciondeCurso"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                                            <textarea name="DescripciondeCurso" id="DescripciondeCurso" 
                                                class="form-control @error('DescripciondeCurso') is-invalid @enderror" 
                                                rows="3" placeholder="Breve descripción del curso" 
                                                oninput="validarCampo(this)"
                                                onchange="validarCampo(this)">{{ old('DescripciondeCurso', $curso->DescripciondeCurso ?? '') }}</textarea>
                                            @error('DescripciondeCurso')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Campo 4: Costo - NO OBLIGATORIO -->
                                    <div class="col-md-4">
                                        <label for="CostodelCurso" class="form-label">
                                            Costo ($)
                                            <span class="estado-indicador" id="estado-CostodelCurso"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                            <input type="number" step="0.01" name="CostodelCurso" id="CostodelCurso"
                                                class="form-control @error('CostodelCurso') is-invalid @enderror"
                                                value="{{ old('CostodelCurso', $curso->CostodelCurso ?? '') }}"
                                                placeholder="0.00" 
                                                oninput="validarCampo(this)"
                                                onchange="validarCampo(this)">
                                            @error('CostodelCurso')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Campo 5: Instructor - NO OBLIGATORIO -->
                                    <div class="col-md-8">
                                        <label for="InstructorResponsable" class="form-label">
                                            Instructor Responsable
                                            <span class="estado-indicador" id="estado-InstructorResponsable"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                            <input type="text" name="InstructorResponsable" id="InstructorResponsable"
                                                class="form-control @error('InstructorResponsable') is-invalid @enderror"
                                                placeholder="Nombre del instructor" 
                                                value="{{ old('InstructorResponsable', $curso->InstructorResponsable ?? '') }}" 
                                                oninput="validarCampo(this)"
                                                onchange="validarCampo(this)">
                                            @error('InstructorResponsable')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Campo 6: Fecha Inicio - NO OBLIGATORIO -->
                                    <div class="col-md-6">
                                        <label for="FechadeInicio" class="form-label">
                                            Fecha de Inicio
                                            <span class="estado-indicador" id="estado-FechadeInicio"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            <input type="date" name="FechadeInicio" id="FechadeInicio"
                                                class="form-control @error('FechadeInicio') is-invalid @enderror"
                                                value="{{ old('FechadeInicio', $curso->FechadeInicio ?? '') }}" 
                                                oninput="validarCampo(this)"
                                                onchange="validarCampo(this)">
                                            @error('FechadeInicio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Campo 7: Fecha Término - NO OBLIGATORIO -->
                                    <div class="col-md-6">
                                        <label for="FechadeTermino" class="form-label">
                                            Fecha de Término
                                            <span class="estado-indicador" id="estado-FechadeTermino"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                            <input type="date" name="FechadeTermino" id="FechadeTermino"
                                                class="form-control @error('FechadeTermino') is-invalid @enderror"
                                                value="{{ old('FechadeTermino', $curso->FechadeTermino ?? '') }}" 
                                                oninput="validarCampo(this)"
                                                onchange="validarCampo(this)">
                                            @error('FechadeTermino')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Campo 8: Duración - NO OBLIGATORIO -->
                                    <div class="col-12">
                                        <label for="Duracioncurso" class="form-label">
                                            Duración del Curso
                                            <span class="estado-indicador" id="estado-Duracioncurso"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                            <input type="text" name="Duracioncurso" id="Duracioncurso"
                                                class="form-control @error('Duracioncurso') is-invalid @enderror"
                                                placeholder="Ej: 20 horas" 
                                                value="{{ old('Duracioncurso', $curso->Duracioncurso ?? '') }}" 
                                                oninput="validarCampo(this)"
                                                onchange="validarCampo(this)">
                                            @error('Duracioncurso')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contador de campos -->
                            <div class="contador-campos">
                                <span class="completos" id="totalCompletos">0</span> completos | 
                                <span class="incompletos" id="totalIncompletos">0</span> incompletos | 
                                <span class="vacios" id="totalVacios">0</span> vacíos
                            </div>

                            <div class="botones-container">
                                <button type="submit" class="btn btn-guardar-verde btn-custom shadow-sm">
                                    <i class="fas fa-save me-2"></i> Guardar y Continuar
                                </button>
                                <button type="button" class="btn btn-warning btn-custom shadow-sm" id="finalizarForzadoBtn">
                                    <i class="fas fa-exclamation-triangle me-2"></i> Finalización Forzada
                                </button>
                                <a href="{{ route('curso.cancelar') }}" class="btn btn-secondary btn-custom shadow-sm">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener todos los campos del formulario
    const campos = document.querySelectorAll('#formularioCurso .form-control');
    
    // Validar todos los campos al cargar la página
    campos.forEach(campo => {
        validarCampo(campo);
    });
    
    // Actualizar estado general
    actualizarEstadoGeneral();
    
    // ============================================
    // FINALIZACIÓN FORZADA
    // ============================================
    document.getElementById('finalizarForzadoBtn').addEventListener('click', function () {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esto guardará el curso con los datos actuales y no podrás continuar editándolo.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, finalizar ahora',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("curso.finalizacionForzada") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Curso guardado',
                            text: 'El curso ha sido guardado exitosamente.'
                        }).then(() => {
                            window.location.href = "{{ route('cursos.index') }}";
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Ocurrió un error al finalizar el curso.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al procesar la solicitud.'
                    });
                });
            }
        });
    });
});

/**
 * Función principal para validar un campo
 */
function validarCampo(campo) {
    const valor = campo.value.trim();
    const idCampo = campo.id;
    const indicador = document.getElementById('estado-' + idCampo);
    
    // Remover clases anteriores
    campo.classList.remove('validado-completo', 'validado-incompleto', 'validado-vacio');
    if (indicador) {
        indicador.classList.remove('estado-verde', 'estado-amarillo', 'estado-rojo');
    }
    
    // Determinar el estado del campo
    if (valor === '') {
        // CAMPO VACÍO - ROJO
        campo.classList.add('validado-vacio');
        if (indicador) {
            indicador.classList.add('estado-rojo');
            indicador.title = 'Campo vacío';
        }
    } else if (valor.length > 0 && valor.length < 3) {
        // CAMPO INCOMPLETO - AMARILLO (menos de 3 caracteres)
        campo.classList.add('validado-incompleto');
        if (indicador) {
            indicador.classList.add('estado-amarillo');
            indicador.title = 'Campo incompleto';
        }
    } else {
        // CAMPO COMPLETO - VERDE
        campo.classList.add('validado-completo');
        if (indicador) {
            indicador.classList.add('estado-verde');
            indicador.title = 'Campo completo';
        }
    }
    
    // Actualizar contadores y estado general
    actualizarContadores();
    actualizarEstadoGeneral();
}

/**
 * Actualizar los contadores de campos
 */
function actualizarContadores() {
    const campos = document.querySelectorAll('#formularioCurso .form-control');
    let completos = 0, incompletos = 0, vacios = 0;
    
    campos.forEach(campo => {
        if (campo.classList.contains('validado-completo')) {
            completos++;
        } else if (campo.classList.contains('validado-incompleto')) {
            incompletos++;
        } else if (campo.classList.contains('validado-vacio')) {
            vacios++;
        }
    });
    
    document.getElementById('totalCompletos').textContent = completos;
    document.getElementById('totalIncompletos').textContent = incompletos;
    document.getElementById('totalVacios').textContent = vacios;
}

/**
 * Actualizar el estado general del formulario
 */
function actualizarEstadoGeneral() {
    const campos = document.querySelectorAll('#formularioCurso .form-control');
    let completos = 0, incompletos = 0, vacios = 0;
    const total = campos.length;
    
    campos.forEach(campo => {
        if (campo.classList.contains('validado-completo')) {
            completos++;
        } else if (campo.classList.contains('validado-incompleto')) {
            incompletos++;
        } else if (campo.classList.contains('validado-vacio')) {
            vacios++;
        }
    });
    
    const indicadorGeneral = document.getElementById('indicadorGeneral');
    const textoEstado = document.getElementById('textoEstado');
    
    // Determinar el estado general
    if (vacios > 0) {
        // Hay campos vacíos - ROJO
        indicadorGeneral.className = 'estado-indicador estado-rojo';
        textoEstado.textContent = `⚠️ ${vacios} campo(s) vacío(s) - Requiere atención`;
    } else if (incompletos > 0) {
        // Hay campos incompletos - AMARILLO
        indicadorGeneral.className = 'estado-indicador estado-amarillo';
        textoEstado.textContent = `🟡 ${incompletos} campo(s) incompleto(s) - Revisar`;
    } else if (completos === total) {
        // Todos los campos completos - VERDE
        indicadorGeneral.className = 'estado-indicador estado-verde';
        textoEstado.textContent = '✅ Todos los campos completos - Listo para guardar';
    } else {
        // Estado por defecto
        indicadorGeneral.className = 'estado-indicador';
        textoEstado.textContent = 'Verificando campos...';
    }
}
</script>

@endsection