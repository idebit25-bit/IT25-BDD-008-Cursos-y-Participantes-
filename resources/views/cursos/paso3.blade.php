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
    .form-control {
        border-left: none;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        box-shadow: none;
        border-color: #dee2e6;
    }

    .form-control.porcentaje-completo {
        border-color: #28a745 !important;
        background-color: #f0fff4 !important;
        border-left: 4px solid #28a745 !important;
    }
    .form-control.porcentaje-incompleto {
        border-color: #ffc107 !important;
        background-color: #fffef0 !important;
        border-left: 4px solid #ffc107 !important;
    }
    .form-control.porcentaje-vacio {
        border-color: #dc3545 !important;
        background-color: #fff5f5 !important;
        border-left: 4px solid #dc3545 !important;
        animation: pulse-red 2s ease-in-out infinite;
    }

    .form-control.drive-completo {
        border-color: #28a745 !important;
        background-color: #f0fff4 !important;
        border-left: 4px solid #28a745 !important;
    }
    .form-control.drive-vacio {
        border-color: #dc3545 !important;
        background-color: #fff5f5 !important;
        border-left: 4px solid #dc3545 !important;
        animation: pulse-red 2s ease-in-out infinite;
    }

    @keyframes pulse-red {
        0% { box-shadow: 0 0 12px rgba(220, 53, 69, 0.4); }
        50% { box-shadow: 0 0 25px rgba(220, 53, 69, 0.8); }
        100% { box-shadow: 0 0 12px rgba(220, 53, 69, 0.4); }
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

    .documento-preview {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 15px;
        background: #e9ecef;
        border-radius: 6px;
        margin-top: 8px;
        font-size: 0.9rem;
    }
    .documento-preview i {
        font-size: 1.3rem;
        color: #0d6efd;
    }
    .documento-preview .doc-nombre {
        flex: 1;
        font-weight: 500;
        color: #212529;
        word-break: break-all;
    }
    .documento-preview .doc-acciones {
        display: flex;
        gap: 8px;
    }
    .documento-preview .doc-acciones a {
        text-decoration: none;
        font-size: 0.85rem;
        padding: 4px 12px;
        border-radius: 4px;
    }
    .btn-ver-doc {
        background: #0d6efd;
        color: white !important;
    }
    .btn-ver-doc:hover {
        background: #0b5ed7;
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

    .platform-title {
        font-weight: 700;
        text-transform: uppercase;
        color: #333;
        border-bottom: 2px solid #333;
        padding-bottom: 5px;
        margin-bottom: 20px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 10px;
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
    .contador-campos .incompletos { color: #ffc107; }
    .contador-campos .vacios { color: #dc3545; }

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
</style>

<div class="step-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card step-card">
                    <div class="step-header">
                        <h2>Paso 3: Formato de Flyer / Imagen</h2>
                        <p class="mb-0 mt-2 opacity-75">Material gráfico y redes sociales</p>

                        <div id="estadoGeneral" class="mt-3">
                            <span id="estadoBadge">
                                <span class="estado-indicador" id="indicadorGeneral"></span>
                                <span id="textoEstado">Verificando campos...</span>
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('curso.paso3.guardar') }}" method="POST" enctype="multipart/form-data" id="formularioCurso">
                            @csrf

                            <!-- ==========================================
                            SIN FECHA
                            ========================================== -->
                            <div class="form-section-card shadow-sm">
                                <div class="platform-title"><i class="fas fa-calendar-times"></i> Sin Fecha</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="SinFecha" class="form-label">
                                            Porcentaje
                                            <span class="estado-indicador" id="estado-SinFecha"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                                            <input type="text" name="SinFecha" id="SinFecha" 
                                                class="form-control" 
                                                value="{{ old('SinFecha', isset($recursos['sin_fecha']) ? $recursos['sin_fecha']->url : '') }}" 
                                                placeholder="Ej: 100%"
                                                oninput="validarPorcentaje(this)"
                                                onchange="validarPorcentaje(this)">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="DriveSinFecha" class="form-label">
                                            URL Drive
                                            <span class="estado-indicador" id="estado-DriveSinFecha"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-google-drive"></i></span>
                                            <input type="text" name="DriveSinFecha" id="DriveSinFecha" 
                                                class="form-control" 
                                                value="{{ old('DriveSinFecha', isset($recursos['sin_fecha']) ? $recursos['sin_fecha']->drive_url : '') }}" 
                                                placeholder="https://drive.google.com/..."
                                                oninput="validarDrive(this)"
                                                onchange="validarDrive(this)">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Archivo Local</label>
                                        <input type="file" name="archivoSinFecha" class="form-control">
                                        @if(isset($recursos['sin_fecha_archivo']) && $recursos['sin_fecha_archivo'])
                                            <div class="documento-preview">
                                                <i class="fas fa-file-pdf"></i>
                                                <span class="doc-nombre">{{ basename($recursos['sin_fecha_archivo']->url) }}</span>
                                                <div class="doc-acciones">
                                                    <a href="{{ $recursos['sin_fecha_archivo']->archivo_publico_url }}" target="_blank" class="btn-ver-doc">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- ==========================================
                            FACEBOOK
                            ========================================== -->
                            <div class="form-section-card shadow-sm">
                                <div class="platform-title"><i class="fab fa-facebook"></i> Facebook</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="Facebook" class="form-label">
                                            Porcentaje
                                            <span class="estado-indicador" id="estado-Facebook"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                                            <input type="text" name="Facebook" id="Facebook" 
                                                class="form-control" 
                                                value="{{ old('Facebook', isset($recursos['facebook']) ? $recursos['facebook']->url : '') }}" 
                                                placeholder="Ej: 100%"
                                                oninput="validarPorcentaje(this)"
                                                onchange="validarPorcentaje(this)">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="DriveFacebook" class="form-label">
                                            URL Drive
                                            <span class="estado-indicador" id="estado-DriveFacebook"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-google-drive"></i></span>
                                            <input type="text" name="DriveFacebook" id="DriveFacebook" 
                                                class="form-control" 
                                                value="{{ old('DriveFacebook', isset($recursos['facebook']) ? $recursos['facebook']->drive_url : '') }}" 
                                                placeholder="https://drive.google.com/..."
                                                oninput="validarDrive(this)"
                                                onchange="validarDrive(this)">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Archivo Local</label>
                                        <input type="file" name="archivoFacebook" class="form-control">
                                        @if(isset($recursos['facebook_archivo']) && $recursos['facebook_archivo'])
                                            <div class="documento-preview">
                                                <i class="fas fa-file-image"></i>
                                                <span class="doc-nombre">{{ basename($recursos['facebook_archivo']->url) }}</span>
                                                <div class="doc-acciones">
                                                    <a href="{{ $recursos['facebook_archivo']->archivo_publico_url }}" target="_blank" class="btn-ver-doc">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- ==========================================
                            LINKEDIN
                            ========================================== -->
                            <div class="form-section-card shadow-sm">
                                <div class="platform-title"><i class="fab fa-linkedin"></i> LinkedIn</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="Linkedin" class="form-label">
                                            Porcentaje
                                            <span class="estado-indicador" id="estado-Linkedin"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                                            <input type="text" name="Linkedin" id="Linkedin" 
                                                class="form-control" 
                                                value="{{ old('Linkedin', isset($recursos['linkedin']) ? $recursos['linkedin']->url : '') }}" 
                                                placeholder="Ej: 100%"
                                                oninput="validarPorcentaje(this)"
                                                onchange="validarPorcentaje(this)">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="DriveLinkedin" class="form-label">
                                            URL Drive
                                            <span class="estado-indicador" id="estado-DriveLinkedin"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-google-drive"></i></span>
                                            <input type="text" name="DriveLinkedin" id="DriveLinkedin" 
                                                class="form-control" 
                                                value="{{ old('DriveLinkedin', isset($recursos['linkedin']) ? $recursos['linkedin']->drive_url : '') }}" 
                                                placeholder="https://drive.google.com/..."
                                                oninput="validarDrive(this)"
                                                onchange="validarDrive(this)">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Archivo Local</label>
                                        <input type="file" name="archivoLinkedIn" class="form-control">
                                        @if(isset($recursos['linkedin_archivo']) && $recursos['linkedin_archivo'])
                                            <div class="documento-preview">
                                                <i class="fas fa-file-pdf"></i>
                                                <span class="doc-nombre">{{ basename($recursos['linkedin_archivo']->url) }}</span>
                                                <div class="doc-acciones">
                                                    <a href="{{ $recursos['linkedin_archivo']->archivo_publico_url }}" target="_blank" class="btn-ver-doc">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- ==========================================
                            INSTAGRAM
                            ========================================== -->
                            <div class="form-section-card shadow-sm">
                                <div class="platform-title"><i class="fab fa-instagram"></i> Instagram</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="Instagram" class="form-label">
                                            Porcentaje
                                            <span class="estado-indicador" id="estado-Instagram"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                                            <input type="text" name="Instagram" id="Instagram" 
                                                class="form-control" 
                                                value="{{ old('Instagram', isset($recursos['instagram']) ? $recursos['instagram']->url : '') }}" 
                                                placeholder="Ej: 100%"
                                                oninput="validarPorcentaje(this)"
                                                onchange="validarPorcentaje(this)">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="DriveInstagram" class="form-label">
                                            URL Drive
                                            <span class="estado-indicador" id="estado-DriveInstagram"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-google-drive"></i></span>
                                            <input type="text" name="DriveInstagram" id="DriveInstagram" 
                                                class="form-control" 
                                                value="{{ old('DriveInstagram', isset($recursos['instagram']) ? $recursos['instagram']->drive_url : '') }}" 
                                                placeholder="https://drive.google.com/..."
                                                oninput="validarDrive(this)"
                                                onchange="validarDrive(this)">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Archivo Local</label>
                                        <input type="file" name="archivoInstagram" class="form-control">
                                        @if(isset($recursos['instagram_archivo']) && $recursos['instagram_archivo'])
                                            <div class="documento-preview">
                                                <i class="fas fa-file-image"></i>
                                                <span class="doc-nombre">{{ basename($recursos['instagram_archivo']->url) }}</span>
                                                <div class="doc-acciones">
                                                    <a href="{{ $recursos['instagram_archivo']->archivo_publico_url }}" target="_blank" class="btn-ver-doc">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

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
                                <a href="{{ route('curso.paso2') }}" class="btn btn-secondary btn-custom shadow-sm">
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
    document.querySelectorAll('#SinFecha, #Facebook, #Linkedin, #Instagram').forEach(campo => {
        validarPorcentaje(campo);
    });
    document.querySelectorAll('#DriveSinFecha, #DriveFacebook, #DriveLinkedin, #DriveInstagram').forEach(campo => {
        validarDrive(campo);
    });
    actualizarContadores();
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

function validarPorcentaje(campo) {
    const valor = campo.value.trim();
    const indicador = document.getElementById('estado-' + campo.id);
    
    campo.classList.remove('porcentaje-completo', 'porcentaje-incompleto', 'porcentaje-vacio');
    if (indicador) {
        indicador.classList.remove('estado-verde', 'estado-amarillo', 'estado-rojo');
    }
    
    if (valor === '') {
        campo.classList.add('porcentaje-vacio');
        if (indicador) indicador.classList.add('estado-rojo');
    } else if (valor === '100%' || valor === '100 %' || valor === '100') {
        campo.classList.add('porcentaje-completo');
        if (indicador) indicador.classList.add('estado-verde');
    } else {
        campo.classList.add('porcentaje-incompleto');
        if (indicador) indicador.classList.add('estado-amarillo');
    }
    
    actualizarContadores();
    actualizarEstadoGeneral();
}

function validarDrive(campo) {
    const valor = campo.value.trim();
    const indicador = document.getElementById('estado-' + campo.id);
    
    campo.classList.remove('drive-completo', 'drive-vacio');
    if (indicador) {
        indicador.classList.remove('estado-verde', 'estado-rojo');
    }
    
    if (valor === '') {
        campo.classList.add('drive-vacio');
        if (indicador) indicador.classList.add('estado-rojo');
    } else {
        campo.classList.add('drive-completo');
        if (indicador) indicador.classList.add('estado-verde');
    }
    
    actualizarContadores();
    actualizarEstadoGeneral();
}

function actualizarContadores() {
    const campos = document.querySelectorAll('#formularioCurso .form-control');
    let completos = 0, incompletos = 0, vacios = 0;
    
    campos.forEach(campo => {
        if (campo.classList.contains('porcentaje-completo') || campo.classList.contains('drive-completo')) {
            completos++;
        } else if (campo.classList.contains('porcentaje-incompleto')) {
            incompletos++;
        } else if (campo.classList.contains('porcentaje-vacio') || campo.classList.contains('drive-vacio')) {
            vacios++;
        }
    });
    
    document.getElementById('totalCompletos').textContent = completos;
    document.getElementById('totalIncompletos').textContent = incompletos;
    document.getElementById('totalVacios').textContent = vacios;
}

function actualizarEstadoGeneral() {
    const campos = document.querySelectorAll('#formularioCurso .form-control');
    let completos = 0, incompletos = 0, vacios = 0;
    const total = campos.length;
    
    campos.forEach(campo => {
        if (campo.classList.contains('porcentaje-completo') || campo.classList.contains('drive-completo')) {
            completos++;
        } else if (campo.classList.contains('porcentaje-incompleto')) {
            incompletos++;
        } else if (campo.classList.contains('porcentaje-vacio') || campo.classList.contains('drive-vacio')) {
            vacios++;
        }
    });
    
    const indicadorGeneral = document.getElementById('indicadorGeneral');
    const textoEstado = document.getElementById('textoEstado');
    
    if (vacios > 0) {
        indicadorGeneral.className = 'estado-indicador estado-rojo';
        textoEstado.textContent = `⚠️ ${vacios} campo(s) vacío(s) - Requiere atención`;
    } else if (incompletos > 0) {
        indicadorGeneral.className = 'estado-indicador estado-amarillo';
        textoEstado.textContent = `🟡 ${incompletos} campo(s) incompleto(s) - Deben ser 100%`;
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