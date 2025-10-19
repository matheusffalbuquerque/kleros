@extends('layouts.site')

@section('title', 'Configurações da Denominação — Administração')

@section('content')
@php
    $selectedDenominacaoId = $denominacao->id ?? null;
    $denominacaoAtiva = old('ativa') !== null ? (bool) old('ativa') : (bool) ($denominacao->ativa ?? false);

    $ministeriosFormData = collect(old('ministerios', []))->map(function ($data, $key) {
        return [
            'key' => $key,
            'id' => $data['id'] ?? null,
            'titulo' => $data['titulo'] ?? null,
            'sigla' => $data['sigla'] ?? null,
            'descricao' => $data['descricao'] ?? null,
        ];
    });

    if ($ministeriosFormData->isEmpty() && $denominacao) {
        $ministeriosFormData = $denominacao->ministerios->map(function ($ministerio) {
            return [
                'key' => 'existing_' . $ministerio->id,
                'id' => $ministerio->id,
                'titulo' => $ministerio->titulo,
                'sigla' => $ministerio->sigla,
                'descricao' => $ministerio->descricao,
            ];
        });
    }

    $ministeriosFormData = $ministeriosFormData->values();
@endphp

<div class="min-h-screen bg-[#0f1724] text-[#f4f3f6] font-[Segoe_UI,Roboto,system-ui,-apple-system,Arial,sans-serif]">
    <header class="sticky top-0 z-40 bg-[#0f1724]/95 border-b border-white/10">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between gap-3">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/kleros-logo.svg') }}" alt="Kleros" class="h-8 w-auto">
                <div class="leading-tight">
                    <span class="font-semibold text-lg">Kleros</span>
                    <span class="block text-xs text-white/60">Administração • Configurações da denominação</span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.manage') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-white/20 hover:border-white/40 text-sm text-white/80">
                    <span class="text-xs uppercase tracking-[0.2em]">Voltar ao painel</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-12 space-y-8">
        <section class="rounded-3xl border border-white/10 bg-white/5 p-6 space-y-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-semibold">Configurações da denominação</h1>
                    <p class="mt-2 text-sm text-white/60 max-w-2xl">
                        Atualize dados institucionais e mantenha a lista de ministérios que serão sugeridos nas congregações vinculadas.
                    </p>
                </div>
                @if ($denominacoes->count() > 1)
                    <div class="w-full md:w-80">
                        <label class="block text-sm font-medium text-white/70 mb-2">Selecionar denominação</label>
                        <select id="denominacaoSelector" class="w-full rounded-xl border border-white/15 bg-white/10 px-4 py-2 text-sm text-white focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/35">
                            @foreach ($denominacoes as $item)
                                <option value="{{ route('denominacoes.configuracoes', ['denominacao' => $item->id]) }}" @selected($item->id === $selectedDenominacaoId)>
                                    {{ $item->nome }}{{ ! $item->ativa ? ' — Inativa' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            @if (session('success'))
                <div class="rounded-2xl border border-emerald-400/40 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-rose-400/40 bg-rose-400/10 px-4 py-3 text-sm text-rose-200">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('denominacoes.update', $denominacao->id) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 space-y-6">
                    <header class="flex flex-col gap-2">
                        <h2 class="text-lg font-semibold text-white">Dados institucionais</h2>
                        <p class="text-sm text-white/60">Definem a identidade da denominação em relatórios e comunicações.</p>
                    </header>

                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-medium text-white/80">Nome da denominação</span>
                            <input type="text" name="nome" value="{{ old('nome', $denominacao->nome) }}" required class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white placeholder-white/40 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/35">
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-white/80">Base doutrinária</span>
                            <select name="base_doutrinaria" class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/35">
                                <option value="">Selecione uma base</option>
                                @foreach ($basesDoutrinarias as $base)
                                    <option value="{{ $base->id }}" @selected(old('base_doutrinaria', $denominacao->base_doutrinaria) == $base->id)>
                                        {{ $base->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                        <input type="checkbox" name="ativa" id="denominacaoAtiva" value="1" class="h-4 w-4 rounded border-white/30 bg-white/10 text-[#6449a2] focus:ring-[#6449a2]/50" @checked($denominacaoAtiva)>
                        <label for="denominacaoAtiva" class="flex flex-col">
                            <span class="text-sm font-medium text-white">Denominação ativa</span>
                            <span class="text-xs text-white/60">Controla se novas congregações podem ser vinculadas a esta denominação.</span>
                        </label>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 space-y-6">
                    <header class="flex flex-col gap-2">
                        <h2 class="text-lg font-semibold text-white">Ministérios vinculados</h2>
                        <p class="text-sm text-white/60">Lista sugerida automaticamente ao criar ou atualizar congregações e membros.</p>
                    </header>

                    <div id="ministeriosContainer" data-next-index="{{ $ministeriosFormData->count() }}" class="space-y-4">
                        @forelse ($ministeriosFormData as $index => $item)
                            @php
                                $inputKey = $item['key'] ?? ('existing_' . ($item['id'] ?? $index));
                                $tituloId = 'ministerio_' . $loop->index . '_titulo';
                                $siglaId = 'ministerio_' . $loop->index . '_sigla';
                                $descricaoId = 'ministerio_' . $loop->index . '_descricao';
                            @endphp
                            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 space-y-4" data-ministerio-row>
                                @if (! empty($item['id']))
                                    <input type="hidden" name="ministerios[{{ $inputKey }}][id]" value="{{ $item['id'] }}">
                                @endif
                                <div class="grid gap-4 md:grid-cols-3">
                                    <label class="block md:col-span-2">
                                        <span class="text-sm font-medium text-white/80">Nome do ministério</span>
                                        <input type="text" id="{{ $tituloId }}" name="ministerios[{{ $inputKey }}][titulo]" value="{{ $item['titulo'] }}" placeholder="Ex.: Ministério de Louvor" class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white placeholder-white/40 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/35">
                                    </label>
                                    <label class="block">
                                        <span class="text-sm font-medium text-white/80">Sigla</span>
                                        <input type="text" id="{{ $siglaId }}" name="ministerios[{{ $inputKey }}][sigla]" value="{{ $item['sigla'] }}" placeholder="Opcional" class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white placeholder-white/40 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/35">
                                    </label>
                                </div>
                                <label class="block">
                                    <span class="text-sm font-medium text-white/80">Descrição</span>
                                    <textarea id="{{ $descricaoId }}" name="ministerios[{{ $inputKey }}][descricao]" rows="2" placeholder="Descreva responsabilidades ou foco deste ministério" class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-sm text-white placeholder-white/40 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/35">{{ $item['descricao'] }}</textarea>
                                </label>
                                <div class="flex justify-end">
                                    <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-rose-400/30 px-3 py-2 text-sm text-rose-200 hover:border-rose-300/60 hover:bg-rose-400/10 transition" data-remove-ministerio>
                                        Remover
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="rounded-2xl border border-dashed border-white/15 bg-white/5 px-4 py-5 text-sm text-white/60">
                                Nenhum ministério cadastrado ainda. Utilize o botão abaixo para adicionar o primeiro registro.
                            </p>
                        @endforelse
                    </div>

                    <div id="ministeriosRemoved"></div>

                    <button type="button" id="addMinisterio" class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-2 text-sm text-white hover:border-white/35 hover:bg-white/15 transition">
                        Adicionar ministério
                    </button>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-white/50">As alterações são aplicadas imediatamente em todas as congregações desta denominação.</p>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#6449a2] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-[#6449a2]/30 transition hover:bg-[#584091]">
                        Salvar alterações
                    </button>
                </div>
            </form>
        </section>
    </main>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selector = document.getElementById('denominacaoSelector');
        selector?.addEventListener('change', (event) => {
            const option = event.target.selectedOptions[0];
            if (option && option.value) {
                window.location.href = option.value;
            }
        });

        const container = document.getElementById('ministeriosContainer');
        const addButton = document.getElementById('addMinisterio');
        const removedContainer = document.getElementById('ministeriosRemoved');

        if (container && addButton) {
            let nextIndex = Number(container.dataset.nextIndex || 0);

            const createMinisterioRow = (indexKey) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'rounded-2xl border border-white/10 bg-white/10 p-5 space-y-4';
                wrapper.setAttribute('data-ministerio-row', '');

                wrapper.innerHTML = `
                    <div class="grid gap-4 md:grid-cols-3">
                        <label class="block md:col-span-2">
                            <span class="text-sm font-medium text-white/80">Nome do ministério</span>
                            <input type="text" name="ministerios[${indexKey}][titulo]" placeholder="Ex.: Ministério de Louvor" class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white placeholder-white/40 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/35">
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-white/80">Sigla</span>
                            <input type="text" name="ministerios[${indexKey}][sigla]" placeholder="Opcional" class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white placeholder-white/40 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/35">
                        </label>
                    </div>
                    <label class="block">
                        <span class="text-sm font-medium text-white/80">Descrição</span>
                        <textarea name="ministerios[${indexKey}][descricao]" rows="2" placeholder="Descreva responsabilidades ou foco deste ministério" class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-sm text-white placeholder-white/40 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/35"></textarea>
                    </label>
                    <div class="flex justify-end">
                        <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-rose-400/30 px-3 py-2 text-sm text-rose-200 hover:border-rose-300/60 hover:bg-rose-400/10 transition" data-remove-ministerio>
                            Remover
                        </button>
                    </div>
                `;

                container.appendChild(wrapper);
            };

            addButton.addEventListener('click', () => {
                const indexKey = `new_${Date.now()}_${nextIndex++}`;
                createMinisterioRow(indexKey);
            });

            container.addEventListener('click', (event) => {
                const trigger = event.target.closest('[data-remove-ministerio]');
                if (!trigger) {
                    return;
                }

                const row = trigger.closest('[data-ministerio-row]');
                if (!row) {
                    return;
                }

                const idInput = row.querySelector('input[name$="[id]"]');
                if (idInput && idInput.value) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'ministerios_removidos[]';
                    hidden.value = idInput.value;
                    removedContainer?.appendChild(hidden);
                }

                row.remove();
            });
        }
    });
</script>
@endpush
