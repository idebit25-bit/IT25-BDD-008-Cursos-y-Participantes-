<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('participantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->notNullable();
            $table->string('correo')->unique()->notNullable();
            $table->string('telefono')->notNullable();
            $table->integer('edad')->notNullable();
            $table->text('direccion')->notNullable();
            $table->string('escolaridad')->notNullable();
            $table->string('curp', 18)->unique()->notNullable();

            // Datos de la empresa
            $table->string('razon_social', 200)->notNullable();
            $table->string('empresa')->notNullable();
            $table->string('rfc_empresa', 13)->unique()->notNullable();
            $table->string('puesto')->notNullable();

            // Pago y estado
            $table->decimal('pago', 10, 2)->nullable();
            $table->enum('estado_pago', ['Pendiente', 'Pagado', 'Cancelado'])->default('Pendiente');

            // Curso
            $table->date('fecha_curso')->notNullable();

            // Otros
            $table->boolean('estatus')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('participantes');
    }
};
