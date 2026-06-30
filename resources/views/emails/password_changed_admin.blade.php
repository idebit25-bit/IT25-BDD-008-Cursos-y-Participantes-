<!DOCTYPE html>
<html>
<head>
    <title>Notificación de Cambio de Contraseña</title>
</head>
<body>
    <h1>Notificación de Cambio de Contraseña</h1>
    <p>Hola Administrador,</p>
    <p>Un usuario a cambiado su contraseña.</p>
    <p>Detalles:</p>
    <ul>
        <li><strong>Email del Usuario:</strong> {{ $user->email }}</li>
        <li><strong>Fecha y Hora:</strong> {{ $time }}</li>
        <li><strong>Dirección IP:</strong> {{ $ip }}</li>
    </ul>
    <p>Gracias,<br>Tu Aplicación</p>
</body>
</html>
