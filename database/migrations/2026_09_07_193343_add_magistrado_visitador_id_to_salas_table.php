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
        $table->foreignId('magistrado_visitador_id')
            ->nullable()
            ->after('magistrado_visitador')
            ->constrained('users')
            ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('salas', function (Blueprint $table) {
        $table->dropConstrainedForeignId('magistrado_visitador_id');
    });
}
};
