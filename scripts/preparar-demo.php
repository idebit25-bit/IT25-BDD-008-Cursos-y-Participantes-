<?php

// Esquema local provisional: no es una migración del esquema institucional.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Cursos;
use App\Models\User;

if (!app()->environment('local') || config('database.default') !== 'mysql'
    || DB::connection()->getDatabaseName() !== 'gestor_instituto_pruebas') {
    throw new RuntimeException('Sólo se permite la base MySQL local gestor_instituto_pruebas.');
}
if (count(DB::select('SHOW TABLES')) !== 0) {
    throw new RuntimeException('La base debe estar vacía. No se borran ni reemplazan datos.');
}

// El repositorio contiene dos creaciones duplicadas. Se usa la primera de cada tabla.
$duplicadas = [
    '2025_03_03_203644_create_password_reset_tokens_table.php',
    '2025_03_03_204422_create_participant_action_logs_table.php',
];
foreach (glob(__DIR__.'/../database/migrations/*.php') as $path) {
    if (in_array(basename($path), $duplicadas, true)) continue;
    echo basename($path).PHP_EOL;
    $classesBefore = get_declared_classes();
    $migration = require $path;
    if (!is_object($migration)) {
        $added = array_values(array_diff(get_declared_classes(), $classesBefore));
        $migrationClasses = array_values(array_filter($added, fn ($class) =>
            is_subclass_of($class, Illuminate\Database\Migrations\Migration::class)));
        if (count($migrationClasses) !== 1) throw new RuntimeException('Migration ambiguë: '.$path);
        $migration = new $migrationClasses[0];
    }
    $migration->up();
}

if (!Schema::hasColumn('cursos', 'modalidad')) Schema::table('cursos', function (Blueprint $table) {
    $table->string('modalidad')->nullable();
});
if (!Schema::hasColumn('users', 'plain_password')) Schema::table('users', function (Blueprint $table) {
    $table->string('plain_password')->nullable();
});
Schema::create('curso_modalidades', function (Blueprint $table) {
    $table->id();
    $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
    $table->string('modalidad');
});
foreach (['curso_recursos' => 'tipo_recurso', 'curso_evaluaciones' => 'tipo_evaluacion',
    'curso_certificaciones' => 'tipo_certificacion'] as $name => $type) {
    Schema::create($name, function (Blueprint $table) use ($name, $type) {
        $table->id();
        $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
        $table->string($type);
        $table->string('nombre')->nullable();
        $table->text('url')->nullable();
        $table->text('drive_url')->nullable();
        if ($name === 'curso_recursos') $table->text('descripcion')->nullable();
        if ($name === 'curso_certificaciones') {
            $table->boolean('tiene_firma')->default(false);
            $table->date('fecha_registro')->nullable();
        }
        $table->timestamps();
        $table->unique(['curso_id', $type]);
    });
}
Schema::create('curso_pasos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
    $table->unsignedTinyInteger('paso_numero');
    $table->unsignedTinyInteger('porcentaje')->default(0);
    foreach (['nombre', 'documento_url', 'documento_nombre', 'documento_tipo'] as $field) {
        $table->string($field)->nullable();
    }
    $table->unsignedBigInteger('documento_tamano')->nullable();
    $table->timestamps();
});

$password = bin2hex(random_bytes(8));
User::create(['name' => 'Demo', 'apellido' => 'Local', 'email' => 'demo@example.test',
    'password' => $password, 'puesto' => 'Administrador', 'estatus' => 1]);
foreach (require __DIR__.'/cursos-demo.php' as $code => $nombre) {
    $curso = Cursos::create(['nomenclatura' => $code, 'nombre' => $nombre,
        'descripcion' => 'Datos ficticios para desarrollo local', 'status' => 1,
        'modalidad' => 'virtual', 'costo' => 100, 'duracion' => '8 horas',
        'instructor_responsable' => 'Instructor Demo']);
    if ($code === 'ID-A02') {
        $curso->certificaciones()->create(['tipo_certificacion' => 'fecha_registro', 'fecha_registro' => '2026-01-15']);
    }
    if (in_array($code, ['ID-A02', 'ID-A10'])) {
        Cursos::create(['parent_id' => $curso->id, 'nomenclatura' => $code.'-01',
            'nombre' => $nombre.' — Grupo Noviembre 2026', 'status' => 1]);
    }
}
echo "Demo lista. Usuario: demo@example.test\nContraseña: {$password}\n";
