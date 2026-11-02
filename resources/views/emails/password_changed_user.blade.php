<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tu contraseña fue cambiada</title>
</head>
<body>
    <p>Hola {{ $user->name }},</p>
    <p>La contraseña de tu cuenta en el Gestor Instituto I-DEB se cambió el <strong>{{ $time }}</strong>.</p>
    <p>Por seguridad se cerraron las sesiones que tenías abiertas; vuelve a iniciar sesión con tu nueva contraseña.</p>
    <p><strong>Si no fuiste tú</strong>, contacta de inmediato al administrador del sistema.</p>
    <p>Gracias,<br>Capital Humano - I-DEB</p>
</body>
</html>
