<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Carpeta de Drive creada automáticamente para cada curso, dentro de la carpeta base del instituto
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            if (!Schema::hasColumn('cursos', 'drive_carpeta_id')) {
                $table->string('drive_carpeta_id')->nullable()->after('modalidad');
            }
            if (!Schema::hasColumn('cursos', 'drive_carpeta_url')) {
                $table->string('drive_carpeta_url', 500)->nullable()->after('drive_carpeta_id');
            }
        });

        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'drive_carpeta_url')) {
                $table->string('drive_carpeta_url', 500)->nullable()->after('logo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropColumn(['drive_carpeta_id', 'drive_carpeta_url']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('drive_carpeta_url');
        });
    }
};
