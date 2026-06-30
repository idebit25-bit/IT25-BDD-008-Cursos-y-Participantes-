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
  <!-- Tus enlaces a CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <script src="https://kit.fontawesome.com/e97d2d8812.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <title>I-DEB @yield('title')</title>
      <link rel="icon" type="image/x-icon" href="{{ asset('images/Logoibeb.ico') }}">

 
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            font-family: Arial, sans-serif;
            font-size: 16px;
        }

        /* Header */
        .header {
            background-color: #000000;
            color: white;
            padding: 15px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: 71px;
        }

        .logo {
            max-height: 40px;
            margin-right: 20px;
        }

        /* Contenedor principal */
        .main-container {
            display: flex;
            margin-top: 71px;
        }
        
        /* Menú vertical */
        .vertical-menu {
            position: fixed;
            left: -280px;
            top: 71px;
            height: calc(100vh - 71px);
            width: 280px;
            background-color: #000;
            transition: all 0.3s ease;
            z-index: 999;
            overflow-y: auto;
        }

        .vertical-menu.active {
            left: 0;
        }

        .vertical-menu.fixed {
            left: 0;
            width: 60px;
        }

        /* Toggle Fixed Button */
        #toggleFixedMenu {
            position: absolute;
            top: 10px;
            right: 10px;
            background: transparent;
            border: none;
            color: white;
            cursor: pointer;
            padding: 5px;
            z-index: 1000;
        }

    /* Área de contenido principal */
    .content-area {
        flex: 1;
        padding: 20px;
        transition: all 0.3s ease;
        width: 100% !important;
        margin-left: 0 !important;
        position: relative;
    }

    .content-area.menu-active {
        margin-left: 280px !important;
        width: calc(100% - 280px) !important;
    }

    .content-area.menu-fixed {
        margin-left: 60px !important;
        width: calc(100% - 60px) !important;
    }

        .logo {
        margin-left: 20px; /* Ajusta el valor según sea necesario */
    }
        /* Nav Links con animación */
        .nav-link {
            color: white !important;
            padding: 12px 15px;
            display: block;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 0;
            position: relative;
            overflow: hidden;
            white-space: nowrap;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 100%;
            background-color: #dc3545;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .nav-link:hover::before {
            transform: translateX(0);
        }
.nav-link.active::before {
    transform: translateX(0);
}

.nav-link.active {
    color: #dc3545 !important; /* o blanco si prefieres */
    font-weight: bold;
}

        .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Menú compacto */
        .vertical-menu.fixed .nav-link span {
            display: none;
        }

        .vertical-menu.fixed .nav-link i {
            margin-right: 0;
        }

        .vertical-menu.fixed #toggleFixedMenu {
            right: 5px;
        }

        /* Overlay */
        .overlay {
            position: fixed;
            top: 71px;
            left: 0;
            width: 100%;
            height: calc(100% - 71px);
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 998;
        }

        .overlay.active {
            opacity: 1;
            visibility: visible;
        }

        @media (min-width: 768px) {
            .overlay.active {
                background-color: transparent;
                pointer-events: none; /* Dejar pasar los clics en escritorio si se prefiere, o quitar si se quiere cerrar al clic */
            }
        }

        /* Botón de menú */
        .menu-toggle {
            background: none;
            border: none;
            cursor: pointer;
            padding: 10px;
        }

        .menu-toggle .bar {
            display: block;
            width: 25px;
            height: 3px;
            margin: 5px 0;
            background-color: white;
            transition: all 0.3s ease;
        }

        /* Botón de cerrar sesión */
        .btn-cerrar-sesion {
            background: linear-gradient(135deg, #ff2235, #ff2a4a);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-cerrar-sesion:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 71, 87, 0.3);
        }

        /* Footer Styles */
        .footerContacto, .FooterRedes {
            display: flex;
            flex-direction: column;
        }

        .titulosFooter, .titulosFooterR {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 1.2em;
          
        }

        .iconoFooter1, .iconoFooter2 {
            width: 30px;
            margin-right: 10px;
        }
        .footer {
        background-color: #000000;
        color: white;
        padding: 15px;
        width: 100%;
    }

    .footer-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .footerContacto {
        flex: 1;
        max-width: 50%;
    }

    .FooterRedes {
        text-align: right;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .contenedorconosRedes {
        margin-top: 10px;
    }

    /* input.form-control {
    max-width: 120px;
    } */

    /* Estilos para el menú de usuario */
    .user-menu {
        position: relative;
        display: inline-block;
    }

        .user-menu-trigger {
            background: none;
            border: none;
            color: white;
            padding: 8px 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            border-radius: 0;
        }

        .user-menu-trigger:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .user-menu-trigger i {
            transition: transform 0.3s ease;
        }

        .user-menu-trigger.active i {
            transform: rotate(180deg);
        }

        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            border-radius: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            z-index: 1001;
        }

        .user-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .user-info {
            padding: 16px;
            border-bottom: 1px solid #eee;
        }

        .user-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }

        .user-role {
            font-size: 0.875rem;
            color: #666;
        }

        .dropdown-items {
            padding: 8px 0;
        }

        .dropdown-item {
            padding: 8px 16px;
            color: #333;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .btn-cerrar-sesion {
            width: calc(100% - 32px);
            margin: 8px 16px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #ff4757, #ff6b81);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-cerrar-sesion:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 71, 87, 0.2);
        }
/* Estilos para el menú vertical */
.vertical-menu {
    position: fixed;
    left: -280px;
    top: 71px;
    height: calc(100vh - 71px);
    width: 280px;
    background-color: #000;
    transition: all 0.3s ease;
    z-index: 999;
    overflow-y: auto;
    padding-top: 40px; /* Añadir espacio en la parte superior para el botón */
}

    /* Toggle Fixed Button */
    #toggleFixedMenu {
        position: absolute;
        top: 0; /* Cambiar a 0 para posicionarlo en la parte superior */
        right: 0;
        background: transparent;
        border: none;
        color: white;
        cursor: pointer;
        padding: 10px; /* Aumentar el padding para hacer el botón más visible */
        z-index: 1000;
        width: 100%; /* Hacer que el botón ocupe todo el ancho */
        text-align: center; /* Alinear el ícono a la derecha */
        border-bottom: 1px solid rgba(255, 255, 255, 0.1); /* Añadir un borde sutil */
    }

    #toggleFixedMenu:hover {
        background-color: rgba(255, 255, 255, 0.1); /* Añadir efecto hover */
    }

    /* Ajustar el nav-menu para que empiece después del botón */
    .nav-menu {
        padding-top: 0; /* Eliminar el padding superior si existe */
        margin-top: 0; /* Eliminar el margen superior si existe */
    }

    /* Asegurar que los elementos del menú no se encimen */
    .nav-item:first-child {
        margin-top: 0;
    }
    /* Estilos base para la barra de navegación */
.header {
    width: 100%;
    z-index: 1000;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Menú fijo */
.navbar-fijo {
    position: fixed;
    top: 0;
}

/* Menú dinámico */
.navbar-dinamico {
    position: fixed;
    top: 0;
}

/* Clases para mostrar/ocultar */
.navbar-visible {
    transform: translateY(0);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.navbar-hidden {
    transform: translateY(-100%);
}

/* Agregar padding al body cuando el menú es fijo */
body {
    transition: padding-top 0.3s ease;
}

body .navbar-fijo {
    padding-top: auto; /* Ajusta este valor según la altura de tu barra */
}


/*Estilo para los filtros*/
.sidebar {
  width: 250px;
  height: 65vh;
  top: 0;
  left: 0;
  color: white; /* Cambia el color de texto a blanco */
  background-color: #000000; /* Fondo oscuro para buen contraste */  
  padding: 30px;
  box-shadow: 2px 0 5px rgba(0,0,0,0.1);
  overflow-y: auto;
  z-index: 1000;
}

.sidebar h5 {
  font-weight: bold;
  margin-bottom: 20px;
}

.filter-section {
  margin-bottom: 30px;
}

.filter-section h6 {
  font-size: 1rem;
  font-weight: bold;
  margin-bottom: 10px;
}

.form-check, .mb-3 {
  margin-bottom: 10px;
}

/* input.form-control {
  max-width: 120px;
} */

/* Estandarización de colores verdes */
.btn-success, .bg-success, .alert-success, .text-success {
    --bs-success-rgb: 40, 167, 69 !important;
}

.btn-success {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
    color: white !important;
}

.btn-success:hover, .btn-success:focus, .btn-success:active {
    background-color: #218838 !important;
    border-color: #1e7e34 !important;
    color: white !important;
}

.bg-success {
    background-color: #28a745 !important;
}

.alert-success {
    background-color: #d4edda !important;
    border-color: #c3e6cb !important;
    color: #155724 !important;
}

.text-success {
    color: #28a745 !important;
}

/* Clases personalizadas que se encontraron en el proyecto */
.btn-success-custom, .btn-success-c, .btn-success-p, .btn-success-modern {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
    color: white !important;
}

.btn-success-custom:hover, .btn-success-c:hover, .btn-success-p:hover, .btn-success-modern:hover {
    background-color: #218838 !important;
    border-color: #1e7e34 !important;
}

/* Global overrides for rounded buttons and inputs */
.btn, 
.btn-action,
.btn-action-c,
.btn-action-p,
.btn-action-edit,
.btn-custom, 
.btn-login, 
.btn-download,
.btn-primary, 
.btn-secondary, 
.btn-success, 
.btn-danger, 
.btn-warning, 
.btn-info, 
.btn-link,
.btn-cerrar-sesion,
button:not(.menu-toggle):not(#toggleFixedMenu), 
select,
.form-control,
.input-group,
.input-group-text,
.pagination .page-link,
.page-link {
    border-radius: 6px !important;
}
  </style>

</head>
<body>

    
  @php
    use Illuminate\Support\Facades\Auth;
    $userRole = auth()->user()?->puesto ?? 'guest';
  @endphp

   @auth
     <div class="header">
        <div class="d-flex align-items-center">
            <button class="menu-toggle" id="menuToggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
            <a href="/inicio" class="ms-3">
                <img src="{{ $logoPath }}" alt="Logo de la aplicación" style="max-width: 150px;">
            </a>
        </div>
        <div class="ml-auto">
            <div class="user-menu">
                <button class="user-menu-trigger" id="userMenuTrigger">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ auth()->user()->name }} </span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="user-dropdown" id="userDropdown">
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->name }} {{ ucfirst(auth()->user()?->puesto) }}</div>
                        <div class="user-role"></div>
                    </div>
                    
                    
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-cerrar-sesion">
                            <i class="fas fa-sign-out-alt"></i>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @yield('nav')

    <div class="overlay" id="overlay"></div>

    <div class="main-container">
         
        <nav class="vertical-menu" id="verticalMenu">
            <button id="toggleFixedMenu" class="btn btn-light" style="padding-left:-20px" title="Fijar barra">
                <i class="fas fa-thumbtack"></i>
            </button>
            
       <ul style="list-style-type: none; padding: 0; margin: 0; text-align: left;">

    <li class="nav-item">
        <a href="{{ route('profile') }}" class="nav-link menu-item {{ request()->is('profile') ? 'active' : '' }}">
            <i class="fa-solid fa-circle-user"></i> <span>Perfil</span>
        </a>
    </li>
    @if(auth()->user()?->puesto == 'Programador' || auth()->user()?->puesto == 'Administrador')
    <li class="nav-item">
      <a href="{{ route('configuraciones.index') }}" class="nav-link menu-item {{ request()->is('configuraciones') ? 'active' : '' }}">
          <i class="fas fa-cog"></i> <span>Configuración</span>
      </a>
    </li>
     @endif

  @endauth
  @if(auth()->user()?->puesto != 'Mantenimiento')
    <li class="nav-item">
        <a href="/users" class="nav-link menu-item {{ request()->is('users') ? 'active' : '' }}">
           <i class="fa-solid fa-user-plus"></i> <span>Creación usuario</span>
        </a>
  </li>
  @endif

  <li class="nav-item">
        <a href="/cursos" class="nav-link menu-item {{ request()->is('cursos') ? 'active' : '' }}">
            <i class="fa-solid fa-book"></i> <span>Cursos</span>
        </a>
  </li>
 
  @if(auth()->user()?->puesto != 'Operacion')
  <li class="nav-item">
        <a  href="/registro" class="nav-link menu-item {{ request()->is('registro') ? 'active' : '' }}">
            <i class="fa-solid fa-pencil"></i> <span>Registro de participante</span>
        </a>
  </li>
    @endif

   @if(auth()->user()?->puesto != 'Operacion')
  <li class="nav-item">
        <a  href="/participantes" class="nav-link menu-item {{ request()->is('participantes') ? 'active' : '' }}">
           <i class="fa-solid fa-person"></i><span>Participantes</span>
        </a>
  </li>
  @endif
  <li class="nav-item">
        <a href="{{ route('ruta.archivos') }}" class="nav-link menu-item {{ request()->is('archivos') ? 'active' : '' }}">
            <i class="fa-solid fa-folder-tree"></i> <span>Ruta archivos</span>
        </a>
  </li>

   <li class="nav-item">
        <a href="https://drive.google.com/drive/folders/1HRJ_UliysPgzUOm1_XvcLR4MSuAjdODD?usp=sharing" class="nav-link menu-item {{ request()->is('Usuario') ? 'active' : '' }}">
            <i class="fa-solid fa-folder"></i><span>Carpeta de Drive</span>
        </a>
  </li>

  <li class="nav-item">
        <a href="/export-db" class="nav-link menu-item {{ request()->is('export-db') ? 'active' : '' }}">
           <i class="fa-solid fa-file-export"></i><span>Exportar Base de Datos</span>
        </a>
  </li>

  <li class="nav-item">
        <a href="#" onclick="selectFile()" class="nav-link menu-item {{ request()->is('Usuario') ? 'active' : '' }}">
            <i class="fa-solid fa-file-import"></i> <span>Importar Base de Datos</span>
        </a>
  </li>
<li class="nav-item">
        <a href="https://erp.idebmexico.com/inicio" rel="noopener noreferrer" class="nav-link menu-item">
            <i class="fa-solid fa-house"></i> <span>Regresar al Inicio IDEB</span>
        </a>
  </li>
@if(request()->routeIs('cursos.index') && isset($cursos) && isset($subcursos))
<li>
    <div class="sidebar">
        <h5>Filtros de Curso</h5>
        <div class="filter-section">
            <h6>Opciones de Visualización</h6>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="viewOption" id="defaultView" value="default" checked>
                <label class="form-check-label" for="defaultView">
                    Vista Predeterminada
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="viewOption" id="driveView" value="drive">
                <label class="form-check-label" for="driveView">
                    Vista de Links Drive
                </label>
            </div>
        </div>
        <div class="filter-section">
            <h6>Filtros Adicionales</h6>
            <div class="mb-3">
                <label for="instructorFilter" class="form-label">Instructor</label>
                <select id="instructorFilter" class="form-select" onchange="window.location.href='{{ route('cursos.index') }}?instructor=' + this.value + '&search={{ request('search') }}'">
                    <option value="">Todos los Instructores</option>
                    @php
                        // Obtenemos todos los instructores de la BD para el filtro, no solo los de la página actual
                        $instructores = \App\Models\Cursos::whereNotNull('instructor_responsable')
                            ->distinct()
                            ->pluck('instructor_responsable')
                            ->sort();
                    @endphp
                    @foreach($instructores as $instructor)
                        <option value="{{ $instructor }}" {{ request('instructor') == $instructor ? 'selected' : '' }}>{{ $instructor }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="costoFilter" class="form-label">Rango de Costo</label>
                <div class="input-group">
                    <input type="number" id="costMinFilter" class="form-control" placeholder="Mínimo">
                    <input type="number" id="costMaxFilter" class="form-control" placeholder="Máximo">
                </div>
            </div>
        </div>
    </div> 
</li>
@endif


</ul>

     
        </nav>

        <div class="content-area" id="contentArea">
            @if (request()->is('inicio'))
            <style>
                .welcome-container {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 70vh;
                    text-align: center;
                }
                .welcome-message {
                    max-width: 800px;
                    padding: 40px;
                    background-color: #a7f3fe;
                    border-radius: 10px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                }
                .welcome-message h1 {
                    font-size: 2.5rem;
                    font-weight: 300;
                    color: #1b1b1b;
                    margin-bottom: 1rem;
                }
                .welcome-message p {
                    font-size: 1.1rem;
                    color: #575757;
                    line-height: 1.6;
                }
            </style>
            <div class="welcome-container">
                <div class="welcome-message">
                    <h1>Bienvenido, {{ Auth::user()->name }}</h1>
                    <p>Este es su centro de control para la gestión de cursos y participantes. Desde aquí, puede navegar a todas las secciones clave para organizar, supervisar y analizar la información de manera eficiente.</p>
                    <p>Utilice el menú lateral para comenzar.</p>
                </div>
            </div>
            @endif
            @yield('content')
        </div>
    </div>

    <footer class="footer">
        <div class="footer-container">
            <div class="footerContacto">
                <div>
                    <a class="titulosFooter">Contacto</a>
                </div>
                <div style="margin-top: 8px;">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Fuente Alpaca #274, Villa Fontana, San Pedro Tlaquepaque, Jal.</span>
                </div>
                <div style="margin-top: 10px;">
                    <i class="fas fa-envelope"></i>
                    <span>Contacto@idebmexico.com</span>
                </div>
                <div style="margin-top: 10px;">
                    <i class="fas fa-phone"></i>
                    <span>33 1592 2676</span>
                </div>
            </div>
            <div class="FooterRedes">
                <a class="titulosFooterR">Redes</a>
                <div class="contenedorconosRedes">
                    <a href="https://www.facebook.com/IvDEB"><i class="fab fa-facebook fa-2x"></i></a>
                    <a href="https://www.linkedin.com/company/i-deb"><i class="fab fa-linkedin fa-2x"></i></a>
                </div>
                <div style="margin-top: 10px;">
                
                </div>
            </div>
        </div>
    </footer>    

    <form id="importForm" action="{{ route('database.import') }}" method="POST" enctype="multipart/form-data" style="display: none;">
        @csrf
        <input type="file" id="database_file" name="database_file" accept=".sql" onchange="importDatabase()">
    </form>

    <script>
        function selectFile() {
            document.getElementById('database_file').click();
        }

        function importDatabase() {
            const fileInput = document.getElementById('database_file');
            if (fileInput.files.length > 0) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Al importar la base de datos, se sobrescribirán los datos actuales. ¡Esta acción no se puede deshacer!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, importar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Importando...',
                            text: 'Por favor espere mientras se restaura la base de datos.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        document.getElementById('importForm').submit();
                    } else {
                        fileInput.value = ''; // Limpiar el input si se cancela
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
    // Selecciona la barra de navegación con la clase "header"
    const navbar = document.querySelector('.header');
    
    // Obtiene el tipo de menú de la sesión
    const tipoMenu = "{{ session('tipo_menu', 'fijo') }}";
    
    if (tipoMenu === 'fijo') {
        // Para menú fijo, simplemente aplicamos la clase
        navbar.classList.add('navbar-fijo');
    } else {
        // Para menú dinámico, configuramos el comportamiento de scroll
        navbar.classList.add('navbar-dinamico');
        
        // Variables para detectar dirección de scroll
        let lastScrollTop = 0;
        
        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            // Si estamos en la parte superior, siempre mostrar la barra
            if (scrollTop <= 10) {
                navbar.classList.remove('navbar-hidden');
                navbar.classList.add('navbar-visible');
                lastScrollTop = scrollTop;
                return;
            }
            
            // Detectar dirección de scroll
            if (scrollTop > lastScrollTop) {
                // Scroll hacia abajo: ocultar la barra
                navbar.classList.remove('navbar-visible');
                navbar.classList.add('navbar-hidden');
            } else {
                // Scroll hacia arriba: mostrar la barra
                navbar.classList.remove('navbar-hidden');
                navbar.classList.add('navbar-visible');
            }
            
            lastScrollTop = scrollTop;
        });
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const verticalMenu = document.getElementById('verticalMenu');
    const overlay = document.getElementById('overlay');
    const contentArea = document.getElementById('contentArea');
    const toggleFixedMenu = document.getElementById('toggleFixedMenu');
    
    // Obtener el estado guardado del menú
    let isMenuFixed = localStorage.getItem('menuFixed') === 'true';
    let isMenuActive = false;

    // Aplicar el estado guardado al cargar la página
    if (isMenuFixed) {
        verticalMenu.classList.add('fixed');
        verticalMenu.classList.add('active');
        contentArea.classList.add('menu-fixed');
        toggleFixedMenu.innerHTML = '<i class="fas fa-times"></i>';
    }

    function toggleMenu() {
        if (!isMenuFixed) {
            isMenuActive = !isMenuActive;
            menuToggle.classList.toggle('active');
            verticalMenu.classList.toggle('active');
            
            if (isMenuActive) {
                contentArea.classList.add('menu-active');
                overlay.classList.add('active');
            } else {
                contentArea.classList.remove('menu-active');
                overlay.classList.remove('active');
            }
        }
    }

    function toggleFixed() {
        isMenuFixed = !isMenuFixed;
        
        // Guardar el estado en localStorage
        localStorage.setItem('menuFixed', isMenuFixed);
        
        if (isMenuFixed) {
            verticalMenu.classList.add('fixed');
            verticalMenu.classList.add('active');
            contentArea.classList.add('menu-fixed');
            contentArea.classList.remove('menu-active');
            overlay.classList.remove('active');
            toggleFixedMenu.innerHTML = '<i class="fas fa-times"></i>';
        } else {
            verticalMenu.classList.remove('fixed');
            verticalMenu.classList.remove('active');
            contentArea.classList.remove('menu-fixed');
            toggleFixedMenu.innerHTML = '<i class="fas fa-thumbtack"></i>';
            
            // Si el menú estaba abierto antes de fijarlo, restaurar el estado activo si es necesario
            // o simplemente dejarlo cerrado. Aquí lo dejamos cerrado por simplicidad.
        }
        
        isMenuActive = isMenuFixed;
    }

    menuToggle.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', toggleMenu);
    toggleFixedMenu.addEventListener('click', toggleFixed);

    // Cerrar menú en enlaces si no está fijo
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            if (!isMenuFixed && window.innerWidth < 768) {
                toggleMenu();
            }
        });
    });

    // Ajuste responsive
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && !isMenuFixed) {
            overlay.classList.remove('active');
        }
    });
});
        // Agregar el siguiente código para el menú de usuario
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuTrigger = document.getElementById('userMenuTrigger');
            const userDropdown = document.getElementById('userDropdown');
            
            function toggleUserMenu(event) {
                event.stopPropagation();
                userMenuTrigger.classList.toggle('active');
                userDropdown.classList.toggle('active');
            }

            function closeUserMenu() {
                userMenuTrigger.classList.remove('active');
                userDropdown.classList.remove('active');
            }

            userMenuTrigger.addEventListener('click', toggleUserMenu);

            // Cerrar el menú cuando se hace clic fuera
            document.addEventListener('click', function(event) {
                const isClickInside = userMenuTrigger.contains(event.target) || userDropdown.contains(event.target);
                if (!isClickInside) {
                    closeUserMenu();
                }
            });

            // Evitar que el menú se cierre cuando se hace clic dentro
            userDropdown.addEventListener('click', function(event) {
                event.stopPropagation();
            });
        });
    </script>
    @stack('scripts')

    
</body>
</html>
