<?php

use Illuminate\Support\Facades\Route;
use Modules\Futcristao\Http\Controllers\ConfiguracaoController;
use Modules\Futcristao\Http\Controllers\DiaController;
use Modules\Futcristao\Http\Controllers\FutcristaoController;
use Modules\Futcristao\Http\Controllers\GrupoController;

Route::middleware(['web', 'dominio', 'auth'])
    ->prefix('futcristao')
    ->name('futcristao.')
    ->group(function () {
        Route::get('/', [FutcristaoController::class, 'index'])->name('index');

        Route::middleware('gestor')->group(function () {
            Route::get('/configuracoes/modal', [ConfiguracaoController::class, 'edit'])->name('config.edit');
            Route::put('/configuracoes', [ConfiguracaoController::class, 'update'])->name('config.update');

            Route::get('/grupos/criar', [GrupoController::class, 'create'])->name('grupos.create');
            Route::post('/grupos', [GrupoController::class, 'store'])->name('grupos.store');
            Route::get('/grupos/{grupo}/editar', [GrupoController::class, 'edit'])->name('grupos.edit');
            Route::put('/grupos/{grupo}', [GrupoController::class, 'update'])->name('grupos.update');
            Route::delete('/grupos/{grupo}', [GrupoController::class, 'destroy'])->name('grupos.destroy');

            Route::get('/dias/criar', [DiaController::class, 'create'])->name('dias.create');
            Route::post('/dias', [DiaController::class, 'store'])->name('dias.store');
            Route::get('/dias/{dia}/editar', [DiaController::class, 'edit'])->name('dias.edit');
            Route::put('/dias/{dia}', [DiaController::class, 'update'])->name('dias.update');
            Route::delete('/dias/{dia}', [DiaController::class, 'destroy'])->name('dias.destroy');
        });
    });
