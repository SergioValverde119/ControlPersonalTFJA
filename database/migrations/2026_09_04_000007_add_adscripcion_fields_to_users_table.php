<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->after('email');
            $table->foreignId('sala_id')->nullable()->after('region_id');
            $table->foreignId('area_id')->nullable()->after('sala_id');
            $table->boolean('activo')->default(true)->after('password')->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['region_id', 'sala_id', 'area_id', 'activo']);
        });
    }
};