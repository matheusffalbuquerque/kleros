<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AvisoResource;
use App\Models\Aviso;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MensagemController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $congregacao = app('congregacao');
        $user = $request->user();
        $membro = $user?->membro;

        if ($membro) {
            $avisos = $membro->avisosVisiveis();
        } else {
            $avisos = Aviso::query()
                ->where('congregacao_id', $congregacao->id)
                ->where('para_todos', true)
                ->orderByDesc('created_at')
                ->get();
        }

        return $this->respondOk(AvisoResource::collection($avisos)->resolve());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'mensagem' => ['required', 'string'],
        ]);

        $congregacao = app('congregacao');
        $user = $request->user();

        $aviso = Aviso::create([
            'congregacao_id' => $congregacao->id,
            'titulo' => $data['titulo'],
            'mensagem' => $data['mensagem'],
            'para_todos' => true,
            'data_inicio' => Carbon::now(),
            'status' => 'ativo',
            'prioridade' => 'normal',
            'criado_por' => $user?->membro_id,
        ]);

        return $this->respondOk((new AvisoResource($aviso))->resolve(), status: 201);
    }
}
