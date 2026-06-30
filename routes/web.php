<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\ParticipanteController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\RegistroController;
use App\Models\Cursos;
use App\Http\Controllers\ConfigController;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ArchivosController;
use App\Http\Controllers\RutaCursosController;
use App\Http\Controllers\RutaArchivosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseActionLogController;
use Illuminate\Http\Request;


//Rutas de inicio de sesion:
Route::get('/', function () {
    return view('auth.login');
});
Route::post('/login', [LoginController::class, 'login']);
Route::get('/inicio', function () {
    return view('home');
})->middleware('auth');
// Alias seguro que NO afecta a ninguna otra ruta
Route::get('/Inicio', function () {
    return redirect('/inicio');
});
//Ruta para cerrar sesion:

Route::post('logout', [AuthController::class, 'logout'])->name('logout');

//Ruta para crear usuario de inicio de sesion
// Rutas existentes
// Rutas de usuario
Route::get('/users/papelera', [UserController::class, 'papelera'])->name('users.papelera');
Route::post('/users/{id}/activar', [UserController::class, 'activar'])->name('users.activar');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/profile', [UserController::class, 'profile'])->name('profile');
Route::post('/users/{id}/show-password', [UserController::class, 'showPassword'])->name('users.showPassword');
Route::post('/users/verify-admin', [UserController::class, 'verifyAdminPassword'])->name('users.verifyAdmin');

//Ruta para llenado de formulario de cursos
Route::get('/cursos/ruta', [CursoController::class, 'rutas'])->name('cursos.ruta');
Route::get('/curso/paso1', [CursoController::class, 'crearPaso1'])->name('curso.paso1');
Route::post('/curso/paso1', [CursoController::class, 'guardarPaso1'])->name('curso.paso1.guardar');
Route::get('/curso/paso2', [CursoController::class, 'mostrarPaso2'])->name('curso.paso2');
Route::post('/curso/paso2', [CursoController::class, 'guardarPaso2'])->name('curso.paso2.guardar');
Route::get('/curso/paso3', [CursoController::class, 'mostrarPaso3'])->name('curso.paso3');
Route::post('/curso/paso3', [CursoController::class, 'guardarPaso3'])->name('curso.paso3.guardar');
Route::get('/curso/paso4', [CursoController::class, 'mostrarPaso4'])->name('curso.paso4');
Route::post('/curso/paso4', [CursoController::class, 'guardarPaso4'])->name('curso.paso4.guardar');
Route::get('/curso/paso5', [CursoController::class, 'mostrarPaso5'])->name('curso.paso5');
Route::post('/curso/paso5', [CursoController::class, 'guardarPaso5'])->name('curso.paso5.guardar');
Route::get('/curso/paso6', [CursoController::class, 'mostrarPaso6'])->name('curso.paso6');
//Route::post('/curso/paso6', [CursoController::class, 'guardarPaso6'])->name('curso.paso6.guardar');
Route::get('/curso/paso7', [CursoController::class, 'mostrarPaso7'])->name('curso.paso7');
Route::post('/curso/paso7', [CursoController::class, 'guardarPaso7'])->name('curso.guardar-paso7');
Route::post('/curso/paso6-test', function(Request $request) {
    dd('¡FUNCIONA!', $request->all());
});

//Vista, modificacion, "eliminacion" y consulta de los cursos
Route::get('/cursos', [CursoController::class, 'index'])->name('cursos.index');
Route::get('/cursos/papelera', [CursoController::class, 'papelera'])->name('cursos.papelera');
Route::post('/cursos/{id}/toggle-status', [CursoController::class, 'toggleStatus'])->name('cursos.toggle-status');
Route::resource('cursos', CursoController::class);
Route::get('/curso/editar', [CursoController::class, 'mostrarEdicion'])->name('curso.editar');
//Para obtener la fecha de inicio de cursos para participantes
Route::get('/cursos/{id}/fecha-inicio', [CursoController::class, 'getFechaInicio'])->name('cursos.fecha-inicio');



// Vista, modificacion, "eliminacion" y consulta de los participantes
Route::get('/participantes', [ParticipanteController::class, 'index'])->name('participantes.index');
Route::get('/participantes/papelera', [ParticipanteController::class, 'papelera'])->name('participantes.papelera');
Route::get('/participantes/create', [ParticipanteController::class, 'create'])->name('participantes.create');
Route::get('/participantes/cursos-detalles', [ParticipanteController::class, 'getCursosDetalles'])->name('participantes.cursos-detalles');
Route::post('/participantes', [ParticipanteController::class, 'store'])->name('participantes.store');

// Ruta para mostrar el formulario de edición
Route::get('/participantes/{id}/edit', [ParticipanteController::class, 'edit'])->name('participantes.edit');

// Ruta para actualizar los datos del participante
Route::put('/participantes/{id}', [ParticipanteController::class, 'update'])->name('participantes.update');

// Ruta para eliminar participantes
Route::delete('/participantes/{id}', [ParticipanteController::class, 'destroy'])->name('participantes.destroy');

//Rutas para exportar y importar base de datos
Route::get('/export-db', [DatabaseController::class, 'export'])->name('database.export');
Route::post('/import-db', [DatabaseController::class, 'import'])->name('database.import');

Route::get('/cursos/{id}/pdf', function ($id) {
    $curso = \App\Models\Cursos::findOrFail($id);

    $pdf = Pdf::loadView('cursos.pdf', compact('curso'));
    $filename = \Illuminate\Support\Str::slug($curso->nombre ?: $curso->NombredelCurso) . '.pdf';
    return $pdf->download($filename);
})->name('cursos.pdf');

Route::get('/cursos/{curso}/edit', [CursoController::class, 'edit'])->name('cursos.edit');
Route::get('/cursos/{curso}/edit/{paso}', [CursoController::class, 'editPaso'])->name('cursos.edit.paso');
Route::put('/cursos/{curso}/update/{paso}', [CursoController::class, 'updatePaso'])->name('cursos.update.paso');
Route::delete('/cursos/{curso}', [CursoController::class, 'destroy'])->name('cursos.destroy');

Route::get('/configuraciones', [ConfigController::class, 'index'])->name('configuraciones.index');
Route::post('/configuraciones', [ConfigController::class, 'store'])->name('configuraciones.store');
Route::get('/cursos/{curso}/archivos/{filename}', [CursoController::class, 'verArchivoCurso'])
    ->where('filename', '.*')
    ->name('cursos.archivo');



Route::get('/users/show', [UserController::class, 'show'])->name('users.show');

Route::middleware('auth')->group(function () {
    // Pantalla principal de configuraciones (incluye logs)
    Route::get('/configuraciones', [ConfigController::class, 'index'])->name('configuraciones.index');

    // Activar un curso eliminado
    Route::post('/cursos/{id}/activar', [CursoController::class, 'activarCurso'])->name('cursos.activar');

    Route::post('/configuraciones', [ConfigController::class, 'store'])->name('configuraciones.store');
    Route::get('/configuraciones/logs', [ConfigController::class, 'Cursos_Acciones'])->name('configuraciones.logs');


    Route::get('/configuraciones/logs/{id}', [ConfigController::class, 'showLog'])->name('configuraciones.show-log');

    // Guardar los datos de configuración
    Route::post('/configuraciones/guardar', [ConfigController::class, 'store'])->name('configuraciones.guardar');

    Route::get('/cursos/{curso}', [CursoController::class, 'show'])->name('cursos.show');

    Route::delete('/cursos/{id}/eliminar-definitivo', [CursoController::class, 'eliminarDefinitivo'])->name('cursos.eliminar-definitivo');

    // Activar un participante eliminado
Route::post('/participantes/{id}/activar', [ParticipanteController::class, 'activar'])->name('participantes.activar');

// Eliminar definitivamente un participante
Route::delete('/participantes/{id}/eliminar-definitivo', [ParticipanteController::class, 'eliminarDefinitivo'])->name('participantes.eliminar-definitivo');
});

// Ruta para mostrar el formulario de registro
Route::get('/registro', [RegistroController::class, 'index'])->name('registro.index');
// Ruta para guardar un nuevo participante
Route::post('/registro', [RegistroController::class, 'store'])->name('registro.store');
Route::get('/participantes/filtrar', [ParticipanteController::class, 'filtrar'])->name('participantes.filtrar');
Route::get('/participantes/{id}/detalles', [ParticipanteController::class, 'showDetails'])->name('participantes.detalles');
Route::get('/participantes/{id}/descargar-pdf', [ParticipanteController::class, 'downloadPdf'])->name('participantes.descargar-pdf');

Route::get('/exportar-excel', [ParticipanteController::class, 'exportarExcel'])->name('exportar.excel');
Route::get('/exportar-csv', [ParticipanteController::class, 'exportarCsv'])->name('exportar.csv');

Route::get('/exportar-cursos-excel', [CursoController::class, 'exportarExcel'])->name('exportar.cursos.excel');
Route::get('/exportar-cursos-csv', [CursoController::class, 'exportarCsv'])->name('exportar.cursos.csv');

Route::get('/cursos/{id}/fecha-inicio', [CursoController::class, 'getFechaInicio'])->name('cursos.fecha-inicio');

// Ruta para mostrar el formulario de solicitud de restablecimiento de contraseña (GET)
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request.custom');

// Ruta para procesar el envío del enlace de restablecimiento (POST)
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email.custom');

// Mostrar formulario de restablecimiento de contraseña
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset.custom');

// Procesar el restablecimiento de contraseña
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update.custom');


Route::prefix('archivos')->group(function () {
    // Ruta principal
    Route::get('/', [ArchivosController::class, 'index'])->name('ruta.archivos');

    // Operaciones con archivos
    Route::post('/upload', [ArchivosController::class, 'upload'])->name('archivos.upload');
    Route::get('/download/{archivo}', [ArchivosController::class, 'download'])->name('archivos.download');
    Route::delete('/delete/{archivo}', [ArchivosController::class, 'delete'])->name('archivos.delete');

    // Operaciones con carpetas
    Route::post('/create-folder', [ArchivosController::class, 'createFolder'])->name('archivos.create-folder');
    Route::delete('/delete-folder/{carpeta}', [ArchivosController::class, 'deleteFolder'])
        ->where('carpeta', '.*') // Permite cualquier carácter
        ->name('archivos.delete-folder');

    // Operaciones dentro de una carpeta específica
    Route::get('/open-folder/{carpeta?}', [ArchivosController::class, 'openFolder'])
        ->where('carpeta', '.*') // Permite cualquier carácter
        ->name('archivos.open-folder');
    Route::post('/upload-to-folder/{carpeta}', [ArchivosController::class, 'uploadToFolder'])
        ->where('carpeta', '.*') // Permite cualquier carácter
        ->name('archivos.upload-to-folder');
    Route::post('/create-subfolder/{carpeta}', [ArchivosController::class, 'createSubfolder'])
        ->where('carpeta', '.*') // Permite cualquier carácter
        ->name('archivos.create-subfolder');
    Route::get('/download-from-folder/{carpeta}/{archivo}', [ArchivosController::class, 'downloadFromFolder'])
        ->where('carpeta', '.*') // Permite cualquier carácter
        ->where('archivo', '.*') // Permite cualquier carácter
        ->name('archivos.download-from-folder');
    Route::delete('/delete-from-folder/{carpeta}/{archivo}', [ArchivosController::class, 'deleteFromFolder'])
        ->where('carpeta', '.*') // Permite cualquier carácter
        ->where('archivo', '.*') // Permite cualquier carácter
        ->name('archivos.delete-from-folder');
});

Route::post('/guardar-ruta-cursos', [RutaCursosController::class, 'guardar'])->name('guardar.ruta.cursos');
Route::post('/guardar-ruta-archivos', [RutaArchivosController::class, 'guardar'])->name('guardar.ruta.archivos');
Route::get('/obtener-ruta-archivos', [RutaArchivosController::class, 'obtenerRuta'])->name('obtener.ruta.archivos');
Route::post('/crear-carpeta', [CursoController::class, 'crearCarpeta'])->name('crear.carpeta');
Route::post('/subir-archivo', [CursoController::class, 'subirArchivo'])->name('subir.archivo');

//Obtener ruta
Route::get('/obtener-ruta', [RutaArchivosController::class, 'obtenerRuta'])->name('ruta.obtener');
Route::get('/verificar-ruta', [RutaArchivosController::class, 'verificarRuta'])->name('ruta.verificar');
Route::post('/guardar-ruta-archivos', [RutaArchivosController::class, 'guardarRuta'])->name('guardar.ruta.archivos');
Route::post('/abrir-carpeta', [RutaArchivosController::class, 'abrirCarpeta']);
Route::get('/obtener-ultima-ruta', [RutaArchivosController::class, 'obtenerUltimaRuta'])->name('obtener.ultima.ruta');
Route::get('/obtener-todas-las-rutas', [RutaArchivosController::class, 'obtenerTodasLasRutas']);
Route::post('/verificar-carpeta', [CursoController::class, 'verificarCarpeta'])->name('curso.verificarCarpeta');

// Rutas para permisos
Route::middleware(['auth'])->group(function () {
    Route::get('/permissions', [App\Http\Controllers\RolePermissionController::class, 'getPermissions']);
    Route::post('/check-view-permissions', [App\Http\Controllers\RolePermissionController::class, 'checkViewPermissions']);
});

// Ruta para exportar las tablas
Route::get('/exportar-cursos', [ConfigController::class, 'exportCursos'])->name('exportar.cursos');
Route::get('/exportar-participantes', [ConfigController::class, 'exportParticipantes'])->name('exportar.participantes');

//Abrir visual code
Route::get('/abrir-vscode', [ConfigController::class, 'openInVsCode'])->name('abrir.vscode');

//Actualizar el logo
Route::post('/configuraciones/update-logo', [ConfigController::class, 'updateLogo'])->name('configuraciones.updateLogo');
Route::post('/configuraciones/update-logo-from-list', [ConfigController::class, 'updateLogoFromList'])->name('configuraciones.updateLogoFromList');
Route::get('/course-action-logs', [CourseActionLogController::class, 'index'])->name('course-action-logs.index');

//Forzado de finalizacion del curso
Route::post('/curso/finalizacion-forzada', [CursoController::class, 'finalizacionForzada'])->name('curso.finalizacionForzada');

Route::get('/curso/iniciar', [CursoController::class, 'iniciarCurso'])->name('curso.iniciar');
Route::get('/curso/cancelar', [CursoController::class, 'cancelarCreacion'])->name('curso.cancelar');

Route::post('/crear-carpeta-local', [CursoController::class, 'crearCarpeta'])->name('crear.carpeta.local');
Route::post('/crear-carpeta-local', [RutaArchivosController::class, 'crearCarpeta'])
    ->name('crear.carpeta.local');


    //Ruta para mostrar el subcurso a crear
Route::get('/subcursos/iniciar/{id}', [CursoController::class, 'iniciarSubcursos'])->name('subcursos.iniciar');
Route::get('/cursos/subcursos/{id}', [CursoController::class, 'obtenerSubcursos']);

//Ruta para desactivar los cursos
Route::post('/cursos/{id}/desactivar', [ConfigController::class, 'desactivar'])->name('cursos.desactivar');

Route::post('/subcurso/preparar', [CursoController::class, 'prepararRutaSubcurso'])->name('curso.prepararSubcurso');

