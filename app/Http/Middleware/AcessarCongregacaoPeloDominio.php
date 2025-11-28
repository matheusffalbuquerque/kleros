<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Congregacao;
use App\Models\Dominio;
use Illuminate\Support\Facades\Auth;

class AcessarCongregacaoPeloDominio
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtém o host da requisição
        $host = $request->getHost();
        $publicDomain = config('domains.public', 'kleros.local');
        $adminDomain = config('domains.admin', 'admin.local');

        // Se for o domínio principal, não carregar congregação
        if (in_array($host, [$publicDomain, $adminDomain], true)) {
            app()->instance('modo_admin', $host === $adminDomain);
            app()->instance('site_publico', $host === $publicDomain);
            app()->instance('congregacao', null);
            Auth::shouldUse('web'); // garante sessão padrão
            return $next($request);
        }

        if ($host === '192.168.1.7') {
            $congregacao = Congregacao::with('config')->find(2);
            if ($congregacao) {
                $request->attributes->set('congregacao', $congregacao);
                app()->instance('congregacao', $congregacao);
                app()->instance('modo_admin', false);
                app()->instance('site_publico', false);
                return $next($request);
            }
        }

        // Verifica se o host é um domínio válido
        $dominio = Dominio::with('congregacao.config')->where('dominio', $host)
            ->where('ativo', true)
            ->first();

        if (!$dominio) {
            // 🔁 Redireciona para site principal se o domínio não existir
            $mainUrl = rtrim((string) config('app.url'), '/');

            if ($mainUrl === '') {
                $scheme = config('domains.scheme', $request->isSecure() ? 'https' : 'http');
                $mainUrl = "{$scheme}://{$publicDomain}";
            }

            return redirect()->away($mainUrl);
        }

        app()->instance('congregacao', $dominio->congregacao);
        app()->instance('modo_admin', false);
        app()->instance('site_publico', false);

        return $next($request);
    }
}
