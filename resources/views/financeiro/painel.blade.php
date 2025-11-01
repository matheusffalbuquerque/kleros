@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')
<div class="container">
    <h1>Painel Financeiro</h1>
    <div class="info">
        <h3>Movimentações financeiras</h3>

        <div class="search-panel">
            <div class="search-panel-item">
                <label for="caixa">Caixa:</label>
                <select name="caixa" id="caixa">
                    <option value="">Todos os caixas</option>
                    @foreach($caixas as $caixa)
                        <option value="{{ $caixa->id }}" @selected(request('caixa') == $caixa->id)>{{ $caixa->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="search-panel-item">
                <label for="tipo">Tipo:</label>
                <select name="tipo" id="tipo">
                    <option value="">Entrada e saída</option>
                    <option value="entrada" @selected(request('tipo') === 'entrada')>Entradas</option>
                    <option value="saida" @selected(request('tipo') === 'saida')>Saídas</option>
                </select>
            </div>
            <div class="search-panel-item">
                <label for="tipo_lancamento_id">Tipo de lançamento:</label>
                <select name="tipo_lancamento_id" id="tipo_lancamento_id">
                    <option value="">Todos</option>
                    @foreach($tiposLancamento as $tipo)
                        <option value="{{ $tipo->id }}" @selected(request('tipo_lancamento_id') == $tipo->id)>{{ $tipo->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="search-panel-item">
                <label for="data_inicio">De:</label>
                <input type="date" name="data_inicio" id="data_inicio" value="{{ request('data_inicio') }}">
            </div>
            <div class="search-panel-item">
                <label for="data_fim">Até:</label>
                <input type="date" name="data_fim" id="data_fim" value="{{ request('data_fim') }}">
            </div>
            <div class="search-panel-item">
                <button type="button" class="" id="btn_filtrar"><i class="bi bi-search"></i> Procurar</button>
                <button type="button" class="" id="btn_novo_lancamento"
                    data-base-url="{{ route('financeiro.lancamentos.form_criar', ['caixa' => '__CAIXA__']) }}"
                    data-default-caixa="{{ optional($caixas->first())->id }}">
                    <i class="bi bi-plus-circle"></i> Lançar
                </button>
                <button type="button" class="" id="btn_exportar_financeiro" data-export-url="{{ route('financeiro.lancamentos.export') }}"><i class="bi bi-file-arrow-up"></i> Exportar</button>
                <button class="" id="btn_limpar"><i class="bi bi-eraser"></i> Limpar</button>
                <button class="options-menu__trigger" type="button" data-options-target="financeiroPainelOptions"><i class="bi bi-three-dots-vertical"></i> Opções</button>
            </div>
        </div>
        <div class="options-menu" id="financeiroPainelOptions" hidden>
            <button type="button" class="btn" data-action="financeiro:novo-caixa"><i class="bi bi-bank"></i> Novo caixa</button>
            <button type="button" class="btn" data-action="financeiro:novo-tipo"><i class="bi bi-sliders"></i> Tipo de lançamento</button>
        </div>

        <div class="list">
            <div class="list-title">
                <div class="item-1"><b>Data</b></div>
                <div class="item-1"><b>Caixa</b></div>
                <div class="item-2"><b>Descrição</b></div>
                <div class="item-1"><b>Tipo</b></div>
                <div class="item-1"><b>Categoria</b></div>
                <div class="item-1"><b>Valor</b></div>
            </div>
            <div id="content">
                @forelse($lancamentos as $lancamento)
                    <div class="list-item" onclick="abrirJanelaModal('{{ route('financeiro.lancamentos.form_editar', ['id' => $lancamento->id]) }}')">
                        <div class="item item-1">
                            <p>{{ optional($lancamento->data_lancamento)->format('d/m/Y') }}</p>
                        </div>
                        <div class="item item-1">
                            <p><i class="bi bi-currency-exchange"></i> {{ optional($lancamento->caixa)->nome ?? '—' }}</p>
                        </div>
                        <div class="item item-2">
                            <p>{{ $lancamento->descricao ?? '—' }}</p>
                        </div>
                        <div class="item item-1">
                            <p>{{ ucfirst($lancamento->tipo) }}</p>
                        </div>
                        <div class="item item-1">
                            <p>{{ optional($lancamento->tipoLancamento)->nome ?? '—' }}</p>
                        </div>
                        <div class="item item-1">
                            <p class="{{ $lancamento->tipo === 'entrada' ? 'text-success' : 'text-danger' }}">R$ {{ number_format($lancamento->valor, 2, ',', '.') }}</p>
                        </div>                        
                    </div>
                @empty
                    <div class="card">
                        <p><i class="bi bi-exclamation-triangle"></i> Nenhum lançamento encontrado para os filtros selecionados.</p>
                    </div>
                @endforelse
            </div>
            <div class="pagination">
            {{ $lancamentos->withQueryString()->links('pagination::default') }}
            </div>
        </div>
    </div>

    {{-- Componente Livewire da Calculadora --}}
    @livewire('calculadora')
    
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        function filtrar(event) {
            if (event) {
                event.preventDefault();
            }

            const params = new URLSearchParams();
            const caixa = $('#caixa').val();
            const tipo = $('#tipo').val();
            const tipoLancamento = $('#tipo_lancamento_id').val();
            const dataInicio = $('#data_inicio').val();
            const dataFim = $('#data_fim').val();

            if (caixa) params.set('caixa', caixa);
            if (tipo) params.set('tipo', tipo);
            if (tipoLancamento) params.set('tipo_lancamento_id', tipoLancamento);
            if (dataInicio) params.set('data_inicio', dataInicio);
            if (dataFim) params.set('data_fim', dataFim);

            const query = params.toString();
            const url = query ? `${window.location.pathname}?${query}` : window.location.pathname;
            window.location.href = url;
        }

        $('#btn_filtrar').on('click', filtrar);

        $('#btn_limpar').on('click', function () {
            window.location.href = window.location.pathname;
        });

        $('#btn_novo_lancamento').on('click', function () {
            const baseUrl = this.dataset.baseUrl;
            const selectedCaixa = $('#caixa').val();
            const defaultCaixa = this.dataset.defaultCaixa;
            const caixaId = selectedCaixa || defaultCaixa;

            if (!caixaId) {
                alert('Selecione ou cadastre um caixa antes de lançar.');
                return;
            }

            const url = baseUrl.replace('__CAIXA__', caixaId);
            abrirJanelaModal(url);
        });

        $('#btn_exportar_financeiro').on('click', function (event) {
            event.preventDefault();

            const url = this.dataset.exportUrl;
            const params = new URLSearchParams();

            const caixa = $('#caixa').val();
            const tipo = $('#tipo').val();
            const tipoLancamento = $('#tipo_lancamento_id').val();
            const dataInicio = $('#data_inicio').val();
            const dataFim = $('#data_fim').val();

            if (caixa) params.append('caixa', caixa);
            if (tipo) params.append('tipo', tipo);
            if (tipoLancamento) params.append('tipo_lancamento_id', tipoLancamento);
            if (dataInicio) params.append('data_inicio', dataInicio);
            if (dataFim) params.append('data_fim', dataFim);

            const finalUrl = params.toString() ? `${url}?${params.toString()}` : url;
            window.location.href = finalUrl;
        });

        document.addEventListener('options-menu:action', function (event) {
            const action = event.detail?.action;

            if (!action || !action.startsWith('financeiro:')) {
                return;
            }

            if (action === 'financeiro:novo-caixa') {
                abrirJanelaModal('{{ route('financeiro.caixas.form_criar') }}');
            } else if (action === 'financeiro:novo-tipo') {
                abrirJanelaModal('{{ route('financeiro.tipos.form_criar') }}');
            }
        });
    });
</script>
        
@endpush
