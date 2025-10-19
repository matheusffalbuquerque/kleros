<?php

namespace App\Http\Middleware;

use App\Models\Congregacao;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IdentifyCongregacao
{
    public function handle(Request $request, Closure $next)
    {
        $codigo = $request->header('X-Congregacao-Codigo');

        if (! $codigo) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Cabeçalho X-Congregacao-Codigo não informado.',
            ], 400);
        }

        $congregacao = Congregacao::query()->find($codigo);

        if (! $congregacao) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Congregação não encontrada.',
            ], 404);
        }

        app()->instance('congregacao', $congregacao);

        return $next($request);
    }
}
