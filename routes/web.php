<?php

use App\Http\Controllers\DocumentoTramiteController;
use App\Http\Controllers\EstadoTramiteController;
use App\Http\Controllers\FirmaTramiteController;
use App\Http\Controllers\NuevoTramiteController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Rutas Públicas / Redirección Raíz
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Autenticación Requerida)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Tablero principal
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Módulo: Nuevo Trámite (Fase 1 - Solicitante)
    |----------------------------------------------------------------------
    */
    Route::redirect('/nuevo-tramite', '/nuevo-tramite/plazas')->name('nuevo-tramite.index');

    Route::prefix('nuevo-tramite')->name('nuevo-tramite.')->group(function () {
        Route::get('/plazas', [NuevoTramiteController::class, 'plazas'])->name('plazas');
        Route::get('/alta', [NuevoTramiteController::class, 'alta'])->name('alta');
        Route::post('/alta', [NuevoTramiteController::class, 'store'])->name('alta.store');

        Route::get('/baja', [NuevoTramiteController::class, 'baja'])->name('baja');
        Route::get('/promocion', [NuevoTramiteController::class, 'promocion'])->name('promocion');
        Route::get('/democion', [NuevoTramiteController::class, 'democion'])->name('democion');
    });

    /*
    |----------------------------------------------------------------------
    | Módulo: Estado de Trámite (Consulta y Seguimiento Institucional)
    |----------------------------------------------------------------------
    */
    Route::prefix('estado-tramite')->name('estado-tramite.')->group(function () {
        Route::get('/', [EstadoTramiteController::class, 'index'])->name('index');
        Route::get('/consulta', [EstadoTramiteController::class, 'index'])->name('consulta');

        Route::get('/pendientes', fn () => Inertia::render('estado-tramite/Pendientes'))->name('pendientes');
        Route::get('/rechazados', fn () => Inertia::render('estado-tramite/Rechazados'))->name('rechazados');
        Route::get('/historico', fn () => Inertia::render('estado-tramite/Historico'))->name('historico');

        // Expediente individual
        Route::get('/{tramite}', [EstadoTramiteController::class, 'show'])->name('show');
    });

    /*
    |----------------------------------------------------------------------
    | Módulo: Autorizar (Vistas de Bandejas para AppHeader y Navegación)
    |----------------------------------------------------------------------
    */
    Route::redirect('/autorizar', '/autorizar/pendientes-vobo')->name('autorizar.index');

    Route::prefix('autorizar')->name('autorizar.')->group(function () {
        Route::get('/pendientes-vobo', fn () => Inertia::render('autorizar/PendientesVoBo'))->name('pendientes-vobo');
        Route::get('/por-autorizar', fn () => Inertia::render('autorizar/PorAutorizar'))->name('por-autorizar');
        Route::get('/devueltos', fn () => Inertia::render('autorizar/Devueltos'))->name('devueltos');
        Route::get('/historial-firma', fn () => Inertia::render('autorizar/HistorialFirma'))->name('historial-firma');
    });

    /*
    |----------------------------------------------------------------------
    | Módulo: Firmas (Lógica y Transición de Estados del Circuito)
    |----------------------------------------------------------------------
    */
    Route::prefix('firmas')->name('firmas.')->group(function () {
        Route::get('/pendientes', [FirmaTramiteController::class, 'index'])->name('pendientes');
        Route::put('/{firma}', [FirmaTramiteController::class, 'update'])->name('update');
    });

    /*
    |----------------------------------------------------------------------
    | Módulo: Expediente Transaccional de Documentos (PDF Seguro)
    |----------------------------------------------------------------------
    */
    Route::prefix('documentos-tramite')->name('documentos-tramite.')->group(function () {
        Route::get('/{documento}', [DocumentoTramiteController::class, 'show'])->name('show');
        Route::get('/{documento}/descargar', [DocumentoTramiteController::class, 'download'])->name('download');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/usuarios.php';
