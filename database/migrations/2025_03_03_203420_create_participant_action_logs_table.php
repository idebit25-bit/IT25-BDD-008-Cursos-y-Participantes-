<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('participant_action_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->nullable()->constrained('participantes')->nullOnDelete();
            $table->string('nombre_postulante')->nullable();
            $table->string('correo')->nullable();
            $table->string('accion')->notNullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('detalles')->nullable();
            $table->timestamp('fecha_accion')->useCurrent()->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('participant_action_logs');
    }
};
