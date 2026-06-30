<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantActionLogsTable extends Migration
{
    public function up()
    {
        Schema::create('participant_action_logs', function (Blueprint $table) {
            $table->id()->unsigned();
            $table->unsignedBigInteger('participant_id')->nullable();
            $table->string('nombre_postulante', 255)->nullable();
            $table->string('correo', 255)->nullable();
            $table->string('accion', 255)->required();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('detalles')->nullable();
            $table->timestamp('fecha_accion')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('participant_action_logs');
    }
}
