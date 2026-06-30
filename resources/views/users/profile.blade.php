@extends('home')
@section('title', '- Perfil de Usuario')
@section('nav')

<style>
    .profile-container {
        margin-top: 100px;
        padding-bottom: 50px;
    }
    .profile-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    .profile-card:hover {
        transform: translateY(-5px);
    }
    .profile-header {
        background: #212529;
        padding: 40px 20px;
        text-align: center;
        color: white;
        border-bottom: 4px solid #0d6efd;
    }
    .profile-header h3 {
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        margin-top: 10px;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        background-color: white;
        border-radius: 50%;
        margin: 0 auto 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 60px;
        color: #333;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    .profile-info-item {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
    }
    .profile-info-item:last-child {
        border-bottom: none;
    }
    .profile-info-icon {
        width: 40px;
        height: 40px;
        background-color: #f8f9fa;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: #333;
        font-size: 1.2rem;
    }
    .profile-info-label {
        font-weight: bold;
        color: #666;
        margin-bottom: 0;
        font-size: 0.9rem;
        text-transform: uppercase;
    }
    .profile-info-value {
        color: #333;
        font-size: 1.1rem;
        margin-bottom: 0;
    }
    .btn-edit-profile {
        border-radius: 6px;
        padding: 10px 30px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }
</style>

<div class="container profile-container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <h1 class="mb-4" style="text-align: center; font-weight: 300;">Mi Perfil</h1>
            
            <div class="card profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3>{{ $user->name }} {{ $user->apellido }}</h3>
                    <p class="mb-0 opacity-75">{{ $user->puesto }}</p>
                </div>
                
                <div class="card-body p-0">
                    <div class="profile-info-item">
                        <div class="profile-info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <p class="profile-info-label">Correo Electrónico</p>
                            <p class="profile-info-value">{{ $user->email }}</p>
                        </div>
                    </div>
                    
                    <div class="profile-info-item">
                        <div class="profile-info-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div>
                            <p class="profile-info-label">Puesto</p>
                            <p class="profile-info-value">{{ $user->puesto }}</p>
                        </div>
                    </div>
                    
                    <div class="profile-info-item">
                        <div class="profile-info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <p class="profile-info-label">Teléfono</p>
                            <p class="profile-info-value">{{ $user->telefono ?: 'No especificado' }}</p>
                        </div>
                    </div>
                    
                    <div class="profile-info-item">
                        <div class="profile-info-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <p class="profile-info-label">Edad</p>
                            <p class="profile-info-value">{{ $user->edad ?: 'No especificada' }} años</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-white border-0 text-center py-4">
                    @if(!in_array($user->puesto, ['Mantenimiento', 'Operacion']))
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-edit-profile shadow-sm">
                            <i class="fas fa-edit mr-2"></i> Editar Perfil
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
