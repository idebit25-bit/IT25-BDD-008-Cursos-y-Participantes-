<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
        }

        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 420px;
            margin: 20px;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            max-width: 200px;
            height: auto;
            width: 100%;
            object-fit: contain;
        }

        .form-group label {
            font-weight: 500;
            color: var(--primary-gray);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-control {
            border: 1px solid #e1e1e1;
            border-radius: 6px;
            padding: 10px 12px;
            height: auto;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--secondary-gray);
            box-shadow: 0 0 0 0.2rem rgba(102, 102, 102, 0.15);
        }

        .btn-primary {
            background-color: var(--primary-gray);
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-weight: 500;
            margin-top: 20px;
            transition: background-color 0.2s ease;
        }

        .btn-primary:hover {
            background-color: var(--hover-gray);
        }

        h2 {
            color: var(--primary-gray);
            font-weight: 600;
            margin-bottom: 30px;
            font-size: 1.75rem;
        }

        .alert-danger {
            border-radius: 6px;
            border: none;
            background-color: #fff2f2;
            color: #d63031;
            font-size: 0.9rem;
            padding: 12px 15px;
        }

        /* Global overrides for rounded buttons and inputs */
        .btn, .btn-primary, .form-control, button {
            border-radius: 6px !important;
        }

        .alert-danger ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        /* Estilos para el overlay de carga */
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
            display: none; /* Oculto por defecto */
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        @media (max-width: 576px) {
            .login-container {
                margin: 15px;
                padding: 25px;
            }

            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="logo-container">
        <img src="https://www.idebmexico.com/imagenes/cropped-I-DEB-Negro.png" alt="Logo" class="logo">
    </div>
    <h2 class="text-center">Restablecer Contraseña</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <script>
            // Mostrar ventana emergente cuando se envía el correo de restablecimiento
            $(document).ready(function() {
                $('#statusModal').modal('show');
            });
        </script>
    @endif

    <form action="{{ route('password.email') }}" method="POST" id="resetPasswordForm">
        @csrf
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary btn-block" id="submitButton">Enviar Enlace de Restablecimiento</button>
    </form>
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

<!-- Modal de estado -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Enlace Enviado</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Se ha enviado un enlace de restablecimiento a tu correo.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
    // Mostrar el spinner cuando se envíe el formulario
    document.getElementById('resetPasswordForm').addEventListener('submit', function() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    });

    // Ocultar el spinner si hay un error en el formulario
    @if ($errors->any())
        document.getElementById('loadingOverlay').style.display = 'none';
    @endif
</script>

</body>
</html>
