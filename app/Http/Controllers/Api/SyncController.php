<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AvisoResource;
use App\Http\Resources\FeedResource;
use App\Http\Resources\LivroResource;
use App\Models\Aviso;
use App\Models\Feed;
use App\Models\Livro;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SyncController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $congregacao = app('congregacao');
        $lastSyncRaw = $request->query('lastSync');

        try {
            $lastSync = $lastSyncRaw ? Carbon::parse($lastSyncRaw) : Carbon::createFromTimestamp(0);
        } catch (\Throwable $exception) {
            return $this->respondError('Formato de lastSync inválido.', 422);
        }

        $noticias = Feed::query()
            ->where('categoria', 'noticia')
            ->where(function ($builder) use ($congregacao) {
                $builder->whereNull('congregacao_id')
                        ->orWhere('congregacao_id', $congregacao->id);
            })
            ->where('updated_at', '>=', $lastSync)
            ->orderByDesc('updated_at')
            ->get();

        $podcasts = Feed::query()
            ->where('categoria', 'podcast')
            ->where(function ($builder) use ($congregacao) {
                $builder->whereNull('congregacao_id')
                        ->orWhere('congregacao_id', $congregacao->id);
            })
            ->where('updated_at', '>=', $lastSync)
            ->orderByDesc('updated_at')
            ->get();

        $avisos = Aviso::query()
            ->where('congregacao_id', $congregacao->id)
            ->where('updated_at', '>=', $lastSync)
            ->orderByDesc('updated_at')
            ->get();

        $livros = Livro::query()
            ->where(function ($builder) use ($congregacao) {
                $builder->whereNull('congregacao_id')
                        ->orWhere('congregacao_id', $congregacao->id);
            })
            ->where('updated_at', '>=', $lastSync)
            ->orderByDesc('updated_at')
            ->get();

        return $this->respondOk([
            'noticias' => FeedResource::collection($noticias)->resolve(),
            'podcasts' => FeedResource::collection($podcasts)->resolve(),
            'mensagens' => AvisoResource::collection($avisos)->resolve(),
            'livros' => LivroResource::collection($livros)->resolve(),
        ], meta: [
            'last_sync' => $lastSync->toIso8601String(),
        ]);
    }
}
