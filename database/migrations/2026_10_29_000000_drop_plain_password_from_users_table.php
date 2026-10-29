<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Elimina la copia en texto plano de las contraseñas; sólo se conserva el hash.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'plain_password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('plain_password');
            });
        }
    }

    /**
     * La columna se recrea vacía: las contraseñas eliminadas no se pueden recuperar.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('users', 'plain_password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('plain_password')->nullable();
            });
        }
    }
};
