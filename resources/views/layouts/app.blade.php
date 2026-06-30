@php
    // Obtener el logo desde la configuración
    $setting = \App\Models\Setting::first();
    $logoPath = $setting && $setting->logo ? asset('storage/' . $setting->logo) : asset('images/default-logo.png');
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Librerías CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="icon" type="image/x-icon" href="{{ asset('images/Logoibeb.ico') }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }
        header {
            background-color: #000;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 10;
        }
        /* Se define dos secciones en el header */
        .header-left,
        .header-right {
            display: flex;
            align-items: center;
        }
        .header-left {
            /* Contiene el logo */
        }
        .header-right {
            gap: 10px;
        }
        .logo {
            width: 100px;
            height: auto;
            cursor: pointer;
        }
        .btn-cerrar-sesion {
            background: linear-gradient(135deg, #ff4757, #ff6b81);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 1rem;
        }
        .btn-cerrar-sesion:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 71, 87, 0.2);
        }
        .container {
            text-align: center;
            padding: 40px;
        }
        h1 {
            margin-bottom: 30px;
            font-size: 2em;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            justify-items: center;
        }
    </style>
</head>
<body>
    @php
        use Illuminate\Support\Facades\Auth;
        $userRole = auth()->user()?->puesto ?? 'guest';
    @endphp
    <header>
        <div class="header-left">
            <a href="/inicio">
                <img src="{{ $logoPath }}" alt="Logo de la aplicación" style="max-width: 150px;">
            </a>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>
