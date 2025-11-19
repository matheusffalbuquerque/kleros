<?php

use Illuminate\Support\Facades\Route;
use Modules\Batismo\Http\Controllers\BatismoController;

Route::middleware(['web', 'dominio', 'auth', 'gestor'])
    ->prefix('batismo')
    ->name('batismo.')
    ->group(function () {
        Route::get('/', [BatismoController::class, 'index'])->name('painel');
        Route::get('/agendar/modal', [BatismoController::class, 'modalAgendar'])->name('agendar.modal');
        Route::post('/agendar', [BatismoController::class, 'agendar'])->name('agendar');
        Route::patch('/agendamentos/{agendamento}', [BatismoController::class, 'atualizarStatus'])->name('agendamentos.atualizar');
    });
