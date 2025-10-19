<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CongregacaoConfigResource;
use App\Http\Resources\ExtensaoResource;
use App\Models\Congregacao;
use App\Models\Extensao;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ConfiguracaoController extends ApiController
{
    public function show(string $codigo): JsonResponse
    {
        $congregacao = Congregacao::with(['config', 'denominacao', 'denominacao.ministerios'])->find($codigo);

        if (! $congregacao) {
            return $this->respondError('Congregação não encontrada.', 404);
        }

        $headerCongregacao = app('congregacao');
        if ($headerCongregacao && $headerCongregacao->id !== $congregacao->id) {
            return $this->respondError('Código de congregação divergente do cabeçalho.', 409);
        }

        $modulos = Extensao::query()
            ->where('congregacao_id', $congregacao->id)
            ->get();

        if ($modulos->isEmpty()) {
            $modulesPath = base_path('modules');
            $files = File::isDirectory($modulesPath) ? File::directories($modulesPath) : [];
            $modulos = collect($files)->map(function (string $path) {
                $moduleKey = Str::lower(basename($path));

                return new Extensao([
                    'module' => $moduleKey,
                    'enabled' => true,
                    'options' => [],
                ]);
            });
        }

        return $this->respondOk([
            'congregacao' => (new CongregacaoConfigResource($congregacao))->resolve(),
            'modulos' => ExtensaoResource::collection($modulos)->resolve(),
        ]);
    }
}
