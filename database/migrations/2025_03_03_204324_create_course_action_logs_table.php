<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseActionLogsTable extends Migration
{
    public function up()
    {
        Schema::create('course_action_logs', function (Blueprint $table) {
            $table->id()->unsigned();
            $table->unsignedBigInteger('curso_id')->nullable();
            $table->string('nombre_curso', 255)->required();
            $table->unsignedBigInteger('user_id')->required();
            $table->string('accion', 255)->required();
            $table->text('detalles')->nullable();
            $table->timestamp('fecha_accion')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_action_logs');
    }
}
