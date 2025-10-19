@extends('layouts.site')

@section('title', 'Painel de Controle — Administração Denominacional')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
@endphp

@php
    $user = Auth::user();
    $allowed = false;
    if ($user) {
        if (method_exists($user, 'hasRole')) {
            try {
                $allowed = $user->hasRole('admin');
            } catch (\Throwable $e) {
                $allowed = false;
            }
        } else {
            // fallback para projetos que usam um campo `role` direto no usuário
            $allowed = in_array($user->role ?? null, ['admin'], true);
        }
    }
@endphp

@if(! $allowed)
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-lg mx-auto p-8 bg-white/5 border border-white/10 rounded-2xl text-center">
            <h1 class="text-2xl font-semibold text-white">Acesso negado</h1>
            <p class="mt-4 text-white/60">Esta área é acessível apenas para usuários com permissão de administrador.</p>
            <div class="mt-6">
                <a href="{{ route('index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-white/20 hover:border-rose-300/60 text-rose-200/80">Voltar ao painel</a>
            </div>
        </div>
    </div>
@else

@php
    // Preparar dados da denominação
    $stats = $stats ?? [];
    $denominacoes = $denominacoes ?? collect();
    $congregacoes = $congregacoes ?? collect();
    $dominios = $dominios ?? collect();
    $membersByDenomination = $membersByDenomination ?? collect();
    
    // Denominação principal (assumindo que é a primeira, já que o controller filtra)
    $denominacao = $denominacoes->first();
    $denominacaoNome = $denominacao?->nome ?? 'Denominação';
    
    // Dados para gráfico de congregações
    $congregacaoChartData = $congregacoes->map(fn ($cong) => [
        'label' => $cong->identificacao ?? 'Sem nome',
        'value' => (int) ($cong->membros_count ?? 0),
        'dominio' => $dominios->where('congregacao_id', $cong->id)->first()?->dominio ?? 'N/A'
    ])->sortByDesc('value')->values();
@endphp

<div class="min-h-screen bg-[#0f1724] text-[#f4f3f6] font-[Segoe_UI,Roboto,system-ui,-apple-system,Arial,sans-serif]">
    <header class="sticky top-0 z-40 bg-[#0f1724]/95 border-b border-white/8">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/kleros-logo.svg') }}" alt="Kleros" class="h-8 w-auto">
                <div class="leading-tight">
                    <span class="font-semibold text-lg">{{ $denominacaoNome }}</span>
                    <span class="block text-xs text-white/60">Painel de Controle</span>
                </div>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <span class="hidden sm:inline text-white/60">{{ now()->format('d \d\e F \d\e Y') }}</span>
                <a href="{{ route('logout') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-white/20 hover:border-rose-300/60 text-rose-200/80">
                    <span class="text-xs uppercase tracking-[0.2em]">Sair</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-12 space-y-12">
        <!-- Header da Denominação -->
        <section class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-[#cbb6ff] via-[#8d6add] to-[#6449a2] bg-clip-text text-transparent">
                {{ $denominacaoNome }}
            </h1>
            <p class="mt-4 text-white/70 text-lg max-w-3xl mx-auto">
                Painel de controle e gerenciamento das congregações, membros e atividades da denominação
            </p>
        </section>

        <!-- Estatísticas da Denominação -->
        <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <article class="rounded-2xl border border-white/10 bg-white/5 px-6 py-5 text-center">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#6449a2]/20 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#cbb6ff]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <p class="text-xs uppercase tracking-[0.3em] text-white/40 mb-2">Congregações</p>
                <p class="text-3xl font-bold text-white">{{ number_format($stats['congregacoes'] ?? 0, 0, ',', '.') }}</p>
                <p class="mt-1 text-sm text-white/60">unidades ativas</p>
            </article>

            <article class="rounded-2xl border border-white/10 bg-white/5 px-6 py-5 text-center">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#6449a2]/20 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#cbb6ff]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <p class="text-xs uppercase tracking-[0.3em] text-white/40 mb-2">Membros</p>
                <p class="text-3xl font-bold text-white">{{ number_format($stats['membros'] ?? 0, 0, ',', '.') }}</p>
                <p class="mt-1 text-sm text-white/60">pessoas cadastradas</p>
            </article>

            <article class="rounded-2xl border border-white/10 bg-white/5 px-6 py-5 text-center">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#6449a2]/20 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#cbb6ff]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                </div>
                <p class="text-xs uppercase tracking-[0.3em] text-white/40 mb-2">Usuários</p>
                <p class="text-3xl font-bold text-white">{{ number_format($stats['usuarios'] ?? 0, 0, ',', '.') }}</p>
                <p class="mt-1 text-sm text-white/60">contas ativas</p>
            </article>

            <article class="rounded-2xl border border-white/10 bg-white/5 px-6 py-5 text-center">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#6449a2]/20 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#cbb6ff]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9" />
                    </svg>
                </div>
                <p class="text-xs uppercase tracking-[0.3em] text-white/40 mb-2">Domínios</p>
                <p class="text-3xl font-bold text-white">{{ number_format($stats['dominios'] ?? 0, 0, ',', '.') }}</p>
                <p class="mt-1 text-sm text-white/60">sites ativos</p>
            </article>
        </section>

        <!-- Gráfico de Congregações e Ações Rápidas -->
        <section class="grid gap-8 lg:grid-cols-[1.4fr,1fr] items-start">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Membros por Congregação</h2>
                        <p class="text-sm text-white/60">Distribuição de membros nas congregações da denominação</p>
                    </div>
                    <span class="text-xs uppercase tracking-[0.4em] text-white/40">{{ now()->format('d/m/Y') }}</span>
                </header>
                <div id="congregationsChart" data-chart='@json($congregacaoChartData)' class="space-y-4"></div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 space-y-6">
                <h2 class="text-xl font-semibold text-white">Ações Rápidas</h2>
                
                <div class="space-y-3">
                    <a href="{{ route('congregacoes.index') }}" class="flex items-center gap-3 p-4 rounded-xl bg-[#6449a2]/20 border border-[#6449a2]/40 hover:bg-[#6449a2]/30 transition-colors">
                        <div class="flex-shrink-0 w-10 h-10 bg-[#6449a2]/30 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#cbb6ff]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-white">Ver Todas as Congregações</p>
                            <p class="text-xs text-white/60">Gerenciar congregações da denominação</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.reports.congregations') }}" target="_blank" class="flex items-center gap-3 p-4 rounded-xl bg-white/10 border border-white/15 hover:border-white/30 hover:bg-white/15 transition-colors">
                        <div class="flex-shrink-0 w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-white">Relatórios</p>
                            <p class="text-xs text-white/60">Visualizar estatísticas e dados</p>
                        </div>
                    </a>

                    @if ($denominacoes->isNotEmpty())
                        <a href="{{ route('denominacoes.configuracoes', ['denominacao' => $denominacoes->first()->id]) }}" class="flex items-center gap-3 p-4 rounded-xl bg-white/10 border border-white/15 hover:border-white/30 hover:bg-white/15 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-white">Configurações</p>
                                <p class="text-xs text-white/60">Ajustes da denominação</p>
                            </div>
                        </a>
                    @else
                        <span class="flex items-center gap-3 p-4 rounded-xl bg-white/5 border border-white/10 text-white/40">
                            <div class="flex-shrink-0 w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span class="flex-1">
                                <span class="font-medium">Configurações</span>
                                <span class="block text-xs">Ajustes da denominação</span>
                            </span>
                        </span>
                    @endif
                </div>
            </div>
        </section>

        <!-- Lista Detalhada de Congregações -->
        <section class="rounded-3xl border border-white/10 bg-white/5 p-6">
            <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-white">Congregações da Denominação</h2>
                    <p class="text-sm text-white/60">Lista completa das congregações vinculadas a {{ $denominacaoNome }}</p>
                </div>
                <div class="relative w-full md:w-72">
                    <input type="search" id="congregationFilterManage" placeholder="Filtrar congregações" class="w-full rounded-xl border border-white/15 bg-white/10 px-4 py-2 pl-10 text-sm text-white placeholder-white/40 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/30">
                    <span class="absolute left-3 top-2.5 text-white/40">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m19 19-4-4m0-7a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                        </svg>
                    </span>
                </div>
            </header>

            <div class="overflow-hidden rounded-2xl border border-white/10">
                <table class="min-w-full divide-y divide-white/10 text-sm">
                    <thead class="bg-white/5 text-left text-xs uppercase tracking-[0.3em] text-white/40">
                        <tr>
                            <th class="px-4 py-3">Congregação</th>
                            <th class="px-4 py-3">Localização</th>
                            <th class="px-4 py-3">Domínio</th>
                            <th class="px-4 py-3">Membros</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="congregationsTableManage" class="divide-y divide-white/10">
                        @forelse ($congregacoes as $congregacaoItem)
                            @php
                                $cidadeNome = $congregacaoItem->cidade->nome ?? '—';
                                $estadoUf = $congregacaoItem->estado->uf ?? null;
                                $dominio = $dominios->where('congregacao_id', $congregacaoItem->id)->first();
                                $membrosCount = $congregacaoItem->membros_count ?? 0;
                            @endphp
                            <tr class="transition hover:bg-white/5" data-search-text="{{ mb_strtolower(trim(($congregacaoItem->identificacao ?? '') . ' ' . $cidadeNome . ' ' . ($dominio?->dominio ?? ''))) }}">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-[#6449a2]/20 rounded-lg flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#cbb6ff]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-white">{{ $congregacaoItem->identificacao ?? '—' }}</p>
                                            @if($congregacaoItem->nome_reduzido)
                                                <p class="text-xs text-white/50">{{ $congregacaoItem->nome_reduzido }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-white/70">
                                    <div class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>{{ $cidadeNome }}{{ $estadoUf ? ', ' . $estadoUf : '' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    @if($dominio)
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-[#6449a2]/20 text-[#cbb6ff] border border-[#6449a2]/30">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9" />
                                                </svg>
                                                {{ $dominio->dominio }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-white/40 text-xs">Sem domínio</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-white/70">
                                    <div class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span class="font-semibold">{{ number_format($membrosCount, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs {{ $congregacaoItem->ativa ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-red-500/20 text-red-300 border border-red-500/30' }}">
                                        <div class="w-2 h-2 rounded-full {{ $congregacaoItem->ativa ? 'bg-emerald-400' : 'bg-red-400' }}"></div>
                                        {{ $congregacaoItem->ativa ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($dominio && $dominio->ativo)
                                            <a href="http://{{ $dominio->dominio }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs bg-white/10 hover:bg-white/20 text-white/70 hover:text-white transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                                Acessar
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.reports.congregation', $congregacaoItem->id) }}" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs bg-white/10 hover:bg-white/20 text-white/70 hover:text-white transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                            Relatório
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-sm font-medium text-white/60">Nenhuma congregação encontrada</p>
                                            <p class="text-xs text-white/40 mt-1">Esta denominação ainda não possui congregações cadastradas.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer class="border-t border-white/10 py-8 text-center text-xs text-white/40">
        Administração Kleros — {{ now()->format('Y') }}
    </footer>
</div>

@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Gráfico de congregações
        const chartContainer = document.getElementById('congregationsChart');
        if (chartContainer) {
            const data = JSON.parse(chartContainer.dataset.chart || '[]');
            const maxValue = data.reduce((max, item) => Math.max(max, item.value), 0) || 1;
            const formatter = new Intl.NumberFormat('pt-BR');

            chartContainer.innerHTML = data.length ? data.map((item, index) => {
                const percentage = Math.round((item.value / maxValue) * 100);
                const barWidth = Math.max(12, percentage);
                const colors = [
                    'from-[#cbb6ff] via-[#8d6add] to-[#6449a2]',
                    'from-[#a78bfa] via-[#7c3aed] to-[#5b21b6]',
                    'from-[#c4b5fd] via-[#8b5cf6] to-[#7c2d12]',
                    'from-[#ddd6fe] via-[#a855f7] to-[#86198f]'
                ];
                const colorClass = colors[index % colors.length];
                
                return `
                    <article class="rounded-2xl border border-white/10 bg-white/5 p-4 hover:bg-white/8 transition-colors">
                        <div class="flex items-center justify-between text-sm mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-gradient-to-r ${colorClass}"></div>
                                <span class="font-medium text-white">${item.label}</span>
                            </div>
                            <span class="text-white/50">${formatter.format(item.value)} membros</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-white/10 mb-2">
                            <div class="h-2 rounded-full bg-gradient-to-r ${colorClass}" style="width: ${barWidth}%;"></div>
                        </div>
                        <div class="flex items-center justify-between text-xs text-white/40">
                            <span>Domínio: ${item.dominio}</span>
                            <span>${percentage}%</span>
                        </div>
                    </article>
                `;
            }).join('') : `
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <p class="text-sm text-white/60">Sem dados suficientes para exibir o gráfico.</p>
                    <p class="text-xs text-white/40 mt-1">Adicione congregações com membros para visualizar as estatísticas.</p>
                </div>
            `;
        }

        // Filtro de congregações
        const filterInput = document.getElementById('congregationFilterManage');
        const rows = document.querySelectorAll('#congregationsTableManage tr[data-search-text]');

        if (filterInput && rows.length) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `
                <td colspan="6" class="px-4 py-8 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-medium text-white/60">Nenhuma congregação encontrada</p>
                            <p class="text-xs text-white/40 mt-1">Tente ajustar os termos de busca.</p>
                        </div>
                    </div>
                </td>
            `;

            const showEmptyState = (show) => {
                if (show && !emptyRow.isConnected) {
                    document.getElementById('congregationsTableManage').appendChild(emptyRow);
                } else if (!show && emptyRow.isConnected) {
                    emptyRow.remove();
                }
            };

            const applyFilter = (term) => {
                const normalized = term.trim().toLowerCase();
                let visibleCount = 0;

                rows.forEach((row) => {
                    const matches = !normalized || row.dataset.searchText.includes(normalized);
                    row.style.display = matches ? '' : 'none';
                    if (matches) {
                        visibleCount += 1;
                    }
                });

                showEmptyState(visibleCount === 0 && normalized.length > 0);
            };

            filterInput.addEventListener('input', () => applyFilter(filterInput.value));
        }

    });
</script>
@endpush
