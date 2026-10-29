<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

if (!app()->environment('local') || config('database.default') !== 'mysql'
    || DB::connection()->getDatabaseName() !== 'gestor_instituto_pruebas') {
    throw new RuntimeException('Sólo se permite la base de pruebas local.');
}

DB::transaction(function () {
    foreach (require __DIR__.'/cursos-demo.php' as $code => $nombre) {
        DB::table('cursos')->where('nomenclatura', $code)
            ->where('nombre', 'Curso de prueba '.$code)->update(['nombre' => $nombre]);
        DB::table('cursos')->where('nomenclatura', $code.'-01')
            ->where('nombre', 'Subcurso de prueba '.$code)
            ->update(['nombre' => $nombre.' — Grupo Noviembre 2026']);
    }
});
foreach (DB::table('cursos')->orderBy('nomenclatura')->get(['nomenclatura', 'nombre']) as $curso) {
    echo $curso->nomenclatura.': '.$curso->nombre.PHP_EOL;
}
