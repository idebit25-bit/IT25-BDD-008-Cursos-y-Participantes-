<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha del Participante - {{ $participante->NombredelPostulante }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 10pt;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo { width: 150px; }
        .contact-info {
            text-align: right;
            font-size: 8pt;
            color: #555;
        }
        
        h1 {
            color: #212529;
            font-size: 18pt;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .id-badge {
            color: #0d6efd;
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 20px;
        }

        .section-title {
            background-color: #f8f9fa;
            color: #0d6efd;
            font-size: 11pt;
            font-weight: bold;
            padding: 6px 10px;
            border-left: 4px solid #0d6efd;
            margin: 20px 0 10px 0;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
            border-bottom: 1px solid #f0f0f0;
        }
        .label {
            font-weight: bold;
            color: #555;
            width: 30%;
        }
        .value {
            color: #212529;
            width: 70%;
        }

        .course-list {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .course-list th {
            background-color: #212529;
            color: white;
            padding: 8px;
            font-size: 9pt;
            text-align: left;
        }
        .course-list td {
            padding: 8px;
            border: 1px solid #dee2e6;
            font-size: 9pt;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .highlight {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td><img src="{{ public_path('images/logo4.jpg') }}" class="logo"></td>
            <td class="contact-info">
                <b>Instituto I-DEB</b><br>
                instituto.ideb@idebmexico.com<br>
                3337029639 / 3320705533
            </td>
        </tr>
    </table>

    <h1>{{ $participante->NombredelPostulante }}</h1>
    <div class="id-badge">ID REGISTRO: {{ $participante->N }}</div>

    <div class="section-title">Información Personal</div>
    <table class="info-table">
        <tr>
            <td class="label">Correo Electrónico:</td>
            <td class="value">{{ $participante->Correo }}</td>
        </tr>
        <tr>
            <td class="label">Teléfono:</td>
            <td class="value">{{ $participante->Telefono }}</td>
        </tr>
        <tr>
            <td class="label">Edad:</td>
            <td class="value">{{ $participante->Edad }} años</td>
        </tr>
        <tr>
            <td class="label">CURP:</td>
            <td class="value">{{ $participante->Curp }}</td>
        </tr>
        <tr>
            <td class="label">Dirección:</td>
            <td class="value">{{ $participante->Direccion }}</td>
        </tr>
    </table>

    <div class="section-title">Información Académica y Laboral</div>
    <table class="info-table">
        <tr>
            <td class="label">Escolaridad:</td>
            <td class="value">{{ $participante->Escolaridad }}</td>
        </tr>
        <tr>
            <td class="label">Ocupación:</td>
            <td class="value">{{ $participante->Ocupacion }}</td>
        </tr>
        <tr>
            <td class="label">Puesto:</td>
            <td class="value">{{ $participante->Puesto }}</td>
        </tr>
        <tr>
            <td class="label">Empresa:</td>
            <td class="value">{{ $participante->Empresa ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">RFC Empresa:</td>
            <td class="value">{{ $participante->RFCEmpresa ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Razón Social:</td>
            <td class="value">{{ $participante->RazónSocial ?: 'N/A' }}</td>
        </tr>
    </table>

    <div class="section-title">Resumen de Cuenta</div>
    <table class="info-table">
        <tr>
            <td class="label">Costo Total de Cursos:</td>
            <td class="value">
                ${{ number_format($participante->total_a_cobrar, 2) }} MXN
            </td>
        </tr>
        <tr>
            <td class="label">Monto Pagado:</td>
            <td class="value highlight">
                @php $pago = !empty($participante->pago) && is_numeric($participante->pago) ? floatval($participante->pago) : 0; @endphp
                ${{ number_format($pago, 2) }} MXN
            </td>
        </tr>
        <tr>
            <td class="label">Saldo Pendiente:</td>
            <td class="value" style="color: {{ ($participante->total_a_cobrar - $pago) > 0 ? '#dc3545' : '#28a745' }}; font-weight: bold;">
                ${{ number_format(max(0, $participante->total_a_cobrar - $pago), 2) }} MXN
            </td>
        </tr>
        <tr>
            <td class="label">Estatus de Pago:</td>
            <td class="value"><b>{{ $participante->EstadoDePago }}</b></td>
        </tr>
    </table>

    <div class="section-title">Cursos Inscritos</div>
    @if($participante->cursos->isEmpty())
        <p>No cuenta con cursos registrados actualmente.</p>
    @else
        <table class="course-list">
            <thead>
                <tr>
                    <th>Nombre del Curso</th>
                    <th>Nomenclatura</th>
                    <th>Fecha de Inscripción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($participante->cursos as $curso)
                    <tr>
                        <td>{{ $curso->NombredelCurso }}</td>
                        <td>{{ $curso->Nomenclatura }}</td>
                        <td>
                            @php
                                $fecha = $curso->FechadeInicio ?: $participante->FechadelCurso;
                            @endphp
                            {{ $fecha ? \Carbon\Carbon::parse($fecha)->format('d/m/Y') : 'N/A' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Documento oficial del Instituto I-DEB | Generado el {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
