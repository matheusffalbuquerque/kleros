<?php

use Illuminate\Support\Facades\Route;
use Modules\Drive\Http\Controllers\DriveController;

Route::middleware(['web', 'dominio', 'auth', 'gestor'])
    ->prefix('drive')
    ->name('drive.')
    ->group(function () {
        Route::get('/', [DriveController::class, 'index'])->name('painel');
        Route::post('/upload', [DriveController::class, 'upload'])->name('upload');
        Route::post('/pastas', [DriveController::class, 'storeFolder'])->name('pastas.store');
        Route::delete('/item', [DriveController::class, 'destroy'])->name('remover');
    });
