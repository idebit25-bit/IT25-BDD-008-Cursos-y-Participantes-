@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
    .form-select {
        border-left: none;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    .form-select:focus {
        box-shadow: none;
        border-color: #dee2e6;
    }
    
    .form-select.validado-completo {
        border-color: #28a745 !important;
        background-color: #f0fff4 !important;
        border-left: 4px solid #28a745 !important;
        box-shadow: 0 0 0 1px rgba(40, 167, 69, 0.1);
    }
    .form-select.validado-vacio {
        border-color: #dc3545 !important;
        background-color: #fff5f5 !important;
        border-left: 4px solid #dc3545 !important;
        box-shadow: 0 0 0 1px rgba(220, 53, 69, 0.1);
        animation: pulse-red 2s ease-in-out infinite;
    }
    
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
    .estado-indicador.estado-rojo {
        background-color: #dc3545;
        border-color: #dc3545;
        box-shadow: 0 0 12px rgba(220, 53, 69, 0.4);
        animation: pulse-red 2s ease-in-out infinite;
    }
    
    @keyframes pulse-red {
        0% { box-shadow: 0 0 12px rgba(220, 53, 69, 0.4); }
        50% { box-shadow: 0 0 25px rgba(220, 53, 69, 0.8); }
        100% { box-shadow: 0 0 12px rgba(220, 53, 69, 0.4); }
    }
    
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
    .contador-campos .vacios { color: #dc3545; }
</style>

<div class="step-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card step-card">
                    <div class="step-header">
                        <h2>Paso 2: Modalidad del Curso</h2>
                        <p class="mb-0 mt-2 opacity-75">Define cómo se impartirá el curso</p>
                        
                        <div id="estadoGeneral" class="mt-3">
                            <span id="estadoBadge">
                                <span class="estado-indicador" id="indicadorGeneral"></span>
                                <span id="textoEstado">Verificando campos...</span>
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('curso.paso2.guardar') }}" method="POST" id="formularioCurso">
                            @csrf
                            <div class="form-section-card shadow-sm">
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <label for="modalidad" class="form-label">
                                            Modalidad del Curso
                                            <span class="estado-indicador" id="estado-modalidad"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                            <select name="modalidad" id="modalidad" 
                                                class="form-select @error('modalidad') is-invalid @enderror" 
                                                onchange="validarSelect(this)">
                                                <option value="" disabled {{ old('modalidad', $curso->modalidad ?? session('cursos_paso2.modalidad')) ? '' : 'selected' }}>Seleccione una modalidad</option>
                                                <option value="virtual" {{ old('modalidad', $curso->modalidad ?? session('cursos_paso2.modalidad')) == 'virtual' ? 'selected' : '' }}>💻 Virtual</option>
                                                <option value="presencial" {{ old('modalidad', $curso->modalidad ?? session('cursos_paso2.modalidad')) == 'presencial' ? 'selected' : '' }}>🏫 Presencial</option>
                                                <option value="mixto" {{ old('modalidad', $curso->modalidad ?? session('cursos_paso2.modalidad')) == 'mixto' ? 'selected' : '' }}>🔄 Mixto</option>
                                            </select>
                                            @error('modalidad')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted">Selecciona la modalidad principal del curso (opcional)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="contador-campos">
                                <span class="completos" id="totalCompletos">0</span> completos | 
                                <span class="vacios" id="totalVacios">0</span> vacíos
                            </div>

                            <div class="botones-container">
                                <button type="submit" class="btn btn-guardar-verde btn-custom shadow-sm">
                                    <i class="fas fa-save me-2"></i> Guardar y Continuar
                                </button>
                                <button type="button" class="btn btn-warning btn-custom shadow-sm" id="finalizarForzadoBtn">
                                    <i class="fas fa-exclamation-triangle me-2"></i> Finalización Forzada
                                </button>
                                <a href="{{ route('curso.paso1') }}" class="btn btn-secondary btn-custom shadow-sm">
                                    <i class="fas fa-arrow-left me-2"></i> Regresar
                                </a>
                                <a href="{{ route('curso.cancelar') }}" class="btn btn-danger btn-custom shadow-sm" onclick="return confirm('¿Estás seguro de que deseas cancelar la creación? Se perderán los datos ingresados.')">
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
    const selects = document.querySelectorAll('#formularioCurso .form-select');
    selects.forEach(select => {
        validarSelect(select);
    });
    actualizarEstadoGeneral();
    
    // Finalización Forzada
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

function validarSelect(select) {
    const valor = select.value;
    const idCampo = select.id;
    const indicador = document.getElementById('estado-' + idCampo);
    
    select.classList.remove('validado-completo', 'validado-vacio');
    if (indicador) {
        indicador.classList.remove('estado-verde', 'estado-rojo');
    }
    
    if (valor === '' || valor === null || valor === undefined) {
        select.classList.add('validado-vacio');
        if (indicador) {
            indicador.classList.add('estado-rojo');
            indicador.title = 'Campo sin seleccionar';
        }
    } else {
        select.classList.add('validado-completo');
        if (indicador) {
            indicador.classList.add('estado-verde');
            indicador.title = 'Campo completado';
        }
    }
    
    actualizarContadores();
    actualizarEstadoGeneral();
}

function actualizarContadores() {
    const selects = document.querySelectorAll('#formularioCurso .form-select');
    let completos = 0, vacios = 0;
    
    selects.forEach(select => {
        if (select.classList.contains('validado-completo')) {
            completos++;
        } else if (select.classList.contains('validado-vacio')) {
            vacios++;
        }
    });
    
    document.getElementById('totalCompletos').textContent = completos;
    document.getElementById('totalVacios').textContent = vacios;
}

function actualizarEstadoGeneral() {
    const selects = document.querySelectorAll('#formularioCurso .form-select');
    let completos = 0, vacios = 0;
    const total = selects.length;
    
    selects.forEach(select => {
        if (select.classList.contains('validado-completo')) {
            completos++;
        } else if (select.classList.contains('validado-vacio')) {
            vacios++;
        }
    });
    
    const indicadorGeneral = document.getElementById('indicadorGeneral');
    const textoEstado = document.getElementById('textoEstado');
    
    if (vacios > 0) {
        indicadorGeneral.className = 'estado-indicador estado-rojo';
        textoEstado.textContent = `⚠️ ${vacios} campo(s) sin seleccionar - Requiere atención`;
    } else if (completos === total) {
        indicadorGeneral.className = 'estado-indicador estado-verde';
        textoEstado.textContent = '✅ Todos los campos completos - Listo para guardar';
    } else {
        indicadorGeneral.className = 'estado-indicador';
        textoEstado.textContent = 'Verificando campos...';
    }
}
</script>
@endsection