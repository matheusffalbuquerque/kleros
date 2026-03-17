@extends('layouts.site')

@section('title', __('congregations.meta.register_title'))
@section('meta_description', __('site.meta.description'))

@section('content')
@php
    $texts = trans('congregations.cadastro');
    $basicFields = $texts['basic']['fields'];
    $visualSection = $texts['visual'] ?? null;
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
                    <label class="block">
                        <span class="text-sm font-medium text-white/80">{{ $texts['denomination']['search_label'] }}</span>
                        <input type="search" id="denominacao_search" placeholder="{{ $texts['denomination']['search_placeholder'] }}" class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white placeholder-white/40 focus:border-[#6449a2] focus:outline-none focus:ring-2 focus:ring-[#6449a2]/40" autocomplete="off">
                    </label>
                    <input type="hidden" name="igreja" id="igreja" value="{{ old('igreja') }}" required>
                    <div id="denominacao_selected" class="rounded-xl border border-[#6449a2]/60 bg-[#6449a2]/20 px-4 py-3 text-sm text-white/90 flex items-center justify-between {{ $preselectedDenominacao ? '' : 'hidden' }}">
                        <div class="flex flex-col">
                            <span class="text-xs uppercase tracking-[0.2em] text-white/50">{{ $texts['denomination']['selected_label'] }}</span>
                            <span id="denominacao_selected_name" class="mt-1 font-medium">{{ optional($preselectedDenominacao)->nome }}</span>
                        </div>
                        <button type="button" id="denominacao_clear" class="rounded-lg border border-white/20 px-3 py-1 text-xs font-medium text-white/80 hover:border-white/40">
                            {{ $texts['denomination']['toggle'] }}
                        </button>
                    </div>
                    <div id="denominacao_results" class="hidden rounded-2xl border border-white/10 bg-white/5 shadow-lg shadow-black/20">
                        <ul class="max-h-64 overflow-y-auto divide-y divide-white/5" role="listbox">
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
            @if($visualSection)
            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold">{{ $visualSection['title'] }}</h2>
                    <p class="text-white/60 text-sm mt-2">{{ $visualSection['subtitle'] }}</p>
                </div>
                <div class="grid gap-6 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-white/80">{{ $visualSection['logo_label'] }}</span>
                        <div class="mt-3 flex items-center gap-4 rounded-2xl border border-white/10 bg-white/5 p-4">
                            <div class="relative h-16 w-16 shrink-0 rounded-xl bg-white/10 flex items-center justify-center overflow-hidden" data-preview-container="logo">
                                <img src="{{ asset('storage/images/loading.gif') }}" alt="Carregando logo" class="absolute inset-0 h-full w-full object-contain hidden" data-preview-loader>
                                <img src="" alt="Pré-visualização do logo" class="hidden h-full w-full object-cover" data-preview-image>
                                <i class="bi bi-image text-2xl text-white/40" data-preview-placeholder></i>
                            </div>
                            <div class="min-w-0 flex-1 space-y-2">
                                <span data-preview-filename="logo" data-placeholder="{{ $visualSection['logo_placeholder'] }}" class="block truncate text-sm text-white/60" title="{{ $visualSection['logo_placeholder'] }}">{{ $visualSection['logo_placeholder'] }}</span>
                                <label class="inline-flex items-center gap-2 rounded-lg border border-white/20 px-3 py-2 text-sm text-white/80 hover:border-white/50 cursor-pointer">
                                    <i class="bi bi-upload"></i> {{ $visualSection['upload'] }}
                                    <input type="file" name="logo" id="logo" class="hidden" accept="image/*" data-preview-input="logo">
                                </label>
                            </div>
                        </div>
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-white/80">{{ $visualSection['banner_label'] }}</span>
                        <div class="mt-3 flex items-center gap-4 rounded-2xl border border-white/10 bg-white/5 p-4">
                            <div class="relative h-16 w-24 shrink-0 rounded-xl bg-white/10 flex items-center justify-center overflow-hidden" data-preview-container="banner">
                                <img src="{{ asset('storage/images/loading.gif') }}" alt="Carregando banner" class="absolute inset-0 h-full w-full object-contain hidden" data-preview-loader>
                                <img src="" alt="Pré-visualização do banner" class="hidden h-full w-full object-cover" data-preview-image>
                                <i class="bi bi-images text-2xl text-white/40" data-preview-placeholder></i>
                            </div>
                            <div class="min-w-0 flex-1 space-y-2">
                                <span data-preview-filename="banner" data-placeholder="{{ $visualSection['banner_placeholder'] }}" class="block truncate text-sm text-white/60" title="{{ $visualSection['banner_placeholder'] }}">{{ $visualSection['banner_placeholder'] }}</span>
                                <label class="inline-flex items-center gap-2 rounded-lg border border-white/20 px-3 py-2 text-sm text-white/80 hover:border-white/50 cursor-pointer">
                                    <i class="bi bi-upload"></i> {{ $visualSection['upload'] }}
                                    <input type="file" name="banner" id="banner" class="hidden" accept="image/*" data-preview-input="banner">
                                </label>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
            @endif
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
        const previewInputs = document.querySelectorAll('[data-preview-input]');

        previewInputs.forEach((input) => {
            const key = input.dataset.previewInput;
            const container = document.querySelector(`[data-preview-container="${key}"]`);
            if (!container) {
                return;
            }

            const loader = container.querySelector('[data-preview-loader]');
            const previewImage = container.querySelector('[data-preview-image]');
            const placeholder = container.querySelector('[data-preview-placeholder]');
            const filenameLabel = document.querySelector(`[data-preview-filename="${key}"]`);
            const placeholderText = filenameLabel ? filenameLabel.dataset.placeholder || filenameLabel.textContent : '';

            if (previewImage && !previewImage.dataset.previewOriginal) {
                previewImage.dataset.previewOriginal = previewImage.getAttribute('src') || '';
            }

            input.addEventListener('change', () => {
                const file = input.files && input.files[0] ? input.files[0] : null;

                if (filenameLabel) {
                    const labelText = file ? file.name : placeholderText;
                    filenameLabel.textContent = labelText;
                    filenameLabel.title = labelText;
                }

                if (file) {
                    placeholder?.classList.add('hidden');
                    if (previewImage) {
                        previewImage.classList.add('hidden');
                    }
                    if (loader) {
                        loader.classList.remove('hidden');
                    }

                    const reader = new FileReader();
                    reader.addEventListener('load', (event) => {
                        if (loader) {
                            loader.classList.add('hidden');
                        }
                        if (previewImage) {
                            previewImage.src = event.target?.result || '';
                            previewImage.classList.remove('hidden');
                        }
                    });
                    reader.addEventListener('error', () => {
                        if (loader) {
                            loader.classList.add('hidden');
                        }
                        if (previewImage) {
                            const original = previewImage.dataset.previewOriginal || '';
                            if (original) {
                                previewImage.src = original;
                                previewImage.classList.remove('hidden');
                            } else {
                                previewImage.src = '';
                                previewImage.classList.add('hidden');
                            }
                        }
                        placeholder?.classList.remove('hidden');
                    });
                    reader.readAsDataURL(file);
                } else {
                    if (loader) {
                        loader.classList.add('hidden');
                    }

                    if (previewImage) {
                        const original = previewImage.dataset.previewOriginal || '';
                        if (original) {
                            previewImage.src = original;
                            previewImage.classList.remove('hidden');
                        } else {
                            previewImage.src = '';
                            previewImage.classList.add('hidden');
                        }
                    }

                    placeholder?.classList.remove('hidden');
                }
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('denominacao_search');
        const resultsContainer = document.getElementById('denominacao_results');
        const hiddenInput = document.getElementById('igreja');
        const selectedCard = document.getElementById('denominacao_selected');
        const selectedName = document.getElementById('denominacao_selected_name');
        const emptyState = document.getElementById('denominacao_empty_state');
        const clearButton = document.getElementById('denominacao_clear');

        if (searchInput && resultsContainer && hiddenInput) {
            const options = Array.from(resultsContainer.querySelectorAll('[data-denominacao-item]'));

            const highlightSelection = (id) => {
                options.forEach((option) => {
                    const isSelected = option.dataset.id === id && id !== '';
                    option.classList.toggle('bg-[#6449a2]/30', isSelected);
                    option.classList.toggle('text-white', isSelected);
                    option.setAttribute('aria-selected', isSelected ? 'true' : 'false');
                });
            };

            const showResults = () => {
                resultsContainer.classList.remove('hidden');
            };

            const hideResults = () => {
                resultsContainer.classList.add('hidden');
            };

            const updateSelectedCard = (label) => {
                if (!selectedCard || !selectedName) {
                    return;
                }

                if (label) {
                    selectedName.textContent = label;
                    selectedCard.classList.remove('hidden');
                } else {
                    selectedName.textContent = '';
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

                if (visibleCount > 0) {
                    showResults();
                }
            };

            const selectOption = (option) => {
                const id = option.dataset.id || '';
                const label = option.dataset.label || '';

                hiddenInput.value = id;
                updateSelectedCard(label);
                highlightSelection(id);
                searchInput.value = label;
                hideResults();
            };

            options.forEach((option) => {
                option.addEventListener('mousedown', (event) => {
                    event.preventDefault();
                    selectOption(option);
                });
            });

            if (clearButton) {
                clearButton.addEventListener('click', () => {
                    hiddenInput.value = '';
                    searchInput.value = '';
                    highlightSelection('');
                    updateSelectedCard('');
                    filterOptions('');
                    showResults();
                    searchInput.focus();
                });
            }

            searchInput.addEventListener('focus', () => {
                filterOptions(searchInput.value);
                showResults();
            });

            searchInput.addEventListener('input', () => {
                filterOptions(searchInput.value);
                showResults();
            });

            searchInput.addEventListener('search', () => {
                filterOptions(searchInput.value);
                showResults();
            });

            searchInput.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    hideResults();
                    searchInput.blur();
                }
            });

            document.addEventListener('click', (event) => {
                const target = event.target;
                if (target === searchInput || resultsContainer.contains(target) || (selectedCard && selectedCard.contains(target))) {
                    return;
                }
                hideResults();
            });

            if (hiddenInput.value) {
                const selectedOption = options.find((option) => option.dataset.id === hiddenInput.value);
                if (selectedOption) {
                    updateSelectedCard(selectedOption.dataset.label || '');
                    highlightSelection(hiddenInput.value);
                }
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
