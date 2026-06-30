<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos y Participantes</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (para los íconos del ojo) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="{{ asset('images/Logoibeb.ico') }}">

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 500px;
            width: 100%;
            padding: 50px;
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }
        .login-container img {
            width: 200px;
            margin-bottom: 30px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-login {
            width: 100%;
            background-color: #0d6efd;
            color: #fff;
            font-weight: bold;
            transition: background-color 0.3s ease;
            padding: 12px;
            font-size: 1.1em;
        }
        .btn-login:hover {
            background-color: #0b5ed7;
        }
        .password-toggle {
            position: relative;
        }
        .password-toggle button {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            font-size: 1.4em;
            cursor: pointer;
        }
        .password-toggle button:hover {
            color: #0d6efd;
        }
        .form-control {
            font-size: 1.1em;
            padding: 12px;
        }
        .forgot-password-link {
            text-align: center;
            margin-bottom: 20px;
        }
        .alert-danger {
            border-radius: 6px;
            padding: 12px;
            background-color: #fff2f2;
            color: #d63031;
            margin-bottom: 20px;
            border: 1px solid #ffcccc;
        }
        
        /* Global overrides for rounded buttons and inputs */
        .btn, .btn-login, .form-control, button {
            border-radius: 6px !important;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Imagen centrada -->
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="mx-auto d-block">

        <h2 class="text-center mb-4" style="font-weight: 300; color: #333;">Registro de</h2>

        <h2 class="text-center mb-4" style="font-weight: 300; color: #333;">Cursos y Participantes</h2>

        <!-- Mostrar errores de autenticación -->

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" autocomplete="off">
            @csrf
            <!-- Campo oculto como señuelo -->
            <input type="text" style="display:none;" name="fake_email" autocomplete="username">
            <input type="password" style="display:none;" name="fake_password" autocomplete="current-password">

            <!-- Campo de correo electrónico -->
            <div class="mb-4">
                <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required autofocus autocomplete="off" value="{{ old('email') }}">
            </div>

            <!-- Campo de contraseña -->
            <div class="mb-4 password-toggle">
                <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required autocomplete="off">
                <button type="button" onclick="togglePassword()">
                    <i id="toggleIcon" class="bi bi-eye-slash"></i>
                </button>
            </div>

            <!-- Enlace para recuperar contraseña -->
            <div class="forgot-password-link">
                <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
            </div>

            <!-- Botón de inicio de sesión -->
            <button type="submit" class="btn btn-login">Iniciar sesión</button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            }
        }
    </script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
