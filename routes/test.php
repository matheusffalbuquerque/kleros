<?php

use Illuminate\Support\Facades\Route;
use App\Models\Dominio;

// Rota de teste para debug de domínios
Route::get('/test-domain', function () {
    $host = request()->getHost();
    $publicDomain = config('domains.public', 'kleros.local');
    $adminDomain = config('domains.admin', 'admin.local');
    
    $dominio = Dominio::with('congregacao.config')
        ->where('dominio', $host)
        ->where('ativo', true)
        ->first();
    
    return response()->json([
        'request_host' => $host,
        'public_domain' => $publicDomain,
        'admin_domain' => $adminDomain,
        'is_public' => in_array($host, [$publicDomain, $adminDomain], true),
        'dominio_found' => $dominio ? true : false,
        'dominio_data' => $dominio ? [
            'id' => $dominio->id,
            'dominio' => $dominio->dominio,
            'ativo' => $dominio->ativo,
            'congregacao_id' => $dominio->congregacao_id,
            'congregacao' => $dominio->congregacao ? $dominio->congregacao->identificacao : null,
        ] : null,
        'all_domains_in_db' => Dominio::select('id', 'dominio', 'ativo', 'congregacao_id')->get(),
    ]);
})->middleware('web');
