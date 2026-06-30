@extends('home')
@section('title', '- Registro de Participante')
@section('nav')

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- jQuery UI -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="icon" type="image/x-icon" href="{{ asset('images/Logoibeb.ico') }}">

<style>
    .registration-container {
        padding: 50px 0;
        margin-top: 50px !important;
    }
    .registration-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        background: white;
    }
    .registration-header {
        background: linear-gradient(135deg, #212529 0%, #343a40 100%);
        padding: 40px 30px;
        color: white;
        border-bottom: 4px solid #28a745;
        text-align: center;
        position: relative;
    }
    .registration-header h2 {
        font-weight: 300;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 10px;
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
    .icon-header {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.9;
    }
    .form-section-title {
        font-size: 0.85rem;
        font-weight: bold;
        color: #0d6efd;
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
        color: #0d6efd;
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
    .form-control:focus, .form-select:focus {
        box-shadow: none;
        border-color: #dee2e6;
    }
    .input-group:focus-within {
        box-shadow: 0 0 0 0.25rem rgba(0, 0, 0, 0.05);
        border-radius: 0.375rem;
    }
    .botones-container {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
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
</style>

<div class="container registration-container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="card registration-card">
                <div class="registration-header">
                    <a href="/participantes" class="back-arrow" title="Regresar">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="icon-header">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <h2>Registro de Participantes</h2>
                    <p class="mb-0 mt-2 opacity-75">Complete sus datos para inscribirse en nuestros programas</p>
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

                    <form action="{{ route('registro.store') }}" method="POST">
                        @csrf 

                        <div class="row">
                            <!-- Sección 1: Datos Personales -->
                            <div class="col-12">
                                <h5 class="form-section-title"><i class="fas fa-user"></i> Datos Personales</h5>
                                <div class="form-section-card shadow-sm">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label for="NombredelPostulante" class="form-label">Nombre Completo</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                                                <input type="text" class="form-control" id="NombredelPostulante" name="NombredelPostulante" value="{{ old('NombredelPostulante') }}" required placeholder="Ingrese su nombre completo">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="Curp" class="form-label">CURP</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                <input type="text" class="form-control" id="Curp" name="Curp" value="{{ old('Curp') }}" maxlength="18" minlength="18" style="text-transform: uppercase;" required placeholder="18 caracteres">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="Correo" class="form-label">Correo Electrónico</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                <input type="email" class="form-control" id="Correo" name="Correo" value="{{ old('Correo') }}" required placeholder="ejemplo@correo.com">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="Telefono" class="form-label">Teléfono</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                <input type="text" class="form-control" id="Telefono" name="Telefono" value="{{ old('Telefono') }}" maxlength="10" pattern="\d{10}" placeholder="10 dígitos" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="Edad" class="form-label">Edad</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                                <input type="number" class="form-control" id="Edad" name="Edad" value="{{ old('Edad') }}" min="18" max="90" required>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="Direccion" class="form-label">Dirección Particular</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                <input type="text" class="form-control" id="Direccion" name="Direccion" value="{{ old('Direccion') }}" required placeholder="Calle, número, colonia, CP">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="Escolaridad" class="form-label">Último Grado de Estudios</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                                                <select class="form-select" id="Escolaridad" name="Escolaridad" required>
                                                    <option value="" disabled {{ old('Escolaridad') ? '' : 'selected' }}>Seleccione...</option>
                                                    <option value="Primaria" {{ old('Escolaridad') == 'Primaria' ? 'selected' : '' }}>Primaria</option>
                                                    <option value="Secundaria" {{ old('Escolaridad') == 'Secundaria' ? 'selected' : '' }}>Secundaria</option>
                                                    <option value="Preparatoria" {{ old('Escolaridad') == 'Preparatoria' ? 'selected' : '' }}>Preparatoria</option>
                                                    <option value="Licenciatura" {{ old('Escolaridad') == 'Licenciatura' ? 'selected' : '' }}>Licenciatura</option>
                                                    <option value="Maestría" {{ old('Escolaridad') == 'Maestría' ? 'selected' : '' }}>Maestría</option>
                                                    <option value="Doctorado" {{ old('Escolaridad') == 'Doctorado' ? 'selected' : '' }}>Doctorado</option>
                                                    <option value="Otro" {{ old('Escolaridad') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección 2: Información Laboral -->
                            <div class="col-12">
                                <h5 class="form-section-title"><i class="fas fa-briefcase"></i> Información Laboral</h5>
                                <div class="form-section-card shadow-sm">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="Empresa" class="form-label">Empresa donde labora</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-industry"></i></span>
                                                <input type="text" class="form-control" id="Empresa" name="Empresa" value="{{ old('Empresa') }}" required placeholder="Nombre comercial">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="RazónSocial" class="form-label">Razón Social</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-file-invoice"></i></span>
                                                <input type="text" class="form-control" id="RazónSocial" name="RazónSocial" value="{{ old('RazónSocial') }}" required placeholder="Nombre legal de la empresa">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="RFCEmpresa" class="form-label">RFC de la Empresa</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-fingerprint"></i></span>
                                                <input type="text" class="form-control" id="RFCEmpresa" name="RFCEmpresa" value="{{ old('RFCEmpresa') }}" required placeholder="RFC institucional">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="Puesto" class="form-label">Puesto o Cargo</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                <input type="text" class="form-control" id="Puesto" name="Puesto" placeholder="Escriba su puesto..." required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="Ocupacion" class="form-label">Ocupación Específica</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-tools"></i></span>
                                                <input type="text" class="form-control" id="Ocupacion" name="Ocupacion" placeholder="Detalle su ocupación..." required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección 3: Detalles de Inscripción -->
                            <div class="col-12">
                                <h5 class="form-section-title"><i class="fas fa-graduation-cap"></i> Inscripción y Pago</h5>
                                <div class="form-section-card shadow-sm">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label for="cursos" class="form-label">Seleccione el curso de su interés</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-book"></i></span>
                                                <select class="form-select" id="cursos" name="cursos[]" multiple required onchange="updateCourseDetails()" style="height: 100px;">
                                                    @foreach ($cursos as $curso)
                                                        <option value="{{ $curso->id }}" data-fecha="{{ $curso->FechadeInicio }}">
                                                            {{ $curso->NombredelCurso }} (Inicio: {{ $curso->FechadeInicio }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle me-1"></i> Mantenga presionado Ctrl para seleccionar varios cursos.</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="FechadeInicioDisplay" class="form-label">Fecha de Inicio Estimada</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                                <input type="text" class="form-control bg-white" id="FechadeInicioDisplay" readonly placeholder="Seleccione un curso">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="EstadoDePago" class="form-label">Método o Estatus de Pago</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                                <select class="form-select" id="EstadoDePago" name="EstadoDePago" required onchange="togglePagoField()">
                                                    <option value="" disabled selected>Seleccione opción...</option>
                                                    <option value="Pagado">Pago Completo</option>
                                                    <option value="Anticipo">Pago con Anticipo</option>
                                                    <option value="Pendiente">Pago Pendiente</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="pagoFieldContainer" class="col-md-12" style="display: none;">
                                            <label for="Pago" class="form-label">Monto del Pago</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                                <input type="number" step="0.01" min="0" class="form-control" id="Pago" name="Pago" placeholder="Ingrese el monto depositado">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="botones-container pb-3">
                            <button type="submit" class="btn btn-success btn-custom shadow-sm">
                                <i class="fas fa-save me-2"></i> Guardar
                            </button>
                            <a href="javascript:history.back()" class="btn btn-danger btn-custom shadow-sm">
                                <i class="fas fa-times me-2"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateCourseDetails() {
        const cursos = document.getElementById('cursos');
        const fechaDisplay = document.getElementById('FechadeInicioDisplay');
        const selectedOption = cursos.selectedOptions[0];
        if (selectedOption) {
            fechaDisplay.value = selectedOption.getAttribute('data-fecha');
        } else {
            fechaDisplay.value = '';
        }
    }

    function togglePagoField() {
        const estadoPago = document.getElementById('EstadoDePago').value;
        const pagoFieldContainer = document.getElementById('pagoFieldContainer');
        const pagoInput = document.getElementById('Pago');
        if (estadoPago === 'Pagado' || estadoPago === 'Anticipo') {
            $(pagoFieldContainer).fadeIn();
            pagoInput.setAttribute('required', true);
        } else {
            $(pagoFieldContainer).fadeOut();
            pagoInput.removeAttribute('required');
        }
    }

    $(function () {
        const puestos = [
            "01 Cultivo, crianza y aprovechamiento", "01.1 Agricultura y silvicultura", "01.2 Ganadería",
            "01.3 Pesca y acuacultura", "02 Extracción y suministro", "02.1 Exploración",
            "02.2 Extracción", "02.3 Refinación y beneficio", "02.4 Provisión de energia",
            "02.5 Provisión de agua", "03 Construcción", "03.1 Planeación y dirección de obras",
            "03.2 Edificación y urbanización", "03.3 Acabado", "03.4 Instalación y mantenimiento",
            "04 Tecnologia", "04.1 Mecánica", "04.2 Electricidad", "04.3 Electrónica",
            "04.4 Informática", "04.5 Telecomunicaciones", "04.6 Procesos industriales",
            "05 Procesamiento y fabricación", "05.1 Minerales no metálicos", "05.2 Metales",
            "05.3 Alimento y bebidas", "05.4 Textiles y prendas de vestir", "05.5 Materia orgánica",
            "05.6 Productos químicos", "05.7 Productos metálicos y de hule y plástico",
            "05.8 Productos eléctricos y electrónicos", "05.9 Productos impresos",
            "06 Transporte", "06.1 Ferroviario", "06.2 Autotransporte", "06.3 Aéreo",
            "06.4 Maritimo y fluvial", "06.5 Servicios de apoyo", "07 Provisión de bienes y servicios",
            "07.1 Comercio", "07.2 Alimentación y hospedaje", "07.3 Turismo",
            "07.4 Deporte y esparcimiento.", "07.5 Servicios personales",
            "07.6 Reparación de artículos de uso doméstico y personal", "7.7 Limpieza",
            "07.8 Servicio postal y mensajeria", "08 Gestión y soporte administrativo",
            "08.1 Bolsa, banca y seguros", "08.2 Administración", "08.3 Servicios legales",
            "09 Salud y protección social", "09.1 Servicios médicos",
            "09.2 Inspección sanitaria y del medio ambiente", "09.3 Seguridad social",
            "09.4 Protección de bienes y/o personas", "10 Comunicación", "10.1 Publicación",
            "10.2 Radio, cine, televisión y teatro", "10.3 Interpretación artística",
            "10.4 Traducción e interpretación lingüística", "10.5 Publicidad, propaganda y relaciones públicas",
            "11 Desarrollo y extensión del conocimiento", "11.1 Investigación", "11.2 Enseñanza",
            "11.3 Difusión cultural"
        ];

        $("#Puesto").autocomplete({
            source: puestos,
            minLength: 0,
            select: function (event, ui) {
                $("#Puesto").val(ui.item.value);
                return false;
            }
        }).focus(function() {
            $(this).autocomplete("search", "");
        });
    });
</script>
@endsection
