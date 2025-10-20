@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')
<div class="container">
    <h1>Departamento: {{ $departamento->nome }}</h1>

    @if (session('msg'))
        <div class="alert success nao-imprimir">
            <i class="bi bi-check-circle"></i> {{ session('msg') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert danger nao-imprimir">
            <i class="bi bi-exclamation-triangle"></i> Selecione um membro válido para adicionar.
        </div>
    @endif

    <div class="info">
        <h3>Integrantes</h3>
        <form action="{{ route('departamentos.integrantes.adicionar') }}" method="POST">
            @csrf
            <div class="search-panel">
                <div class="search-panel-item">
                    <label>Adicionar membros:</label>
                    <select name="membro" id="membro" class="select2" data-placeholder="Selecione um membro" data-search-placeholder="Pesquise por membro">
                        <option value="">Selecione um membro</option>
                        @foreach ($membros as $item)
                            <option value="{{ $item->id }}" @selected(old('membro') == $item->id)>{{ $item->nome }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="departamento" value="{{ $departamento->id }}">
                </div>
                <div class="search-panel-item">
                    <button type="submit" id="btn_filtrar"><i class="bi bi-plus-circle"></i> Incluir</button>
                    <button type="button" id="btn_filtrar" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
                    <a href="/cadastros#departamentos">
                        <button type="button" id="btn_filtrar"><i class="bi bi-arrow-return-left"></i> Voltar</button>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="list">
        <div class="list-title">
            <div class="item-1">
                <b>Nome</b>
            </div>
            <div class="item-1">
                <b>Telefone</b>
            </div>
            <div class="item-2">
                <b>Endereço</b>
            </div>
            <div class="item-1">
                <b>Ministério</b>
            </div>
        </div>
        @forelse ($integrantes as $item)
            <div class="list-item taggable-item">
                <div class="item item-1 integrante-info">
                    <p>{{ $item->nome }}</p>
                </div>
                <div class="item item-1 integrante-info">
                    <p>{{ $item->telefone ?? '—' }}</p>
                </div>
                <div class="item item-2 integrante-info">
                    <p>
                        {{ $item->endereco ? $item->endereco . ', ' . $item->numero : '—' }}
                        @if ($item->bairro)
                            - {{ $item->bairro }}
                        @endif
                    </p>
                </div>
                <div class="item item-1 integrante-info">
                    <p>{{ optional($item->ministerio)->titulo }}</p>
                </div>
                <div class="taggable-actions">
                    <form action="{{ route('departamentos.integrantes.remover', [$departamento->id, $item->id]) }}" method="POST" onsubmit="return handleSubmit(event, this, 'Remover {{ $item->nome }} do departamento?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Remover integrante">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="card">
                <p><i class="bi bi-info-circle"></i> Nenhum integrante cadastrado neste departamento.</p>
            </div>
        @endforelse

        @if($integrantes instanceof \Illuminate\Pagination\LengthAwarePaginator && $integrantes->total() > $integrantes->perPage())
            <div class="pagination">
                {{ $integrantes->links('pagination::default') }}
            </div>
        @endif
    </div>
</div>
@endsection
