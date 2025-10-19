<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\FeedResource;
use App\Models\Feed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PodcastsController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $congregacao = app('congregacao');
        $perPage = min((int) $request->query('per_page', 15), 100);

        $query = Feed::query()
            ->where('categoria', 'podcast')
            ->where(function ($builder) use ($congregacao) {
                $builder->whereNull('congregacao_id')
                        ->orWhere('congregacao_id', $congregacao->id);
            })
            ->orderByDesc('publicado_em')
            ->orderByDesc('created_at');

        $podcasts = $query->paginate($perPage);

        return $this->respondOk(
            FeedResource::collection($podcasts->items())->resolve(),
            meta: [
                'pagination' => [
                    'current_page' => $podcasts->currentPage(),
                    'per_page' => $podcasts->perPage(),
                    'total' => $podcasts->total(),
                    'last_page' => $podcasts->lastPage(),
                ],
            ]
        );
    }

    public function show(int $id): JsonResponse
    {
        $congregacao = app('congregacao');

        $feed = Feed::query()
            ->where('categoria', 'podcast')
            ->where(function ($builder) use ($congregacao) {
                $builder->whereNull('congregacao_id')
                        ->orWhere('congregacao_id', $congregacao->id);
            })
            ->find($id);

        if (! $feed) {
            return $this->respondError('Podcast não encontrado.', 404);
        }

        return $this->respondOk((new FeedResource($feed))->resolve());
    }
}
