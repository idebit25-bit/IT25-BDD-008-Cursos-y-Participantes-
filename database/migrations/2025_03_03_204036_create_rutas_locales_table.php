<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRutasLocalesTable extends Migration
{
    public function up()
    {
        Schema::create('rutas_locales', function (Blueprint $table) {
            $table->id()->unsigned();
            $table->unsignedBigInteger('id_cursos')->nullable();
            $table->string('nombre_carpeta', 255)->required();
            $table->string('ruta_nombre_carpeta', 255)->required();
            $table->string('rutacompleta', 255)->required();
            $table->string('rutaformatosflyer', 255)->nullable();
            $table->string('rutaSinFecha', 255)->nullable();
            $table->string('rutaFacebook', 255)->nullable();
            $table->string('rutaLinkedIn', 255)->nullable();
            $table->string('rutaInstagram', 255)->nullable();
            $table->string('rutaTemario', 255)->nullable();
            $table->string('rutaItinerario', 255)->nullable();
            $table->string('rutaPlaneacion', 255)->nullable();
            $table->string('rutaMaterialdeapoyo', 255)->nullable();
            $table->string('rutacursoenlinea', 255)->nullable();
            $table->string('rutapresentacion', 255)->nullable();
            $table->string('rutaevaluaciones', 255)->nullable();
            $table->string('rutaEvaluacionDiagnostica', 255)->nullable();
            $table->string('rutaEvaluacionSatisfaccion', 255)->nullable();
            $table->string('rutaEvaluacionFinal', 255)->nullable();
            $table->string('rutaDC5', 255)->nullable();
            $table->string('rutacarpetaDC5', 255)->nullable();
            $table->string('rutaCertificadoComprobacion', 255)->nullable();
            $table->string('rutacartapoder', 255)->nullable();
            $table->string('rutaUdemy', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rutas_locales');
    }
}
