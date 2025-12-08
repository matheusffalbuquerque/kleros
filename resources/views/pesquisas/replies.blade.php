@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')
<div class="container">
    <div class="nao-imprimir">
        <h1>Pesquisas Disponíveis</h1>
        <div class="info">
            <h3>Responda as pesquisas da congregação</h3>
            <div class="form-options filter-toggle">
                @php
                    $statusAtual = $status ?? 'nao-respondidas';
                @endphp
                <button class="btn {{ $statusAtual === 'nao-respondidas' ? 'active' : '' }}" onclick="window.location='{{ route('pesquisas.replies.index', ['status' => 'nao-respondidas']) }}'">
                    <i class="bi bi-question-circle"></i> Não respondidas
                </button>
                <button class="btn {{ $statusAtual === 'respondidas' ? 'active' : '' }}" onclick="window.location='{{ route('pesquisas.replies.index', ['status' => 'respondidas']) }}'">
                    <i class="bi bi-check-circle"></i> Respondidas
                </button>
            </div>
        </div>
    </div>

    <div id="list" class="list">
        <div class="list-title">
            <div class="item-2">
                <b>Título</b>
            </div>
            <div class="item-1">
                <b>Período</b>
            </div>
            <div class="item-1">
                <b>Status</b>
            </div>
            <div class="item-1">
                <b>Participação</b>
            </div>
        </div>
        <div id="content">
            @forelse ($pesquisas as $pesquisa)
                @php
                    $inicio = optional($pesquisa->data_inicio)?->format('d/m/Y');
                    $fim = optional($pesquisa->data_fim)?->format('d/m/Y');
                    $hoje = now();
                    $statusPeriodo = 'Sem período';

                    if ($pesquisa->data_inicio && $pesquisa->data_fim) {
                        if ($hoje->lt($pesquisa->data_inicio)) {
                            $statusPeriodo = 'Agendada';
                        } elseif ($hoje->between($pesquisa->data_inicio, $pesquisa->data_fim)) {
                            $statusPeriodo = 'Em andamento';
                        } else {
                            $statusPeriodo = 'Encerrada';
                        }
                    } elseif ($pesquisa->data_inicio) {
                        $statusPeriodo = $hoje->lt($pesquisa->data_inicio) ? 'Agendada' : 'Em andamento';
                    }

                    $respondida = ($pesquisa->respostas_do_membro ?? 0) > 0;
                @endphp
                <div class="list-item" title="{{ $pesquisa->descricao }}" onclick="window.location='{{ route('pesquisas.replies.show', ['pesquisa' => $pesquisa->id, 'status' => $statusAtual]) }}'">
                    <div class="item item-2">
                        <p class="with-description">
                            <span class="title"><i class="bi bi-bar-chart"></i> {{ $pesquisa->titulo }}</span>
                            @if($pesquisa->descricao)
                                <small>{{ \Illuminate\Support\Str::limit($pesquisa->descricao, 120) }}</small>
                            @endif
                        </p>
                    </div>
                    <div class="item item-1">
                        <p>{{ $inicio ? $inicio : '—' }} @if($fim) até {{ $fim }} @endif</p>
                    </div>
                    <div class="item item-1">
                        <p>{{ $statusPeriodo }}</p>
                    </div>
                    <div class="item item-1">
                        <p class="{{ $respondida ? 'text-success' : 'text-danger' }}">
                            {{ $respondida ? 'Respondida' : 'Pendente' }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="card">
                    <p><i class="bi bi-info-circle"></i> Nenhuma pesquisa encontrada para este filtro.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
