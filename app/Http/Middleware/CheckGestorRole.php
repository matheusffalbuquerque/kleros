<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckGestorRole
{
    /**
     * Handle an incoming request.
     * Permite acesso a usuários com roles: gestor, kleros ou admin
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado para acessar esta página.');
        }

        // Verifica se o usuário tem uma das roles permitidas
        if ($user->hasAnyRole(['gestor', 'kleros', 'admin', 'principal', 'lider'])) {
            return $next($request);
        }

        return redirect()->route('index')->with('error', 'Você não tem permissão para acessar esta página.');
    }
}