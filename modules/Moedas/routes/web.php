<?php

use Illuminate\Support\Facades\Route;
use Modules\Moedas\Http\Controllers\MoedaController;

Route::middleware(['web', 'dominio', 'auth', 'gestor'])
    ->prefix('moedas')
    ->name('moedas.')
    ->group(function () {
        Route::get('/', [MoedaController::class, 'index'])->name('painel');
        Route::post('/', [MoedaController::class, 'store'])->name('store');
        Route::put('/{moeda}', [MoedaController::class, 'update'])->name('update');
        Route::post('/{moeda}/emitir', [MoedaController::class, 'emitir'])->name('emitir');
        Route::put('/{moeda}/regras', [MoedaController::class, 'updateRules'])->name('regras.update');
    });
