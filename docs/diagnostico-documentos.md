# Diagnóstico de documentos — 28 de octubre de 2026

## Alcance y evidencia

Revisión estática del código local. Pendiente una carga controlada con la aplicación
funcional; no se comprobó el servidor institucional ni su configuración efectiva.

## Recorrido de archivos

| Mecanismo | Destino según el código | Referencia |
| --- | --- | --- |
| Archivos adjuntos de cursos | `storage/app/public/cursos/{id}/{timestamp}_{nombre}` | `CursoController::guardarArchivosCurso` |
| Presentación adjunta del paso 6 | Carpeta local del curso | `guardarPaso6` y `updatePaso` |
| Explorador de archivos | `mi_carpeta` en el disco predeterminado | `ArchivosController::upload` |
| Carga en subcarpetas del explorador | `mi_carpeta/{carpeta}` en disco `public` | `ArchivosController::uploadToFolder` |
| Enlaces Drive de formularios | Campos `drive_url` en registros relacionados | `CursoController` |

Los discos `local` y `public` apuntan a `storage/app/public` en
`config/filesystems.php`. El explorador depende además de `FILESYSTEM_DISK`;
su valor local actual es `local`. Un servidor puede tener otra configuración.

El proyecto registra un adaptador Google Drive con OAuth y carpeta configurables.
Las cargas de cursos revisadas usan `move()` sobre rutas locales, sin llamar a
ese adaptador. Guardar una URL de Drive no transfiere el archivo seleccionado.
El enlace del menú a Drive sólo abre una carpeta externa.

## Consulta y reemplazo

`CursoRecurso::getArchivoPublicoUrlAttribute` conserva URLs HTTP externas y
convierte rutas de cursos en la ruta `cursos.archivo`. El controlador devuelve
el archivo físico y responde 404 si falta.

El ayudante de carga actualiza la referencia por curso y tipo de recurso con
`updateOrCreate`. No elimina el archivo físico anterior en ese método: un
reemplazo puede dejar una copia anterior sin referencia. El nombre usa segundos,
por lo que el mismo nombre cargado en el mismo segundo puede colisionar.

El explorador conserva el nombre original. Repetir nombre y carpeta puede
reemplazar contenido. Sus operaciones de eliminación usan `Storage::delete` o
`deleteDirectory`; no se observa sincronización explícita con Drive.

## Verificación pendiente

1. Completar dependencias e importar un esquema MySQL compatible en desarrollo.
2. Cargar un documento sintético por cada flujo y comprobar ruta física y registro.
3. Reabrir el curso, consultar el archivo y probar un reemplazo controlado.
4. Probar borrado y papelera con archivos sintéticos; distinguir registros de archivos.
5. Contrastar con la configuración institucional antes de afirmar su destino real.

## Dependencia del entorno

La base local `gestor_instituto` está vacía. Las migraciones disponibles crean una
versión de `cursos` con documentos en columnas, mientras el código utiliza tablas
relacionadas como `curso_recursos` y `curso_certificaciones`, sin migraciones de
creación encontradas. Se necesita el respaldo/esquema correspondiente antes de
validar funcionalidad o decidir cambios de estructura.

## Primer cambio de listado

El listado principal pasa de fecha de creación descendente a nomenclatura
ascendente, con ID interno como desempate, antes de paginar. Esto corresponde a
ejemplos con consecutivo de ancho fijo como `ID-A02`, `ID-A10`, `ID-B01`.
No constituye orden natural para números sin ceros como `ID-A2` y `ID-A10`;
debe contrastarse con las nomenclaturas reales antes de validar el requisito.

## Prueba de carga, consulta y reemplazo (29 de octubre de 2026)

Se probó por HTTP en la demo con el archivo DC5 del paso 7 (edición del curso `ID-A02`):

| Operación | Resultado observado |
|---|---|
| Carga | El archivo se guarda en `storage/app/public/cursos/4/<marca>_<id>_<nombre>.pdf` y `curso_recursos.url` apunta a `cursos/4/...`. No se sube a Drive. |
| Consulta | `/cursos/4/archivos/<archivo>` responde 200 (`application/pdf`). Sólo PDF e imágenes se abren en el navegador; los demás tipos se descargan. Requiere sesión. |
| Subcurso que hereda STPS | El enlace del subcurso apunta al archivo del curso inicial (`cursos/4/...`). |
| Tipo no permitido (`.html`) | Se rechaza con mensaje visible; no se escribe en disco. |
| Reemplazo | Se guarda un archivo nuevo y el registro apunta a él, pero **el archivo anterior queda en disco sin referencia**. |
| Eliminación | No existe una acción para borrar un documento de curso; sólo se reemplaza. |

Pendiente de decidir con la institución: si al reemplazar se debe borrar el archivo
anterior o conservarlo como historial, y si se requiere respaldo en Drive.
