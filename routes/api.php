<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConfiguracaoController;
use App\Http\Controllers\Api\MensagemController;
use App\Http\Controllers\Api\NoticiasController;
use App\Http\Controllers\Api\PodcastsController;
use App\Http\Controllers\Api\LivrariaController as ApiLivrariaController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\NotificacaoTesteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['congregacao'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/registro', [AuthController::class, 'register']);
    Route::get('/configuracao/{codigo}', [ConfiguracaoController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/noticias', [NoticiasController::class, 'index']);
        Route::get('/noticias/{id}', [NoticiasController::class, 'show']);

        Route::get('/podcasts', [PodcastsController::class, 'index']);
        Route::get('/podcasts/{id}', [PodcastsController::class, 'show']);

        Route::get('/mensagens', [MensagemController::class, 'index']);
        Route::post('/mensagens', [MensagemController::class, 'store']);

        Route::get('/livraria', [ApiLivrariaController::class, 'index']);
        Route::get('/livraria/{id}', [ApiLivrariaController::class, 'show']);
        Route::post('/livraria/{id}/comprar', [ApiLivrariaController::class, 'comprar']);
        Route::get('/livraria/meus-livros', [ApiLivrariaController::class, 'meusLivros']);

        Route::post('/notificacao/teste', [NotificacaoTesteController::class, 'enviar']);
        Route::get('/sincronizar', [SyncController::class, 'index']);
    });
});
