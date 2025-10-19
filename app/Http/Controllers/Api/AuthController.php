<?php

namespace App\Http\Controllers\Api;

use App\Models\Membro;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends ApiController
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string'],
        ]);

        $congregacao = app('congregacao');

        /** @var User|null $user */
        $user = User::query()
            ->where('email', $credentials['email'])
            ->where(function ($query) use ($congregacao) {
                $query->where('congregacao_id', $congregacao->id)
                      ->orWhereNull('congregacao_id');
            })
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return $this->respondError('Credenciais inválidas.', 401);
        }

        if ($user->congregacao_id !== $congregacao->id) {
            return $this->respondError('Usuário não pertence à congregação informada.', 403);
        }

        $tokenName = $credentials['device_name'] ?? ('mobile-' . Str::random(8));
        $token = $user->createToken($tokenName)->plainTextToken;

        return $this->respondOk([
            'token' => $token,
            'usuario' => [
                'id' => $user->id,
                'nome' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return $this->respondOk(['mensagem' => 'Logout realizado com sucesso.']);
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'telefone' => ['required', 'string', 'max:50'],
        ]);

        $congregacao = app('congregacao');

        $membro = new Membro();
        $membro->nome = $data['name'];
        $membro->telefone = $data['telefone'];
        $membro->email = $data['email'];
        $membro->ativo = true;
        $membro->save();

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->congregacao_id = $congregacao->id;
        $user->denominacao_id = $congregacao->denominacao_id;
        $user->membro_id = $membro->id;
        $user->save();

        if ($user->roles()->count() === 0 && method_exists($user, 'assignRole')) {
            $user->assignRole('membro');
        }

        $token = $user->createToken('mobile-register')->plainTextToken;

        return $this->respondOk([
            'token' => $token,
            'usuario' => [
                'id' => $user->id,
                'nome' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ],
        ], status: 201);
    }
}
