<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PwaController extends Controller
{
    public function manifest(Request $request)
    {
        $congregacao = app('congregacao');
        $config = $congregacao->config ?? null;

        $primaryColor = data_get($config, 'conjunto_cores.primaria', '#677b96');
        $backgroundColor = data_get($config, 'tema.propriedades.cor-fundo', '#0a1929');
        $shortName = $congregacao->nome_curto ?? $congregacao->nome ?? config('app.name');
        $name = sprintf('%s | %s', $shortName, config('app.name'));
        $logoPath = data_get($config, 'logo_caminho');
        $iconUrl = $logoPath ? url('/storage/' . ltrim($logoPath, '/')) : url('/favicon.ico');

        $manifest = [
            'id' => '/',
            'name' => $name,
            'short_name' => Str::limit($shortName, 30, ''),
            'start_url' => '/',
            'scope' => '/',
            'display' => 'standalone',
            'theme_color' => $primaryColor,
            'background_color' => $backgroundColor,
            'icons' => [
                ['src' => $iconUrl, 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'],
                ['src' => $iconUrl, 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ],
            'shortcuts' => [
                [
                    'name' => 'Abrir painel',
                    'short_name' => 'Painel',
                    'url' => '/',
                ],
            ],
        ];

        return response()->json($manifest)
            ->header('Content-Type', 'application/manifest+json');
    }
}
