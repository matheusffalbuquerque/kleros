@extends('layouts.site')

@section('title', __('congregations.meta.register_title'))
@section('meta_description', __('site.meta.description'))

@section('content')
@php
    $texts = trans('congregations.cadastro');
    $basicFields = $texts['basic']['fields'];
    $managerSection = $texts['manager'] ?? null;
    $managerFields = $managerSection['fields'] ?? [];
    $locationFields = $texts['location']['fields'];
    $locationSelects = $texts['location']['selects'];
    $selectedPais = old('pais') ?? optional($paises->firstWhere('nome', 'Brasil'))->id;
    $selectedEstado = old('estado');
    $selectedCidade = old('cidade');
    $preselectedDenominacao = $denominacoes->firstWhere('id', old('igreja'));
@endphp
<div class="min-h-screen bg-[#1a1821] text-[#f4f3f6] font-[Segoe_UI,Roboto,system-ui,-apple-system,Arial,sans-serif]">
    <header class="sticky top-0 z-40 bg-[#1a1821]/95 border-b border-white/10">
        <div class="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between gap-3">
            <a href="{{ route('site.home') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/kleros-logo.svg') }}" alt="Kleros" class="h-8 w-auto">
                <div class="leading-tight">
                    <span class="font-semibold text-lg">Kleros</span>
                    <span class="block text-xs text-white/60">{{ __('congregations.header.tagline') }}</span>
                </div>
            </a>
            <div class="flex items-center gap-3">
                @include('site.partials.language-switcher', ['formClass' => 'hidden sm:block', 'selectId' => 'locale-congregations-register'])
                <a href="{{ route('denominacoes.create') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-white/20 hover:border-white/40 text-sm">
                    {{ __('congregations.header.link_denominations') }}
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-16">
        <div class="text-center md:text-left space-y-3">
            <span class="uppercase tracking-[0.2em] text-xs text-white/50">{{ $texts['badge'] }}</span>
            <h1 class="text-3xl md:text-4xl font-semibold">{{ $texts['title'] }}</h1>
            <p class="text-white/70 text-base md:text-lg">{{ $texts['description'] }}</p>
        </div>

        <div class="mt-8 space-y-4">
            @if (session('msg'))
                <div class="rounded-xl border border-emerald-400/40 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">
                    {{ session('msg') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-rose-400/40 bg-rose-400/10 px-4 py-3 text-sm text-rose-200">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <form action="{{ route('congregacoes.store') }}" method="POST" enctype="multipart/form-data" class="mt-10 bg-white/5 border border-white/10 rounded-3xl p-8 space-y-10">
            @csrf
            <input type="hidden" name="language" value="{{ app()->getLocale() }}">
            <div class="space-y-4">
                <div>
                    <h2 class="text-xl font-semibold">{{ $texts['denomination']['title'] }}</h2>
                    <p class="text-white/60 text-sm mt-2">
                        {!! __(
                            'congregations.cadastro.denomination.subtitle',
                            [
                                'link' => '<a href="' . route('denominacoes.create') . '" class="text-[#cbb6ff] hover:text-white">' . $texts['denomination']['link'] . '</a>',
                            ]
                        ) !!}
                    </p>
                </div>
                <div class="space-y-3">
                    <input type="hidden" name="igreja" id="igreja" value="{{ old('igreja') }}" required>
                    <button type="button" id="denominacao_open_modal" class="w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-left text-sm font-medium text-white/80 transition hover:border-white/40 hover:bg-white/15 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/40">
                        Selecionar denominação
                    </button>
                    <div id="denominacao_selected" class="rounded-xl border border-[#6449a2]/60 bg-[#6449a2]/20 px-4 py-3 text-sm text-white/90 flex items-center justify-between {{ $preselectedDenominacao ? '' : 'hidden' }}">
                        <div class="flex flex-col">
                            <span class="text-xs uppercase tracking-[0.2em] text-white/50">{{ $texts['denomination']['selected_label'] }}</span>
                            <span id="denominacao_selected_name" class="mt-1 font-medium">{{ optional($preselectedDenominacao)->nome }}</span>
                        </div>
                        <button type="button" id="denominacao_clear" class="rounded-lg border border-white/20 px-3 py-1 text-xs font-medium text-white/80 hover:border-white/40">
                            {{ $texts['denomination']['toggle'] }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold">{{ $texts['basic']['title'] }}</h2>
                    <p class="text-white/60 text-sm mt-2">{{ $texts['basic']['subtitle'] }}</p>
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                    @foreach ($basicFields as $key => $field)
                        <label class="{{ in_array($key, ['site']) ? 'block md:col-span-2' : 'block' }}">
                            <span class="text-sm font-medium text-white/80">{{ $field['label'] }}</span>
                            <input
                                @class(['mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white placeholder-white/40 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/40'])
                                type="{{ in_array($key, ['email']) ? 'email' : (in_array($key, ['site']) ? 'url' : 'text') }}"
                                id="{{ $key }}"
                                name="{{ $key }}"
                                value="{{ old($key) }}"
                                placeholder="{{ $field['placeholder'] }}"
                                @if(in_array($key, ['nome', 'nome_curto', 'cnpj', 'telefone', 'email'])) required @endif>
                        </label>
                    @endforeach
                </div>
            </div>
            @if($managerSection)
            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold">{{ $managerSection['title'] }}</h2>
                    <p class="text-white/60 text-sm mt-2">{{ $managerSection['subtitle'] }}</p>
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                    <label class="block md:col-span-2">
                        <span class="text-sm font-medium text-white/80">{{ $managerFields['nome']['label'] ?? '' }}</span>
                        <input
                            type="text"
                            id="gestor_nome"
                            name="gestor_nome"
                            value="{{ old('gestor_nome') }}"
                            placeholder="{{ $managerFields['nome']['placeholder'] ?? '' }}"
                            class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white placeholder-white/40 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/40"
                            required>
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-white/80">{{ $managerFields['telefone']['label'] ?? '' }}</span>
                        <input
                            type="text"
                            id="gestor_telefone"
                            name="gestor_telefone"
                            value="{{ old('gestor_telefone') }}"
                            placeholder="{{ $managerFields['telefone']['placeholder'] ?? '' }}"
                            class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white placeholder-white/40 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/40"
                            required>
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-white/80">{{ $managerFields['data_nascimento']['label'] ?? '' }}</span>
                        <input
                            type="date"
                            id="gestor_data_nascimento"
                            name="gestor_data_nascimento"
                            value="{{ old('gestor_data_nascimento') }}"
                            class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white placeholder-white/40 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/40"
                            required>
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-white/80">{{ $managerFields['cpf']['label'] ?? '' }}</span>
                        <input
                            type="text"
                            id="gestor_cpf"
                            name="gestor_cpf"
                            value="{{ old('gestor_cpf') }}"
                            placeholder="{{ $managerFields['cpf']['placeholder'] ?? '' }}"
                            class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white placeholder-white/40 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/40"
                            required>
                    </label>
                </div>
            </div>
            @endif

            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold">{{ $texts['location']['title'] }}</h2>
                    <p class="text-white/60 text-sm mt-2">{{ $texts['location']['subtitle'] }}</p>
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                    @foreach ($locationFields as $key => $field)
                        <label class="{{ $key === 'endereco' ? 'block md:col-span-2' : 'block' }}">
                            <span class="text-sm font-medium text-white/80">{{ $field['label'] }}</span>
                            <input type="text" id="{{ $key }}" name="{{ $key }}" value="{{ old($key) }}" placeholder="{{ $field['placeholder'] }}" class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white placeholder-white/40 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/40">
                        </label>
                    @endforeach
                    @foreach ($locationSelects as $key => $field)
                        <label class="block">
                            <span class="text-sm font-medium text-white/80">{{ $field['label'] }}</span>
                            <select
                                name="{{ $key }}"
                                id="{{ $key }}"
                                class="mt-2 w-full rounded-xl bg-white text-[#1a1821] border border-white/15 px-4 py-3 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/40"
                                data-selected="{{ ${'selected' . ucfirst($key)} ?? '' }}">
                                <option value="">{{ $field['placeholder'] }}</option>
                                @if ($key === 'pais')
                                    @foreach ($paises as $pais)
                                        <option value="{{ $pais->id }}" @selected($selectedPais == $pais->id)>{{ $pais->nome }}</option>
                                    @endforeach
                                @elseif ($key === 'estado')
                                    @foreach ($estados as $estado)
                                        <option value="{{ $estado->id }}" data-pais-id="{{ $estado->pais_id }}" data-uf="{{ $estado->uf }}" @selected($selectedEstado == $estado->id)>{{ $estado->nome }}</option>
                                    @endforeach
                                @else
                                    @foreach ($cidades as $cidade)
                                        <option value="{{ $cidade->id }}" data-estado-id="{{ $cidade->estado_id }}" data-estado-uf="{{ $cidade->uf ?? '' }}" @selected($selectedCidade == $cidade->id)>{{ $cidade->nome }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-white/50">{{ $texts['consent'] }}</p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('site.home') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-xl border border-white/15 text-sm font-medium text-white/80 hover:border-white/40">
                        {{ $texts['buttons']['back'] }}
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-[#6449a2] hover:bg-[#584091] text-sm font-semibold shadow-lg shadow-[#6449a2]/30 transition">
                        {{ $texts['buttons']['submit'] }}
                    </button>
                </div>
            </div>
        </form>
    </main>
</div>

<dialog id="denominacao_modal" class="w-[min(92vw,42rem)] rounded-3xl border border-white/10 bg-[#24212b] p-0 text-[#f4f3f6] shadow-2xl shadow-black/50 backdrop:bg-black/70 backdrop:backdrop-blur-sm" style="margin:auto;">
    <div class="border-b border-white/10 px-6 py-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-xl font-semibold">{{ $texts['denomination']['title'] }}</h3>
                <p class="mt-2 text-sm text-white/60">{{ $texts['denomination']['search_placeholder'] }}</p>
            </div>
            <button type="button" id="denominacao_close_modal" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/15 text-xl leading-none text-white/70 transition hover:border-white/40 hover:text-white" aria-label="Fechar modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    <div class="px-6 py-5">
        <label class="block">
            <span class="text-sm font-medium text-white/80">{{ $texts['denomination']['search_label'] }}</span>
            <input type="search" id="denominacao_search" placeholder="{{ $texts['denomination']['search_placeholder'] }}" class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white placeholder-white/40 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/40" autocomplete="off">
        </label>
        <div id="denominacao_results" class="mt-4 rounded-2xl border border-white/10 bg-white/5 shadow-lg shadow-black/20">
            <ul class="max-h-72 overflow-y-auto divide-y divide-white/5" role="listbox">
                @foreach ($denominacoes as $denominacao)
                    <li data-denominacao-item
                        data-id="{{ $denominacao->id }}"
                        data-label="{{ $denominacao->nome }}"
                        class="cursor-pointer px-4 py-3 text-sm text-white/80 hover:bg-white/10 hover:text-white transition {{ old('igreja') == $denominacao->id ? 'bg-[#6449a2]/30 text-white' : '' }}"
                        role="option"
                        aria-selected="{{ old('igreja') == $denominacao->id ? 'true' : 'false' }}">
                        {{ $denominacao->nome }}
                    </li>
                @endforeach
            </ul>
            <p id="denominacao_empty_state" class="hidden px-4 py-3 text-xs text-rose-200">{{ $texts['denomination']['empty'] }}</p>
        </div>
    </div>
    <div class="flex flex-col-reverse gap-3 border-t border-white/10 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
        <button type="button" id="denominacao_cancel_modal" class="inline-flex items-center justify-center rounded-xl border border-white/15 px-4 py-3 text-sm font-medium text-white/80 transition hover:border-white/40">
            {{ $texts['buttons']['back'] }}
        </button>
        <button type="button" id="denominacao_confirm_modal" class="inline-flex items-center justify-center rounded-xl bg-[#6449a2] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-[#6449a2]/30 transition hover:bg-[#584091] disabled:cursor-not-allowed disabled:opacity-50">
            {{ $texts['denomination']['selected_label'] }}
        </button>
    </div>
</dialog>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

@endsection

@push('scripts')
<script>
    $(document).ready(function(){
        $('#telefone').mask('(00) 00000-0000');
        $('#gestor_telefone').mask('(00) 00000-0000');
        $('#cep').mask('00000-000');
        $('#cnpj').mask('00.000.000/0000-00');
        $('#gestor_cpf').mask('000.000.000-00');
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('denominacao_modal');
        const openModalButton = document.getElementById('denominacao_open_modal');
        const closeModalButton = document.getElementById('denominacao_close_modal');
        const cancelModalButton = document.getElementById('denominacao_cancel_modal');
        const confirmModalButton = document.getElementById('denominacao_confirm_modal');
        const searchInput = document.getElementById('denominacao_search');
        const resultsContainer = document.getElementById('denominacao_results');
        const hiddenInput = document.getElementById('igreja');
        const selectedCard = document.getElementById('denominacao_selected');
        const selectedName = document.getElementById('denominacao_selected_name');
        const triggerText = document.getElementById('denominacao_trigger_text');
        const emptyState = document.getElementById('denominacao_empty_state');
        const clearButton = document.getElementById('denominacao_clear');

        if (modal && searchInput && resultsContainer && hiddenInput) {
            const options = Array.from(resultsContainer.querySelectorAll('[data-denominacao-item]'));
            let pendingSelectionId = hiddenInput.value || '';

            const highlightSelection = (id) => {
                options.forEach((option) => {
                    const isSelected = option.dataset.id === id && id !== '';
                    option.classList.toggle('bg-[#6449a2]/30', isSelected);
                    option.classList.toggle('text-white', isSelected);
                    option.setAttribute('aria-selected', isSelected ? 'true' : 'false');
                });
            };

            const updateSelectedCard = (label) => {
                if (!selectedCard || !selectedName) {
                    return;
                }

                if (label) {
                    selectedName.textContent = label;
                    if (triggerText) {
                        triggerText.textContent = label;
                        triggerText.classList.remove('text-white/60');
                        triggerText.classList.add('text-white');
                    }
                    selectedCard.classList.remove('hidden');
                } else {
                    selectedName.textContent = '';
                    if (triggerText) {
                        triggerText.textContent = '{{ $texts['denomination']['search_placeholder'] }}';
                        triggerText.classList.add('text-white/60');
                        triggerText.classList.remove('text-white');
                    }
                    selectedCard.classList.add('hidden');
                }
            };

            const filterOptions = (term) => {
                const normalized = term.trim().toLowerCase();
                let visibleCount = 0;

                options.forEach((option) => {
                    const label = option.dataset.label ? option.dataset.label.toLowerCase() : '';
                    const shouldShow = normalized === '' || label.includes(normalized);
                    option.classList.toggle('hidden', !shouldShow);

                    if (shouldShow) {
                        visibleCount += 1;
                    }
                });

                if (emptyState) {
                    const showEmpty = normalized.length > 0 && visibleCount === 0;
                    emptyState.classList.toggle('hidden', !showEmpty);
                }

                return visibleCount;
            };

            const updateConfirmState = () => {
                if (confirmModalButton) {
                    confirmModalButton.disabled = pendingSelectionId === '';
                }
            };

            const setPendingSelection = (option) => {
                pendingSelectionId = option.dataset.id || '';
                highlightSelection(pendingSelectionId);
                updateConfirmState();
            };

            const openModal = () => {
                pendingSelectionId = hiddenInput.value || '';
                highlightSelection(pendingSelectionId);
                searchInput.value = '';
                filterOptions('');
                updateConfirmState();
                modal.showModal();
                setTimeout(() => searchInput.focus(), 0);
            };

            const closeModal = () => {
                modal.close();
            };

            options.forEach((option) => {
                option.addEventListener('click', () => {
                    setPendingSelection(option);
                });
            });

            if (openModalButton) {
                openModalButton.addEventListener('click', openModal);
            }

            if (closeModalButton) {
                closeModalButton.addEventListener('click', closeModal);
            }

            if (cancelModalButton) {
                cancelModalButton.addEventListener('click', closeModal);
            }

            if (confirmModalButton) {
                confirmModalButton.addEventListener('click', () => {
                    const selectedOption = options.find((option) => option.dataset.id === pendingSelectionId);
                    hiddenInput.value = pendingSelectionId;
                    updateSelectedCard(selectedOption ? (selectedOption.dataset.label || '') : '');
                    highlightSelection(hiddenInput.value);
                    closeModal();
                });
            }

            if (clearButton) {
                clearButton.addEventListener('click', () => {
                    hiddenInput.value = '';
                    pendingSelectionId = '';
                    highlightSelection('');
                    updateSelectedCard('');
                    updateConfirmState();
                    openModal();
                });
            }

            searchInput.addEventListener('input', () => {
                filterOptions(searchInput.value);
            });

            searchInput.addEventListener('search', () => {
                filterOptions(searchInput.value);
            });

            searchInput.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    event.preventDefault();
                    closeModal();
                }
            });

            modal.addEventListener('click', (event) => {
                const rect = modal.getBoundingClientRect();
                const clickedOutside = event.clientY < rect.top
                    || event.clientY > rect.bottom
                    || event.clientX < rect.left
                    || event.clientX > rect.right;

                if (clickedOutside) {
                    closeModal();
                }
            });

            if (hiddenInput.value) {
                const selectedOption = options.find((option) => option.dataset.id === hiddenInput.value);
                if (selectedOption) {
                    updateSelectedCard(selectedOption.dataset.label || '');
                    highlightSelection(hiddenInput.value);
                    pendingSelectionId = hiddenInput.value;
                }
            } else {
                updateSelectedCard('');
            }
        }

        const paisSelect = document.getElementById('pais');
        const estadoSelect = document.getElementById('estado');
        const cidadeSelect = document.getElementById('cidade');

        if (paisSelect && estadoSelect && cidadeSelect) {
            const estadosOptions = Array.from(estadoSelect.querySelectorAll('option[data-pais-id]'));
            const cidadesOptions = Array.from(cidadeSelect.querySelectorAll('option[data-estado-id], option[data-estado-uf]'));

            const toggleSelectState = (select, disabled) => {
                select.disabled = disabled;
                select.style.opacity = disabled ? '0.5' : '';
                select.style.cursor = disabled ? 'not-allowed' : '';
            };

            const updateCidadeOptions = () => {
                const estadoId = estadoSelect.value;
                const estadoOption = estadoSelect.options[estadoSelect.selectedIndex] || null;
                const estadoUf = estadoOption ? (estadoOption.dataset.uf || '') : '';
                let visibleCount = 0;
                let resetNeeded = estadoId === '';

                cidadesOptions.forEach((option) => {
                    if (!option.value) {
                        return;
                    }

                    const optionEstadoId = option.dataset.estadoId || '';
                    const optionEstadoUf = option.dataset.estadoUf || '';
                    const matchesById = estadoId !== '' && optionEstadoId === estadoId;
                    const matchesByUf = estadoUf !== '' && optionEstadoUf === estadoUf;
                    const matches = matchesById || matchesByUf;

                    option.hidden = !matches;
                    option.disabled = !matches;
                    option.style.display = matches ? '' : 'none';

                    if (!matches && option.selected) {
                        option.selected = false;
                        resetNeeded = true;
                    }

                    if (matches) {
                        visibleCount += 1;
                    }
                });

                if (resetNeeded || visibleCount === 0) {
                    cidadeSelect.value = '';
                }

                toggleSelectState(cidadeSelect, visibleCount === 0);
            };

            const updateEstadoOptions = () => {
                const paisId = paisSelect.value;
                let visibleCount = 0;
                let resetNeeded = paisId === '';

                estadosOptions.forEach((option) => {
                    if (!option.value) {
                        return;
                    }

                    const matches = paisId !== '' && option.dataset.paisId === paisId;
                    option.hidden = !matches;
                    option.disabled = !matches;
                    option.style.display = matches ? '' : 'none';

                    if (!matches && option.selected) {
                        option.selected = false;
                        resetNeeded = true;
                    }

                    if (matches) {
                        visibleCount += 1;
                    }
                });

                if (resetNeeded || visibleCount === 0) {
                    estadoSelect.value = '';
                }

                toggleSelectState(estadoSelect, visibleCount === 0);
                updateCidadeOptions();
            };

            updateEstadoOptions();

            paisSelect.addEventListener('change', () => {
                updateEstadoOptions();
            });

            estadoSelect.addEventListener('change', () => {
                updateCidadeOptions();
            });
        }
    });
</script>
@endpush
