<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Estilos personalizados -->
    <style>
        :root {
            --primary-gray: #333333;
            --secondary-gray: #666666;
            --light-gray: #f5f5f5;
            --hover-gray: #444444;
        }

        body {
            background-color: var(--light-gray);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }

        .reset-container {
            background-color: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 420px;
            margin: 20px;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .logo {
            max-width: 200px;
            height: auto;
            width: 100%;
            object-fit: contain;
        }

        h2 {
            color: var(--primary-gray);
            font-weight: 600;
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            font-weight: 500;
            color: var(--primary-gray);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-control {
            border: 1px solid #e1e1e1;
            border-radius: 6px;
            padding: 0.75rem;
            font-size: 0.95rem;
            width: 100%;
        }

        .form-control:focus {
            border-color: var(--secondary-gray);
            box-shadow: 0 0 0 0.2rem rgba(102, 102, 102, 0.15);
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle button {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .password-toggle button:hover {
            color: var(--primary-gray);
        }

        .btn-primary {
            background-color: var(--primary-gray);
            border: none;
            padding: 0.75rem;
            border-radius: 6px;
            font-weight: 500;
            width: 100%;
            transition: background-color 0.2s ease;
        }

        .btn-primary:hover {
            background-color: var(--hover-gray);
        }

        .alert-danger {
            border-radius: 6px;
            border: none;
            background-color: #fff2f2;
            color: #d63031;
            font-size: 0.9rem;
            padding: 0.75rem;
            margin-bottom: 1.5rem;
        }

        /* Global overrides for rounded buttons and inputs */
        .btn, .btn-primary, .form-control, button {
            border-radius: 6px !important;
        }

        .alert-danger ul {
            margin-bottom: 0;
            padding-left: 1.5rem;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            display: none;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        .text-center {
            text-align: center;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        @media (max-width: 576px) {
            .reset-container {
                padding: 1.5rem;
                margin: 1rem;
            }

            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Contenedor principal -->
    <div class="reset-container">
        <!-- Logo -->
        <div class="logo-container">
            <img src="https://www.idebmexico.com/imagenes/cropped-I-DEB-Negro.png" alt="Logo" class="logo">
        </div>

        <!-- Título -->
        <h2>Restablecer Contraseña</h2>

        <!-- Mostrar errores -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario -->
        <form id="resetPasswordForm">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <!-- Campo de correo electrónico -->
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $email) }}" required autofocus>
            </div>

            <!-- Campo de nueva contraseña -->
            <div class="form-group password-toggle">
                <label for="password">Nueva Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <button type="button" onclick="togglePassword('password', 'toggleIcon1')">
                    <i id="toggleIcon1" class="bi bi-eye-slash"></i>
                </button>
            </div>

            <!-- Campo de confirmación de contraseña -->
            <div class="form-group password-toggle">
                <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
            </div>

            <!-- Botón de envío -->
            <button type="submit" class="btn btn-primary" id="submitButton">Restablecer Contraseña</button>
        </form>

        <!-- Enlace para regresar al login -->
        <p class="text-center mt-3">
            <a href="{{ url('/login') }}">Regresar al inicio de sesión</a>
        </p>
    </div>

    <!-- Overlay de carga -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
    </div>

    <!-- Modal de éxito -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Contraseña Restablecida</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ¡Tu contraseña ha sido restablecida con éxito! Regresando al login.
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Función para alternar la visibilidad de la contraseña
        function togglePassword(fieldId, iconId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(iconId);
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

        // Manejar el envío del formulario
        document.getElementById('resetPasswordForm').addEventListener('submit', async function (event) {
            event.preventDefault();

            // Mostrar el overlay de carga
            document.getElementById('loadingOverlay').style.display = 'flex';

            // Enviar la solicitud
            try {
                const formData = new FormData(this);
                const response = await fetch("{{ route('password.update') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                });

                // Ocultar el overlay de carga
                document.getElementById('loadingOverlay').style.display = 'none';

                // Manejar la respuesta
                if (response.ok) {
                    $('#statusModal').modal('show'); // Mostrar el modal de éxito
                    setTimeout(() => {
                        window.location.href = "{{ url('/login') }}";
                    }, 3000);
                } else {
                    const data = await response.json();
                    if (data.errors) {
                        let errorHtml = '<ul>';
                        for (const error of Object.values(data.errors)) {
                            errorHtml += `<li>${error}</li>`;
                        }
                        errorHtml += '</ul>';
                        document.querySelector('.alert-danger').innerHTML = errorHtml;
                        document.querySelector('.alert-danger').style.display = 'block';
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('loadingOverlay').style.display = 'none';
            }
        });
    </script>
</body>
</html>
