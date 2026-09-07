<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas del Módulo de Control de Personal (TFJA)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // Endpoints de catálogos para selects en cascada
    Route::prefix('catalogos')->name('catalogos.')->group(function () {
        Route::get('regiones/{region}/salas', [UserController::class, 'salasPorRegion'])
            ->name('salas');

        Route::get('salas/{sala}/areas', [UserController::class, 'areasPorSala'])
            ->name('areas');
    });

    // CRUD de Usuarios
    Route::resource('usuarios', UserController::class)
        ->parameters(['usuarios' => 'usuario'])
        ->except(['create', 'edit', 'show']);

});