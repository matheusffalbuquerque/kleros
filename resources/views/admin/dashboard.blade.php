@extends('layouts.site')

@section('title', 'Painel Administrativo — Kleros')

@section('content')
@php
    $chartData = $membersByDenomination->map(fn ($item) => [
        'label' => $item->nome,
        'value' => (int) $item->total_membros,
    ]);
@endphp
<div class="min-h-screen bg-[#1a1821] text-[#f4f3f6] font-[Segoe_UI,Roboto,system-ui,-apple-system,Arial,sans-serif]">
    <header class="sticky top-0 z-40 bg-[#1a1821]/95 border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('site.home') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/kleros-logo.svg') }}" alt="Kleros" class="h-8 w-auto">
                <div class="leading-tight">
                    <span class="font-semibold text-lg">Kleros</span>
                    <span class="block text-xs text-white/60">Administração</span>
                </div>
            </a>
            <div class="flex items-center gap-3 text-sm">
                <span class="hidden sm:inline text-white/60">{{ now()->format('d \d\e F \d\e Y') }}</span>
                <a href="{{ route('logout') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-white/20 hover:border-rose-300/60 text-rose-200/80">
                    <span class="text-xs uppercase tracking-[0.2em]">Sair</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-12 space-y-12">
        <section class="grid gap-10 md:grid-cols-[1.1fr,0.9fr] items-start">
            <div>
                <h1 class="text-3xl md:text-4xl font-semibold">Painel Administrativo</h1>
                <p class="mt-3 text-white/70 text-base md:text-lg max-w-2xl">Acompanhe o crescimento das denominações, monitore congregações conectadas ao ecossistema e tenha visão rápida sobre domínios e membros cadastrados.</p>
                <div class="mt-8 grid gap-4 sm:grid-cols-2">
                    <article class="rounded-2xl border border-white/10 bg-white/5 px-5 py-4">
                        <p class="text-xs uppercase tracking-[0.3em] text-white/40">Denominações</p>
                        <p class="mt-2 text-3xl font-semibold">{{ number_format($stats['denominacoes'] ?? 0, 0, ',', '.') }}</p>
                        <p class="mt-1 text-sm text-white/60">com {{ $denominacoes->sum('congregacoes_count') }} congregações ativas</p>
                    </article>
                    <article class="rounded-2xl border border-white/10 bg-white/5 px-5 py-4">
                        <p class="text-xs uppercase tracking-[0.3em] text-white/40">Congregações</p>
                        <p class="mt-2 text-3xl font-semibold">{{ number_format($stats['congregacoes'] ?? 0, 0, ',', '.') }}</p>
                        <p class="mt-1 text-sm text-white/60">distribuídas pelas redes cadastradas</p>
                    </article>
                    <article class="rounded-2xl border border-white/10 bg-white/5 px-5 py-4">
                        <p class="text-xs uppercase tracking-[0.3em] text-white/40">Membros</p>
                        <p class="mt-2 text-3xl font-semibold">{{ number_format($stats['membros'] ?? 0, 0, ',', '.') }}</p>
                        <p class="mt-1 text-sm text-white/60">com denominação vinculada</p>
                    </article>
                    <article class="rounded-2xl border border-white/10 bg-white/5 px-5 py-4">
                        <p class="text-xs uppercase tracking-[0.3em] text-white/40">Domínios</p>
                        <p class="mt-2 text-3xl font-semibold">{{ number_format($stats['dominios'] ?? 0, 0, ',', '.') }}</p>
                        <p class="mt-1 text-sm text-white/60">endereços ativos no ecossistema</p>
                    </article>
                </div>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 space-y-4">
                <h2 class="text-lg font-semibold">Atalhos rápidos</h2>
                <ul class="space-y-2 text-sm text-white/70">
                    <li><a href="{{ route('denominacoes.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#6449a2]/20 border border-[#6449a2]/40 hover:bg-[#6449a2]/30">Gerenciar denominações</a></li>
                    <li>
                        @if ($denominacoes->isNotEmpty())
                            <a href="{{ route('denominacoes.configuracoes', ['denominacao' => $denominacoes->first()->id]) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white/10 border border-white/15 hover:border-white/30 hover:bg-white/15">Configurações da denominação</a>
                        @else
                            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white/40">Configurações da denominação</span>
                        @endif
                    </li>
                    <li><a href="{{ route('congregacoes.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white/10 border border-white/15 hover:border-white/30">Ver congregações</a></li>
                    <li><a href="{{ route('denominacoes.create') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white/10 border border-white/15 hover:border-white/30">Nova denominação</a></li>
                    <li><a href="{{ route('congregacoes.cadastro') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white/10 border border-white/15 hover:border-white/30">Check-in de congregação</a></li>
                    <li><a href="{{ route('admin.extensions.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white/10 border border-white/15 hover:border-white/30">Gestão de extensões</a></li>
                    @role('kleros')
                    <li class="pt-2 border-t border-white/10">
                        <a href="/guia-tecnico/" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-600/20 border border-emerald-600/40 hover:bg-emerald-600/30 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Guia Técnico
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                Dev
                            </span>
                        </a>
                    </li>
                    @endrole
                </ul>
            </div>
        </section>

        <section class="grid gap-8 lg:grid-cols-[1.2fr,0.8fr] items-start">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Membros por denominação</h2>
                        <p class="text-sm text-white/60">Volume total de membros vinculados a cada denominação cadastrada.</p>
                    </div>
                    <span class="text-xs uppercase tracking-[0.4em] text-white/40">Atualizado {{ now()->format('d/m/Y') }}</span>
                </header>
                <div id="denominationsChart" data-chart='@json($chartData)' class="mt-6 space-y-3"></div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 space-y-4">
                <h2 class="text-lg font-semibold">Top denominações</h2>
                <p class="text-sm text-white/60">Ranking considerando congregações ativas e número de membros.</p>
                <ol class="space-y-3 text-sm">
                    @foreach ($membersByDenomination->take(5) as $index => $item)
                        <li class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#6449a2]/30 text-[#cbb6ff] font-semibold">{{ $index + 1 }}</span>
                                <div>
                                    <p class="font-medium text-white">{{ $item->nome }}</p>
                                    <p class="text-xs text-white/50">{{ number_format($item->total_membros, 0, ',', '.') }} membros</p>
                                </div>
                            </div>
                            @php
                                $relatedDenomination = $denominacoes->firstWhere('id', $item->id);
                                $congregacoesCount = $relatedDenomination?->congregacoes_count ?? 0;
                            @endphp
                            <span class="text-xs text-white/50">{{ $congregacoesCount }} congregações</span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        <section class="rounded-3xl border border-white/10 bg-white/5 p-6">
            <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Congregações cadastradas</h2>
                    <p class="text-sm text-white/60">Visualize as congregações adicionadas recentemente e filtre por nome ou denominação.</p>
                </div>
                <div class="relative w-full md:w-72">
                    <input type="search" id="congregationFilter" placeholder="Filtrar congregações" class="w-full rounded-xl border border-white/15 bg-white/10 px-4 py-2 pl-10 text-sm text-white placeholder-white/40 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/30">
                    <span class="absolute left-3 top-2.5 text-white/40">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m19 19-4-4m0-7a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                        </svg>
                    </span>
                </div>
            </header>

            <div class="mt-6 overflow-hidden rounded-2xl border border-white/10">
                <table class="min-w-full divide-y divide-white/10 text-sm">
                    <thead class="bg-white/5 text-left text-xs uppercase tracking-[0.3em] text-white/40">
                        <tr>
                            <th class="px-4 py-3">Congregação</th>
                            <th class="px-4 py-3">Denominação</th>
                            <th class="px-4 py-3">Localização</th>
                            <th class="px-4 py-3">Membros</th>
                            <th class="px-4 py-3">Criada em</th>
                        </tr>
                    </thead>
                    <tbody id="congregationsTable" class="divide-y divide-white/10">
                        @forelse ($congregacoes as $congregacao)
                            @php
                                $cidadeNome = $congregacao->cidade->nome ?? '—';
                                $estadoUf = $congregacao->estado->uf ?? null;
                            @endphp
                            <tr class="transition hover:bg-white/5" data-search-text="{{ mb_strtolower(trim(($congregacao->identificacao ?? '') . ' ' . ($congregacao->denominacao->nome ?? '') . ' ' . $cidadeNome)) }}">
                                <td class="px-4 py-3 font-medium text-white">{{ $congregacao->identificacao ?? '—' }}</td>
                                <td class="px-4 py-3 text-white/70">{{ $congregacao->denominacao->nome ?? '—' }}</td>
                                <td class="px-4 py-3 text-white/70">{{ $cidadeNome }}{{ $estadoUf ? ', ' . $estadoUf : '' }}</td>
                                <td class="px-4 py-3 text-white/70">{{ number_format($congregacao->membros_count ?? 0, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-white/50">{{ optional($congregacao->created_at)->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-white/50">Nenhuma congregação cadastrada até o momento.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer class="border-t border-white/10 py-8 text-center text-xs text-white/40">
        Painel Administrativo Kleros — {{ now()->format('Y') }}
    </footer>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const chartContainer = document.getElementById('denominationsChart');
        if (chartContainer) {
            const data = JSON.parse(chartContainer.dataset.chart || '[]');
            const maxValue = data.reduce((max, item) => Math.max(max, item.value), 0) || 1;
            const formatter = new Intl.NumberFormat('pt-BR');

            chartContainer.innerHTML = data.length ? data.map((item) => {
                const percentage = Math.round((item.value / maxValue) * 100);
                const barWidth = Math.max(8, percentage);
                return `
                    <article class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-white">${item.label}</span>
                            <span class="text-white/50">${formatter.format(item.value)} membros</span>
                        </div>
                        <div class="mt-3 h-2 w-full rounded-full bg-white/10">
                            <div class="h-2 rounded-full bg-gradient-to-r from-[#cbb6ff] via-[#8d6add] to-[#6449a2]" style="width: ${barWidth}%;"></div>
                        </div>
                    </article>
                `;
            }).join('') : '<p class="text-sm text-white/60">Sem dados suficientes para exibir o gráfico.</p>';
        }

        const filterInput = document.getElementById('congregationFilter');
        const rows = document.querySelectorAll('#congregationsTable tr[data-search-text]');

        if (filterInput && rows.length) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = '<td colspan="5" class="px-4 py-6 text-center text-sm text-white/50">Nenhuma congregação encontrada para o filtro aplicado.</td>';
            const showEmptyState = (show) => {
                if (show && !emptyRow.isConnected) {
                    document.getElementById('congregationsTable').appendChild(emptyRow);
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

                showEmptyState(visibleCount === 0);
            };

            filterInput.addEventListener('input', () => applyFilter(filterInput.value));
        }
    });
</script>
@endpush
