<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::inertia('/', 'Welcome')->name('home');

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

// Route::get('/ui', function () {
//     return Inertia::render('Dev/UIPlayground');
// });

Route::middleware(['auth'])->group(function () {
    // Ruta base del módulo nombrada para Wayfinder
    Route::redirect('/nuevo-tramite', '/nuevo-tramite/plazas')->name('nuevo-tramite.index');

    Route::prefix('nuevo-tramite')->name('nuevo-tramite.')->group(function () {
        Route::get('/plazas', function () {
            return Inertia::render('nuevo-tramite/PlazasDisponibles');
        })->name('plazas');

        Route::get('/alta', function () {
            return Inertia::render('nuevo-tramite/Alta');
        })->name('alta');

        Route::get('/baja', function () {
            return Inertia::render('nuevo-tramite/Baja');
        })->name('baja');

        Route::get('/promocion', function () {
            return Inertia::render('nuevo-tramite/Promocion');
        })->name('promocion');

        Route::get('/democion', function () {
            return Inertia::render('nuevo-tramite/Democion');
        })->name('democion');
    });
});

Route::middleware(['auth'])->prefix('estado-tramite')->name('estado-tramite.')->group(function () {
    // Redirección raíz del módulo a la consulta general
    Route::get('/', function () {
        return redirect()->route('estado-tramite.consulta');
    })->name('index');

    // 1. Consulta general de folios
    Route::get('/consulta', function () {
        return Inertia::render('estado-tramite/ConsultaGeneral');
    })->name('consulta');

    // 2. Trámites pendientes o en proceso
    Route::get('/pendientes', function () {
        return Inertia::render('estado-tramite/Pendientes');
    })->name('pendientes');

    // 3. Trámites observados o rechazados
    Route::get('/rechazados', function () {
        return Inertia::render('estado-tramite/Rechazados');
    })->name('rechazados');

    // 4. Histórico general
    Route::get('/historico', function () {
        return Inertia::render('estado-tramite/Historico');
    })->name('historico');
});

Route::middleware(['auth'])->prefix('autorizar')->name('autorizar.')->group(function () {
    // Redirección raíz del módulo a la bandeja de pendientes de Vo.Bo.
    Route::get('/', function () {
        return redirect()->route('autorizar.pendientes-vobo');
    })->name('index');

    // 1. Pendientes de visto bueno
    Route::get('/pendientes-vobo', function () {
        return Inertia::render('autorizar/PendientesVoBo');
    })->name('pendientes-vobo');

    // 2. Trámites listos para autorización definitiva
    Route::get('/por-autorizar', function () {
        return Inertia::render('autorizar/PorAutorizar');
    })->name('por-autorizar');

    // 3. Devoluciones o rechazos de firma
    Route::get('/devueltos', function () {
        return Inertia::render('autorizar/Devueltos');
    })->name('devueltos');

    // 4. Historial de folios firmados/autorizados
    Route::get('/historial-firma', function () {
        return Inertia::render('autorizar/HistorialFirma');
    })->name('historial-firma');
});

require __DIR__.'/settings.php';
require __DIR__.'/usuarios.php';
