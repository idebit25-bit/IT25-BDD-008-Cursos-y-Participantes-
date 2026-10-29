{{-- Errores de validación y mensajes de sesión. $soloErrores = true omite los mensajes
     de éxito/error para páginas que ya los muestran por su cuenta. --}}
@php
    $mostrarSesion = empty($soloErrores) && (session('error') || session('success'));
@endphp
@if ($errors->any() || $mostrarSesion)
    <div class="container mt-3">
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if ($mostrarSesion && session('error'))
            <div class="alert alert-danger shadow-sm" role="alert">{{ session('error') }}</div>
        @endif
        @if ($mostrarSesion && session('success'))
            <div class="alert alert-success shadow-sm" role="status">{{ session('success') }}</div>
        @endif
    </div>
@endif
