<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\FirmaTramiteController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/firmas/pendientes', [FirmaTramiteController::class, 'index'])
        ->name('firmas.pendientes');

    Route::put('/firmas/{firma}', [FirmaTramiteController::class, 'update'])
        ->name('firmas.update');
});
