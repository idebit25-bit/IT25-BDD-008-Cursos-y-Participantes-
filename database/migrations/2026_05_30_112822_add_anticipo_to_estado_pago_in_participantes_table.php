<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Usamos DB::statement para asegurar la compatibilidad al modificar enums en MySQL
        DB::statement("ALTER TABLE participantes MODIFY COLUMN estado_pago ENUM('Pendiente', 'Anticipo', 'Pagado', 'Cancelado') DEFAULT 'Pendiente' NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE participantes MODIFY COLUMN estado_pago ENUM('Pendiente', 'Pagado', 'Cancelado') DEFAULT 'Pendiente' NOT NULL");
    }
};
