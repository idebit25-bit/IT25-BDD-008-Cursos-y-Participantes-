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
