<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\FeedResource;
use App\Models\Feed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoticiasController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $congregacao = app('congregacao');
        $perPage = min((int) $request->query('per_page', 15), 100);

        $query = Feed::query()
            ->where('categoria', 'noticia')
            ->where(function ($builder) use ($congregacao) {
                $builder->whereNull('congregacao_id')
                        ->orWhere('congregacao_id', $congregacao->id);
            })
            ->orderByDesc('publicado_em')
            ->orderByDesc('created_at');

        $news = $query->paginate($perPage);

        return $this->respondOk(
            FeedResource::collection($news->items())->resolve(),
            meta: [
                'pagination' => [
                    'current_page' => $news->currentPage(),
                    'per_page' => $news->perPage(),
                    'total' => $news->total(),
                    'last_page' => $news->lastPage(),
                ],
            ]
        );
    }

    public function show(int $id): JsonResponse
    {
        $congregacao = app('congregacao');

        $feed = Feed::query()
            ->where('categoria', 'noticia')
            ->where(function ($builder) use ($congregacao) {
                $builder->whereNull('congregacao_id')
                        ->orWhere('congregacao_id', $congregacao->id);
            })
            ->find($id);

        if (! $feed) {
            return $this->respondError('Notícia não encontrada.', 404);
        }

        return $this->respondOk((new FeedResource($feed))->resolve());
    }
}
