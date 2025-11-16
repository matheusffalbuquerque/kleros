<?php

use Illuminate\Support\Facades\Route;
use Modules\Celulas\Http\Controllers\CelulaController;

Route::middleware(['web', 'dominio'])->group(function () {
    Route::get('/celulas', [CelulaController::class, 'painel'])->name('celulas.painel');
    Route::post('/celulas', [CelulaController::class, 'store'])->name('celulas.store');
    Route::post('/celulas/search', [CelulaController::class, 'search'])->name('celulas.search');
    Route::get('/celulas/novo', [CelulaController::class, 'form_criar'])->name('celulas.form_criar');
    Route::get('/celulas/editar/{id}', [CelulaController::class, 'form_editar'])->name('celulas.form_editar');
    Route::get('/celulas/encontros', [CelulaController::class, 'encontros'])->name('celulas.encontros');
    Route::get('/celulas/encontros/presentes/modal', [CelulaController::class, 'modalAdicionarPresente'])->name('celulas.encontros.presentes.modal');
    Route::get('/celulas/integrantes/{id}', [CelulaController::class, 'integrantes'])->name('celulas.integrantes');
    Route::post('/celulas/integrantes', [CelulaController::class, 'adicionarParticipante'])->name('celulas.integrantes.adicionar');
    Route::get('/celulas/{id}/membros', [CelulaController::class, 'membrosPorCelula'])->name('celulas.membros');
    Route::put('/celulas/{id}', [CelulaController::class, 'update'])->name('celulas.update');
    Route::delete('/celulas/{id}', [CelulaController::class, 'destroy'])->name('celulas.destroy');
});
