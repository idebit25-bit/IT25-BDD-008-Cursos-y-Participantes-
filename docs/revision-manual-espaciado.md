# Revisión manual de separación de controles

Cambios locales sobre el commit `2389358`. Revisar en el puerto **8000**.
La comparación de SofiSantillan en 8001 conserva sus estilos originales.

## Alcance: 41 vistas

Se carga la hoja compartida en las plantillas `home` (17 vistas) y
`layouts.app` (21 vistas), más tres pantallas independientes de autenticación.
La cobertura indica que reciben las reglas; no implica validación visual individual.
No incluye PDF, correos ni componentes de la plantilla alternativa Tailwind.

| Área | Cantidad | Vistas para revisar |
| --- | ---: | --- |
| Cursos | 19 | Listado, detalle, edición general, papelera, rutas; alta pasos 1–7; edición pasos 1–7. |
| Participantes y registro | 6 | Listado, alta, edición, detalle, papelera y registro. |
| Usuarios | 5 | Listado, alta, edición, papelera y perfil. |
| Archivos | 2 | Explorador principal y carpeta abierta. |
| Configuración y logs | 3 | Configuración, listado de logs y detalle de log. |
| Autenticación | 6 | Login, registro de usuario, verificación, confirmar contraseña, solicitar recuperación y restablecer contraseña. |
| Total | 41 | |

## Prioridad de revisión

1. Cursos: filtro mínimo/máximo, buscador y botones de acciones.
2. Edición de curso: abrir un curso de prueba y revisar los siete pasos;
   iconos, campos, cargas de archivos, selects y botones deben estar separados.
3. Alta de curso: revisar los mismos siete pasos durante un alta de prueba.
4. Alta y edición de participantes; formulario de registro.
5. Alta y edición de usuarios, incluidos indicadores de modificación y modales.
6. Login y recuperación: el botón para mostrar contraseña tiene su propio espacio.
7. Listados, papeleras, configuración, logs y explorador de archivos.

Algunas pantallas requieren un registro, una carpeta, un log o un enlace de
recuperación válido; acceder desde su flujo normal en la aplicación.

## Criterios visuales

- Espacio compartido de 2 px entre las piezas de grupos de controles, filtros,
  botones agrupados y controles de contraseña (`--control-gap` en la hoja compartida).
- Cada campo conserva sus cuatro bordes y esquinas redondeadas.
- Etiquetas separadas de sus campos; filas con separación vertical.
- Mensajes de error en una línea propia y borde de validación visible.
- Campos y botones de contraseña separados, sin botones sobre el texto.
- Revisar escritorio y ventana estrecha: controles legibles y sin solapamiento;
  la tabla puede desplazarse horizontalmente.
- Comprobar foco con Tab, selects, entrada de archivos y botones de mostrar contraseña.

## Archivos incluidos por área

- Cursos: `cursos/index`, `show`, `edit`, `papelera`, `ruta`,
  `paso1` a `paso7`, `edit-paso1` a `edit-paso7`.
- Participantes: `participantes/index`, `create`, `edit`, `detalles`, `papelera`;
  `registro/index`.
- Usuarios: `users/index`, `create`, `edit`, `papelera`, `profile`.
- Archivos: `archivos/index`, `folder`.
- Configuración: `configuraciones/index`, `logs`, `show-log`.
- Autenticación: `auth/login`, `register`, `verify`, `passwords/confirm`,
  `passwords/email`, `passwords/reset`.

Todos los nombres anteriores corresponden a archivos `.blade.php` dentro de
`resources/views`.
