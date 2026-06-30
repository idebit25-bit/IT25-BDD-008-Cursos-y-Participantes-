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
        Schema::table('participantes', function (Blueprint $table) {
            if (!Schema::hasColumn('participantes', 'ocupacion')) {
                $table->string('ocupacion')->nullable()->after('puesto');
            }
            if (!Schema::hasColumn('participantes', 'N')) {
                $table->string('N')->nullable()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participantes', function (Blueprint $table) {
            $table->dropColumn(['ocupacion', 'N']);
        });
    }
};
