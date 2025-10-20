@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')
<div class="container">
    <div class="nao-imprimir">
        <h1>Respostas da Pesquisa</h1>
        <div class="info header-info">
            <div>
                <h3>{{ $pesquisa->titulo }}</h3>
                <p>{{ $pesquisa->descricao ?: 'Sem descrição disponível para esta pesquisa.' }}</p>
            </div>
            <div>
                <a href="{{ route('pesquisas.painel') }}" class="btn">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>

    <div class="info filtros nao-imprimir">
        <form action="{{ route('pesquisas.respostas', $pesquisa->id) }}" method="GET" class="filter-form">
            <div class="form-control filtro-grid">
                <div class="form-item">
                    <label for="membro">Filtrar por membro</label>
                    <select name="membro" id="membro" class="select2" data-placeholder="Selecione um membro">
                        <option value="">Todos os membros</option>
                        @foreach ($membros as $membroOption)
                            <option value="{{ $membroOption->id }}" @selected($membroId == $membroOption->id)>
                                {{ $membroOption->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-item">
                    <label for="pergunta">Filtrar por pergunta</label>
                    <select name="pergunta" id="pergunta" class="select2" data-placeholder="Selecione uma pergunta">
                        <option value="">Todas as perguntas</option>
                        @foreach ($perguntas as $perguntaOption)
                            <option value="{{ $perguntaOption->id }}" @selected($perguntaId == $perguntaOption->id)>
                                {{ $perguntaOption->texto }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-item form-actions">
                    <button type="submit" class="btn"><i class="bi bi-filter"></i> Aplicar</button>
                    <a href="{{ route('pesquisas.respostas', $pesquisa->id) }}" class="btn btn-outline">
                        <i class="bi bi-x-circle"></i> Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="list respostas-list">
        <div class="list-title">
            <div class="item-1">
                <b>Data</b>
            </div>
            <div class="item-1">
                <b>Membro</b>
            </div>
            <div class="item-2">
                <b>Pergunta</b>
            </div>
            <div class="item-2">
                <b>Resposta</b>
            </div>
        </div>
        @forelse ($respostas as $resposta)
            @php
                $textoResposta = $resposta->resposta_texto;
                if (!$textoResposta) {
                    $textoResposta = $resposta->opcoes
                        ->map(fn ($opcao) => optional($opcao->opcao)->texto)
                        ->filter()
                        ->implode(', ');
                }
            @endphp
            <div class="list-item">
                <div class="item item-1">
                    <p>{{ $resposta->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="item item-1">
                    <p>{{ optional($resposta->membro)->nome ?? '—' }}</p>
                </div>
                <div class="item item-2">
                    <p>{{ optional($resposta->pergunta)->texto ?? 'Pergunta removida' }}</p>
                    <small class="hint">{{ ucfirst(optional($resposta->pergunta)->tipo) }}</small>
                </div>
                <div class="item item-2">
                    <p>{{ $textoResposta ?: '—' }}</p>
                </div>
            </div>
        @empty
            <div class="card">
                <p><i class="bi bi-info-circle"></i> Nenhuma resposta encontrada para os filtros aplicados.</p>
            </div>
        @endforelse

        @if ($respostas instanceof \Illuminate\Pagination\LengthAwarePaginator && $respostas->hasPages())
            <div class="pagination">
                {{ $respostas->links('pagination::default') }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .header-info {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1.5rem;
    }
    .filtro-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }
    .filter-form .form-item {
        display: flex;
        flex-direction: column;
        gap: .5rem;
    }
    .form-actions {
        align-self: flex-end;
        flex-direction: row !important;
        gap: .75rem !important;
    }
    .btn.btn-outline {
        background: transparent;
        border: 1px solid rgba(15, 23, 42, .2);
    }
    .respostas-list .list-item {
        position: relative;
    }
    @media (max-width: 768px) {
        .header-info {
            flex-direction: column;
        }
        .form-actions {
            flex-direction: column !important;
            align-items: stretch;
        }
        .form-actions .btn {
            width: 100%;
        }
        .respostas-list .list-title {
            display: none;
        }
        .respostas-list .list-item {
            display: grid;
            gap: .5rem;
        }
        .respostas-list .item {
            width: 100%;
        }
    }
</style>
@endpush
