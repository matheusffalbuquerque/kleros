<?php

use Illuminate\Support\Facades\Route;
use Modules\Projetos\Http\Controllers\ProjetoController;

Route::middleware(['web', 'dominio', 'auth'])
    ->prefix('projetos')
    ->name('projetos.')
    ->group(function () {
        Route::get('/', [ProjetoController::class, 'index'])->name('painel');

        Route::get('/criar', [ProjetoController::class, 'formCriar'])->name('form_criar')->middleware('gestor');
        Route::get('/{projeto}/editar', [ProjetoController::class, 'formEditar'])->name('form_editar')->middleware('gestor');

        Route::post('/', [ProjetoController::class, 'store'])->name('store')->middleware('gestor');
        Route::put('/{projeto}', [ProjetoController::class, 'update'])->name('update')->middleware('gestor');
        Route::delete('/{projeto}', [ProjetoController::class, 'destroy'])->name('delete')->middleware('gestor');

        Route::post('/{projeto}/listas', [ProjetoController::class, 'storeLista'])->name('listas.store')->middleware('gestor');
        Route::post('/{projeto}/listas/{lista}/cards', [ProjetoController::class, 'storeCard'])->name('cards.store')->middleware('gestor');
        Route::get('/{projeto}', [ProjetoController::class, 'show'])->name('exibir');

    });
