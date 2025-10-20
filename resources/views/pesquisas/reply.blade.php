@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')
<div class="container">
    <div class="nao-imprimir">
        <div class="info heading">
            <h1 class="heading-title">{{ $pesquisa->titulo }}</h1>
            @php
                $inicio = optional($pesquisa->data_inicio)?->format('d/m/Y');
                $fim = optional($pesquisa->data_fim)?->format('d/m/Y');
                $hoje = now();
                $statusPeriodo = 'Sem período definido';

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
            @endphp
            <p class="descricao">
                {{ $pesquisa->descricao ?: 'Sem descrição disponível.' }}
            </p>
            <p class="meta">
                <strong>Período:</strong> {{ $inicio ? $inicio : '—' }} @if($fim) até {{ $fim }} @endif |
                <strong>Status:</strong> {{ $statusPeriodo }}
            </p>
            <div class="form-options heading-actions">
                <button onclick="window.location='{{ route('pesquisas.replies.index', ['status' => request('status', 'nao-respondidas')]) }}'" class="btn btn-outline">
                    <i class="bi bi-arrow-left"></i> Voltar
                </button>
            </div>
        </div>
    </div>

    @if (session('msg'))
        <div class="alert success">
            <i class="bi bi-check-circle"></i> {{ session('msg') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert danger">
            <i class="bi bi-exclamation-triangle"></i> Corrija os campos destacados e tente novamente.
        </div>
    @endif

    <form action="{{ route('pesquisas.replies.submit', $pesquisa->id) }}" method="POST" class="reply-form">
        @csrf
        <div class="card perguntas">
            @forelse ($pesquisa->perguntas as $index => $pergunta)
                @php
                    /** @var \App\Models\Resposta|null $resposta */
                    $resposta = $respostas->get($pergunta->id);
                    $oldRadio = old("respostas.{$pergunta->id}.opcao", $resposta?->opcoes->first()?->opcao_id);
                    $oldCheckbox = old("respostas.{$pergunta->id}.opcao", $resposta?->opcoes->pluck('opcao_id')->all() ?? []);
                    $oldTexto = old("respostas.{$pergunta->id}.texto", $resposta?->resposta_texto);
                @endphp
                <div class="pergunta card">
                    <div class="pergunta-header">
                        <div class="pergunta-heading">
                            <span class="pergunta-numero">{{ $index + 1 }}.</span>
                            <h4 class="pergunta-titulo">{{ $pergunta->texto }}</h4>
                        </div>
                        <span class="pergunta-tipo badge">
                            @switch($pergunta->tipo)
                                @case('texto')
                                    Texto livre
                                    @break
                                @case('radio')
                                    Escolha única
                                    @break
                                @case('checkbox')
                                    Múltipla escolha
                                    @break
                            @endswitch
                        </span>
                    </div>
                    <div class="pergunta-body">
                        @if ($pergunta->tipo === 'texto')
                            <div class="form-item">
                                <label for="pergunta-{{ $pergunta->id }}">Resposta</label>
                                <textarea id="pergunta-{{ $pergunta->id }}" name="respostas[{{ $pergunta->id }}][texto]" rows="4" required>{{ $oldTexto }}</textarea>
                                @error("respostas.{$pergunta->id}.texto")
                                    <small class="hint text-error">{{ $message }}</small>
                                @enderror
                            </div>
                        @elseif ($pergunta->tipo === 'radio')
                            <div class="form-item">
                                <label>Escolha uma opção</label>
                                @if ($pergunta->opcoes->isEmpty())
                                    <p class="hint text-error">Esta pergunta ainda não possui opções cadastradas.</p>
                                @else
                                    <div class="option-list">
                                        @foreach ($pergunta->opcoes as $opcao)
                                            <label class="option-item">
                                                <input type="radio"
                                                    name="respostas[{{ $pergunta->id }}][opcao]"
                                                    value="{{ $opcao->id }}"
                                                    @checked($oldRadio == $opcao->id)>
                                                <span>{{ $opcao->texto }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                                @error("respostas.{$pergunta->id}.opcao")
                                    <small class="hint text-error">{{ $message }}</small>
                                @enderror
                            </div>
                        @elseif ($pergunta->tipo === 'checkbox')
                            <div class="form-item">
                                <label>Selecione uma ou mais opções</label>
                                @if ($pergunta->opcoes->isEmpty())
                                    <p class="hint text-error">Esta pergunta ainda não possui opções cadastradas.</p>
                                @else
                                    <div class="option-list">
                                        @foreach ($pergunta->opcoes as $opcao)
                                            <label class="option-item">
                                                <input type="checkbox"
                                                    name="respostas[{{ $pergunta->id }}][opcao][]"
                                                    value="{{ $opcao->id }}"
                                                    @checked(in_array($opcao->id, $oldCheckbox, true))>
                                                <span>{{ $opcao->texto }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                                @error("respostas.{$pergunta->id}.opcao")
                                    <small class="hint text-error">{{ $message }}</small>
                                @enderror
                            </div>
                        @else
                            <p class="hint text-error">Tipo de pergunta não suportado.</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card">
                    <p><i class="bi bi-info-circle"></i> Nenhuma pergunta cadastrada para esta pesquisa no momento.</p>
                </div>
            @endforelse
        </div>

        @if ($pesquisa->perguntas->isNotEmpty())
            <div class="form-options submit-area nao-imprimir center">
                <button type="submit" class="btn">
                    <i class="bi bi-send"></i> Enviar respostas
                </button>
            </div>
        @endif
    </form>
</div>
@endsection

@push('styles')
<style>
    
    .pesquisa-reply .card {
        border-radius: 16px;
        padding: 1.75rem;
    }
    .pesquisa-reply .heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .pesquisa-reply .heading-left {
        flex: 1 1 auto;
    }
    .pesquisa-reply .heading-title {
        margin: 0;
        font-size: 2rem;
    }
    .pesquisa-reply .heading-actions .btn {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding-inline: 1.25rem;
    }
    .pesquisa-reply .btn.btn-outline {
        background: transparent;
        border: 1px solid rgba(15, 23, 42, .15);
    }
    .pesquisa-reply .descricao {
        line-height: 1.6;
        margin-bottom: 1rem;
    }
    .pesquisa-reply .meta {
        font-size: .95rem;
        color: rgba(15, 23, 42, .7);
    }
    .reply-form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .perguntas {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        background: transparent;
        padding: 0;
    }
    .pergunta {
        box-shadow: 0 12px 24px rgba(15, 23, 42, .08);
    }
    .pergunta-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .pergunta-heading {
        display: flex;
        align-items: center;
        gap: .75rem;
        flex-wrap: wrap;
    }
    .pergunta-numero {
        font-size: 1.25rem;
        font-weight: 700;
    }
    .pergunta-titulo {
        margin: 0;
        font-size: 1.15rem;
    }
    .badge {
        background: rgba(15, 23, 42, .08);
        border-radius: 999px;
        padding: .25rem .85rem;
        font-size: .85rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .pergunta-body .form-item {
        display: flex;
        flex-direction: column;
        gap: .75rem;
    }
    .pergunta-body textarea {
        border-radius: 12px;
        padding: 1rem;
        min-height: 140px;
        resize: vertical;
    }
    .option-list {
        display: grid;
        gap: .75rem;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }
    .option-item {
        display: flex;
        align-items: flex-start;
        gap: .5rem;
        padding: .85rem 1rem;
        border: 1px solid rgba(15, 23, 42, .1);
        border-radius: 12px;
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .option-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(15, 23, 42, .12);
    }
    .option-item input {
        margin-top: .2rem;
    }
    .submit-area {
        justify-content: flex-end;
        padding: 1rem 0 2rem;
    }
    .submit-area .btn {
        min-width: 220px;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        justify-content: center;
        padding-inline: 1.75rem;
        padding-block: .9rem;
        font-size: 1rem;
    }
    @media (max-width: 768px) {
        .pesquisa-reply {
            padding-inline: 1rem;
        }
        .pesquisa-reply .heading {
            flex-direction: column;
            align-items: stretch;
        }
        .heading-actions {
            width: 100%;
        }
        .heading-actions .btn {
            width: 100%;
            justify-content: center;
        }
        .option-list {
            grid-template-columns: 1fr;
        }
        .submit-area {
            padding-bottom: 1rem;
        }
        .submit-area .btn {
            width: 100%;
        }
    }
</style>
@endpush
