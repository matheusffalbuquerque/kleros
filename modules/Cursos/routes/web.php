<?php

use Illuminate\Support\Facades\Route;
use Modules\Cursos\Http\Controllers\CursoController;

Route::middleware(['web', 'dominio', 'auth', 'gestor'])->group(function () {
    Route::get('/cursos', [CursoController::class, 'index'])->name('cursos.index');
    Route::get('/cursos/novo', [CursoController::class, 'form_criar'])->name('cursos.form_criar');
    Route::post('/cursos', [CursoController::class, 'store'])->name('cursos.store');
    Route::delete('/cursos/{id}', [CursoController::class, 'destroy'])->name('cursos.destroy');
});
