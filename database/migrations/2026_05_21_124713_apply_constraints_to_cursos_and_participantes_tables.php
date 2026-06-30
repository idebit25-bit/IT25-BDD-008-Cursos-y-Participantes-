<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Aplicar unicidad a la nomenclatura en la tabla cursos
        Schema::table('cursos', function (Blueprint $table) {
            // Primero nos aseguramos de que no sea null si queremos que sea único y obligatorio, 
            // o simplemente aplicamos unique si permitimos nulls (pero el usuario quiere que sea como el ID/CURP)
            $table->string('nomenclatura')->nullable(false)->change();
            $table->unique('nomenclatura');
        });

        // Asegurar que CURP sea única y teléfono tenga longitud adecuada en participantes
        Schema::table('participantes', function (Blueprint $table) {
            // El curp ya tenía unique() en la migración original, pero lo reafirmamos o corregimos si es necesario
            // El teléfono debe ser de 10 dígitos. Cambiamos el tipo a string(10) para forzarlo en BD.
            $table->string('telefono', 10)->change();
            
            // Si por alguna razón no fuera único el CURP (aunque la migración original decía que sí)
            // $table->unique('curp'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropUnique(['nomenclatura']);
            $table->string('nomenclatura')->nullable()->change();
        });

        Schema::table('participantes', function (Blueprint $table) {
            $table->string('telefono', 255)->change();
        });
    }
};
