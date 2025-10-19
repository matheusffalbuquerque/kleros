<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NotificacaoTesteController extends ApiController
{
    public function enviar(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'expo_token' => ['nullable', 'string'],
            'titulo' => ['nullable', 'string', 'max:120'],
            'mensagem' => ['nullable', 'string', 'max:400'],
        ]);

        $token = $payload['expo_token'] ?? 'ExpoPushToken[xxxxxxxxxxxxxxxxxxxxxx]';

        $body = [
            'to' => $token,
            'title' => $payload['titulo'] ?? 'Kleros • Notificação teste',
            'body' => $payload['mensagem'] ?? 'Esta é uma notificação enviada para testes do aplicativo móvel.',
            'data' => [
                'identifier' => Str::uuid()->toString(),
            ],
        ];

        try {
            $response = Http::timeout(10)->post('https://exp.host/--/api/v2/push/send', $body);
            $responseJson = $response->json();
        } catch (\Throwable $exception) {
            return $this->respondError('Não foi possível contatar o serviço de push: ' . $exception->getMessage(), 500);
        }

        return $this->respondOk([
            'solicitacao' => $body,
            'resposta' => $responseJson,
        ]);
    }
}
