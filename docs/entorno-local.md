# Entorno MySQL de demostración

Se utiliza `gestor_instituto_pruebas`, separada de `gestor_instituto`.
Los datos son ficticios y el esquema se deduce del código; no es una copia del hosting.

## Preparación

Configurar `.env` con `APP_ENV=local`, `DB_CONNECTION=mysql` y
`DB_DATABASE=gestor_instituto_pruebas`, además de las credenciales MySQL locales.
Crear esa base vacía. Instalar dependencias y compilar recursos:

```powershell
php -d extension=gd C:/ProgramData/ComposerSetup/bin/composer.phar install --no-interaction --prefer-dist
npm ci --no-audit --no-fund
npm run build
php artisan key:generate
php scripts/preparar-demo.php
php -d extension=gd artisan serve --host=127.0.0.1 --port=8000
```

El comando de Composer corresponde a la instalación XAMPP de este equipo.
`-d extension=gd` habilita GD para ese proceso sin cambiar el PHP global.

El script muestra una contraseña aleatoria para `demo@example.test`, crea un
administrador y siete cursos generales con dos subcursos. Hay un curso general
con fecha STPS y otro sin fecha para probar los dos escenarios.

El script rechaza bases no vacías; no elimina ni restaura registros.
Sólo debe ejecutarse una vez sobre la base de pruebas recién creada.

## Limitaciones del esquema

Se ejecutan las migraciones históricas directamente salvo las dos creaciones
duplicadas de `password_reset_tokens` y `participant_action_logs`. Después se
añaden las tablas relacionadas y columnas requeridas por los modelos actuales.
No se registra un historial ficticio de migraciones: **no ejecutar `artisan migrate`
sobre esta demo**, pues las migraciones históricas volverían a crear tablas.

La reconstrucción es exclusivamente una herramienta de desarrollo. Antes de
preparar migraciones para el hosting se deberá comparar su esquema real y
verificar nombres, tipos, restricciones y referencias de documentos.

Los archivos subidos en la demo usan el almacenamiento local de este checkout.
No se configura acceso al Drive institucional.

## Verificación realizada

El 28 de octubre de 2026 se completaron Composer y la compilación Vite.
Con la cuenta demo se comprobó por HTTP el inicio de sesión, listado de cursos,
segunda página, búsqueda por `ID-A`, dos pantallas de edición del paso 7 y
Participantes (respuestas 200). El listado mostró `ID-A01`, `ID-A02`, `ID-A10`,
`ID-A11`, `ID-B01` en la primera página.

Esto verifica el arranque y esas lecturas, no todos los guardados ni la herencia
STPS, que sigue pendiente de implementación.

## Usuarios de prueba por rol (29 de octubre de 2026)

Para probar permisos se añadieron a la demo, con la contraseña `Prueba12345`:

| Correo | Puesto | Estado |
|---|---|---|
| `mante@example.test` | Mantenimiento | Activo |
| `opera@example.test` | Operacion | Activo |
| `inactivo@example.test` | Administrador | Desactivado (no puede entrar) |

Permisos aplicados en el servidor (`routes/web.php`):

- **Programador / Administrador:** todo, incluidos usuarios, base de datos,
  configuración, explorador de archivos, papeleras y eliminación.
- **Mantenimiento:** consultar, crear y editar cursos y participantes; sin eliminar.
- **Operacion:** sólo consulta y reportes (Excel/CSV/PDF).

La columna `users.plain_password` se eliminó de esta demo directamente. En el
hosting la elimina la migración `2026_10_29_000000_drop_plain_password_from_users_table`.

## Herencia STPS en subcursos

Un subcurso usa la fecha de registro STPS, DC5, certificado de comprobación y
carta poder de su curso inicial cuando éste tiene fecha de registro STPS y el
subcurso no capturó datos propios. UDEMY siempre es propio. Si el subcurso ya
tenía datos propios, se conservan; el paso 7 ofrece "Usar datos del curso inicial".

En la demo: `ID-A02` (con alta) → `ID-A02-01` hereda; `ID-A10` recibió alta
durante las pruebas y `ID-A10-01` ya usa sus datos.

## Correo (recuperación de contraseña)

Se usa un solo buzón (`capital.humano@i-deb.com.mx`, SMTP de IONOS) para enviar los enlaces de restablecimiento y para recibir el aviso de cambio de contraseña. Variables en `.env`:

```
MAIL_MAILER=smtp
MAIL_SCHEME=smtps
MAIL_HOST=smtp.ionos.mx
MAIL_PORT=465
MAIL_USERNAME=capital.humano@i-deb.com.mx
MAIL_PASSWORD=        # contraseña del buzón, nunca se versiona
MAIL_FROM_ADDRESS=capital.humano@i-deb.com.mx
MAIL_FROM_NAME="Capital Humano - I-DEB"
MAIL_ADMIN_ADDRESS=capital.humano@i-deb.com.mx   # opcional; si falta se usa MAIL_FROM_ADDRESS
```

Después de editar `.env`: `php artisan config:clear`.

Prueba rápida de envío:

```
php artisan tinker --execute="Mail::raw('Prueba de correo del Gestor', fn(\$m) => \$m->to('capital.humano@i-deb.com.mx')->subject('Prueba SMTP'));"
```

- El enlace del correo usa `APP_URL`; en local apunta a `http://127.0.0.1:8000` y sólo abre en el mismo equipo.
- Si el envío falla, el usuario ve "No se pudo enviar el correo" y el detalle queda en `storage/logs/laravel.log`. Si falla el aviso al administrador, la contraseña sí se cambia.
- 30/10/2026: desde la red de trabajo no salen los puertos SMTP (465, 587 ni 25, tampoco a Gmail). Para probar en local hay que usar otra red (p. ej. datos del celular) o probar directamente en el hosting.

## Carpeta de Drive por curso

Al guardar el paso 1 de un curso (crear o editar) se crea en Drive la carpeta `NOMENCLATURA - Nombre` dentro de la carpeta base; si después cambia la nomenclatura o el nombre, la carpeta se renombra. Cada subcurso va dentro de la carpeta de su curso inicial. El enlace queda en `cursos.drive_carpeta_url` y aparece como botón "Carpeta en Drive" en el detalle y en la edición del curso. Si Drive falla, el curso se guarda igual, el error queda en `storage/logs/laravel.log` y aparece el botón "Crear carpeta en Drive" para reintentar.

Activación (una sola vez, con la cuenta de Google dueña de la carpeta):

1. En https://console.cloud.google.com crear un proyecto y habilitar **Google Drive API**.
2. En *IAM y administración → Cuentas de servicio* crear una cuenta, y en *Claves* agregar una clave **JSON**.
3. Guardar el archivo como `storage/app/google-drive.json` (está ignorado por git). En `.env`:
   `GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON=storage/app/google-drive.json` y `php artisan config:clear`.
4. En Drive, compartir la carpeta base como **Editor** con el correo de la cuenta de servicio (`...@...iam.gserviceaccount.com`; también se muestra en Configuraciones).
5. En *Configuraciones → Carpeta de Drive* pegar la URL de la carpeta base y guardar. Debe aparecer "Conectado con Google Drive".

Alternativa: OAuth con `GOOGLE_DRIVE_CLIENT_ID`, `GOOGLE_DRIVE_CLIENT_SECRET` y `GOOGLE_DRIVE_REFRESH_TOKEN`. Si la app de OAuth queda en modo "Prueba", Google vence el refresh token a los 7 días.

Los cursos que ya existían obtienen su carpeta la próxima vez que se guarde su paso 1 o con el botón "Crear carpeta en Drive". Los archivos que se cargan en los pasos se siguen guardando en el servidor; subirlos a esa carpeta queda para RF-03.
