@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')
@php
    $members = trans('members');
    $common = $members['common'];
    $painel = $members['painel'];
    $showingInactives = $showingInactives ?? false;
@endphp

<div class="container">
    <div class="nao-imprimir">
        <h1>{{ $painel['title'] }}</h1>
        <div class="info">
            <h3>{{ $painel['search']['heading'] }}</h3>
            <form>
                @csrf
                <div class="search-panel">
                    <div class="search-panel-item">
                        <label for="filtro">{{ $painel['search']['filter_label'] }}:</label>
                        <select id="filtro">
                            @foreach ($painel['search']['filters'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="search-panel-item">
                        <label for="chave">{{ $painel['search']['keyword_label'] }}:</label>
                        <input type="text" id="chave" placeholder="{{ $common['placeholders']['keyword'] }}">
                    </div>
                    <div class="search-panel-item">
                        <button id="btn_filtrar" type="button"><i class="bi bi-search"></i> {{ $common['buttons']['search'] }}</button>
                        <a href="{{ route('membros.adicionar') }}">
                            <button type="button"><i class="bi bi-plus-circle"></i> {{ $common['buttons']['add'] }}</button>
                        </a>
                        <button id="btn_exportar" type="button" data-export-url="{{ route('membros.export') }}"><i class="bi bi-file-arrow-up"></i> {{ $common['buttons']['export'] }}</button>
                        <button class="options-menu__trigger" type="button" data-options-target="membrosPainelOptions"><i class="bi bi-three-dots-vertical"></i> {{ $common['buttons']['options'] }}</button>
                    </div>
                </div>
                <div class="options-menu" id="membrosPainelOptions" hidden>
                    <button type="button" class="btn" data-action="print"><i class="bi bi-printer"></i> {{ $painel['options']['print'] }}</button>
                    @if($showingInactives)
                        <button type="button" class="btn" data-action="redirect" data-url="{{ route('membros.painel') }}"><i class="bi bi-people"></i> {{ $painel['options']['show_actives'] ?? 'Ver ativos' }}</button>
                    @else
                        <button type="button" class="btn" data-action="redirect" data-url="{{ route('membros.inativos') }}"><i class="bi bi-person-x"></i> {{ $painel['options']['show_inactives'] }}</button>
                    @endif
                    @if(module_enabled('batismo') && Route::has('batismo.painel'))
                        <button type="button" class="btn" data-action="redirect" data-url="{{ route('batismo.painel') }}"><i class="bi bi-water"></i> Batismos</button>
                    @endif
                    <button type="button" class="btn" data-action="redirect" data-url="{{ route('membros.aniversariantes') }}"><i class="bi bi-cake2"></i> Aniversariantes</button>
                    <button type="button" class="btn" data-action="back"><i class="bi bi-arrow-return-left"></i> {{ $painel['options']['back'] }}</button>
                </div>
            </form>
        </div>
    </div>

    <div id="list" class="list">
        <div class="list-title">
            <div class="item-2">
                <b>{{ $common['table']['name'] }}</b>
            </div>
            <div class="item-1">
                <b>{{ $common['table']['phone'] }}</b>
            </div>
            <div class="item-2">
                <b>{{ $common['table']['address'] }}</b>
            </div>
            <div class="item-1">
                <b>{{ $common['table']['ministry'] }}</b>
            </div>
        </div>
        <div id="content">
            @foreach ($membros as $item)
                <a href="{{ url('/membros/exibir/' . $item->id) }}">
                    <div class="list-item">
                        <div class="item item-2">
                            <p style="display:flex; align-items: center; gap:.5em">
                                <img src="{{ $item->foto ? asset('storage/' . $item->foto) : asset('storage/images/newuser.png') }}" class="avatar" alt="Avatar">
                                {{ $item->nome }}
                            </p>
                        </div>
                        <div class="item item-1">
                            <p>{{ $item->telefone }}</p>
                        </div>
                        <div class="item item-2">
                            <p>{{ $item->endereco }}, {{ $item->numero }} - {{ $item->bairro }}</p>
                        </div>
                        <div class="item item-1">
                            <p>{{ optional($item->ministerio)->titulo ?? $common['statuses']['not_informed'] }}</p>
                        </div>
                    </div>
                </a>
            @endforeach

            @if ($membros->total() > 10)
                <div class="pagination">
                    {{ $membros->links('pagination::default') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        const filterBtn = document.getElementById('btn_filtrar');
        const exportBtn = document.getElementById('btn_exportar');
        const filterInput = document.getElementById('filtro');
        const keywordInput = document.getElementById('chave');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const contentTarget = document.getElementById('content');
        const notInformed = @json($common['statuses']['not_informed']);
        const searchEmpty = @json($members['search']['empty']);
        const showingInactives = @json($showingInactives);

        function filtrar() {
            
            if (!csrfToken) {
                return;
            }

            const filtro = filterInput.value;
            const chave = keywordInput.value;

            fetch('{{ route('membros.search') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ filtro, chave, showInactives: showingInactives }),
            })
            .then(response => response.json())
            .then(({ view }) => {
                if (view && contentTarget) {
                    contentTarget.innerHTML = view;
                }
            })
            .catch(() => {
                if (contentTarget) {
                    contentTarget.innerHTML = `<div class="card"><p>${searchEmpty}</p></div>`;
                }
            });
        }

        if (filterBtn) {
            filterBtn.addEventListener('click', function (event) {
                event.preventDefault();
                filtrar();
            });
        }

        if (keywordInput) {
            keywordInput.addEventListener('keypress', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    filtrar();
                }
            });
        }

        if (exportBtn) {
            exportBtn.addEventListener('click', function (event) {
                event.preventDefault();
                const url = this.dataset.exportUrl;
                const filtro = filterInput.value;
                const chave = keywordInput.value;

                const params = new URLSearchParams();
                if (filtro) params.append('filtro', filtro);
                if (chave) params.append('chave', chave);

                window.location.href = params.toString() ? `${url}?${params.toString()}` : url;
            });
        }
    })();
</script>
@endpush
