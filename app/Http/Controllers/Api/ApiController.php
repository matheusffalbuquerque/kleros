<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

abstract class ApiController extends Controller
{
    protected function respondOk($data = [], array $meta = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'data' => $data,
            'meta' => array_merge(['timestamp' => Carbon::now()->toIso8601String()], $meta),
        ], $status);
    }

    protected function respondError(string $message, int $status = 400, array $meta = []): JsonResponse
    {
        return response()->json([
            'status' => 'erro',
            'mensagem' => $message,
            'meta' => array_merge(['timestamp' => Carbon::now()->toIso8601String()], $meta),
        ], $status);
    }
}
