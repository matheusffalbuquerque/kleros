<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminRole
{
    
    public function handle(Request $request, Closure $next)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user && $user->hasRole('kleros')) {
            return $next($request);
        }

        return redirect()->route('login')->with('error', 'Você não tem permissão para acessar este ambiente.');
    }
}
