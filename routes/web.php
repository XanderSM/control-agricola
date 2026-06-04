<?php

use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CultivoController;

Route::get('/', [CultivoController::class, 'index'])->name('cultivos.index');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::get('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
});


// Añade esto debajo de las rutas existentes


Route::get('/cultivos', [CultivoController::class, 'index']);
Route::post('/cultivos', [CultivoController::class, 'store']);
// Nuevas rutas para Editar y Eliminar
Route::put('/cultivos/{cultivo}', [CultivoController::class, 'update']);
Route::delete('/cultivos/{cultivo}', [CultivoController::class, 'destroy']);

require __DIR__.'/settings.php';
