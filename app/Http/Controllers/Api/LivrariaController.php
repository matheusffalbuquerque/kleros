<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\LivroResource;
use App\Models\Livro;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LivrariaController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $congregacao = app('congregacao');
        $busca = $request->query('q');

        $query = Livro::query()
            ->where(function ($builder) use ($congregacao) {
                $builder->whereNull('congregacao_id')
                    ->orWhere('congregacao_id', $congregacao->id);
            })
            ->orderBy('titulo');

        if ($busca) {
            $query->where(function ($builder) use ($busca) {
                $builder->where('titulo', 'like', "%{$busca}%")
                    ->orWhere('autor', 'like', "%{$busca}%");
            });
        }

        $livros = $query->get();

        return $this->respondOk(LivroResource::collection($livros)->resolve());
    }

    public function show(int $id): JsonResponse
    {
        $congregacao = app('congregacao');

        $livro = Livro::query()
            ->where(function ($builder) use ($congregacao) {
                $builder->whereNull('congregacao_id')
                    ->orWhere('congregacao_id', $congregacao->id);
            })
            ->find($id);

        if (! $livro) {
            return $this->respondError('Livro não encontrado.', 404);
        }

        return $this->respondOk((new LivroResource($livro))->resolve());
    }

    public function comprar(Request $request, int $id): JsonResponse
    {
        $livro = Livro::find($id);

        if (! $livro) {
            return $this->respondError('Livro não encontrado.', 404);
        }

        $user = $request->user();

        // Registro futuro da compra:
        // $user->livros()->syncWithoutDetaching([$livro->id => ['adquirido_em' => now()]]);

        return $this->respondOk([
            'mensagem' => 'Compra simulada registrada.',
            'livro' => (new LivroResource($livro))->resolve(),
        ]);
    }

    public function meusLivros(Request $request): JsonResponse
    {
        $user = $request->user();

        // Implementação futura:
        // $livros = $user->livros()->get();
        $livros = collect();

        return $this->respondOk(LivroResource::collection($livros)->resolve());
    }
}
