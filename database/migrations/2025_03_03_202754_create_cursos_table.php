<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->string('nomenclatura')->nullable();
            $table->string('nombre')->nullable();
            $table->text('descripcion')->nullable();
            $table->decimal('costo', 8, 2)->nullable();
            $table->string('instructor_responsable')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_termino')->nullable();
            $table->boolean('virtual')->default(false);
            $table->boolean('presencial')->default(false);
            $table->boolean('mixto')->default(false);
            $table->boolean('sin_fecha')->default(false);

            // Redes sociales y enlaces
            $table->string('facebook')->nullable();
            $table->string('drive_facebook')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('drive_linkedin')->nullable();
            $table->string('instagram')->nullable();
            $table->string('drive_instagram')->nullable();

            // Materiales y documentos
            $table->string('temario')->nullable();
            $table->string('drive_temario')->nullable();
            $table->string('itinerario')->nullable();
            $table->string('drive_itinerario')->nullable();
            $table->string('planeacion')->nullable();
            $table->string('drive_planeacion')->nullable();
            $table->boolean('digital')->default(false);
            $table->string('drive_digital')->nullable();
            $table->boolean('impreso_presentable')->default(false);
            $table->string('presentacion')->nullable();

            // Evaluaciones y certificaciones
            $table->string('evaluacion_diagnostica')->nullable();
            $table->string('evaluacion_satisfaccion')->nullable();
            $table->string('evaluacion_final')->nullable();
            $table->string('dc3')->nullable();
            $table->date('fecha_registro_stps')->nullable();
            $table->string('formato_dc5')->nullable();
            $table->boolean('formato_dc5_tiene_firma')->default(false);
            $table->string('certificado_comprobacion')->nullable();
            $table->string('drive_certificado_comprobacion')->nullable();
            $table->boolean('carta_poder_tiene_firma')->default(false);
            $table->string('drive_carta_poder')->nullable();

            // Plataformas externas
            $table->boolean('udemy')->default(false);
            $table->string('enlace_udemy', 500)->default('https://www.udemy.com/');

            // Otros
            $table->boolean('status')->default(true);
            $table->string('duracion')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cursos');
    }
};

