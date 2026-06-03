<?php

use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::get('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
});

use App\Http\Controllers\CultivoController;

// Añade esto debajo de las rutas existentes
Route::get('/cultivos', [CultivoController::class, 'index'])->name('cultivos.index');
Route::post('/cultivos', [CultivoController::class, 'store'])->name('cultivos.store');

require __DIR__.'/settings.php';
