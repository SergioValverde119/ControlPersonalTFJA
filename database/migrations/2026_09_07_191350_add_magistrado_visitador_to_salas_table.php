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
    Schema::table('salas', function (Blueprint $table) {
        $table->string('magistrado_visitador', 20)->nullable()->after('tipo');
    });
}

public function down(): void
{
    Schema::table('salas', function (Blueprint $table) {
        $table->dropColumn('magistrado_visitador');
    });
}
};
