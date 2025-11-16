@extends('layouts.main')

@section('title', 'Encontros de Células')

@section('content')
@php
    $celulaNome = optional($selectedCelula)->identificacao ?? 'Selecione uma célula';
    $celulaEndereco = optional($selectedCelula)
        ? collect([
            $selectedCelula->endereco,
            $selectedCelula->numero,
            $selectedCelula->bairro,
        ])->filter()->implode(', ')
        : null;

    $diaLegenda = $selectedDateCarbon->translatedFormat('l');
    $diaLegenda = mb_convert_case($diaLegenda, MB_CASE_TITLE, 'UTF-8');
    $dataFormatada = $selectedDateCarbon->format('d/m/Y');

    $encontroStatus = $encontroDoDia?->status;
    $statusLabel = $encontroStatus
        ? ($statusOptions[$encontroStatus] ?? ucfirst($encontroStatus))
        : 'Nenhum encontro registrado';

    $horaEncontro = $encontroDoDia && $encontroDoDia->hora_encontro
        ? \Carbon\Carbon::parse($encontroDoDia->hora_encontro)->format('H:i')
        : '—';

    $totalPresentes = $encontroDoDia?->presentes?->count() ?? 0;
    $quantidadeInformada = $encontroDoDia?->quantidade_presentes ?? 0;

    $presentesColecao = $encontroDoDia?->presentes ?? collect();
    $modalParams = array_filter([
        'celula_id' => $selectedCelulaId,
        'data' => $selectedDate,
    ], static fn ($value) => ! is_null($value));
@endphp

<div class="container">
    <h1>Encontros das Células</h1>

    <div class="info">
        <h3>Cadastro de encontros</h3>

        <div class="search-panel encontros-filtros">
            <form method="get" action="{{ route('celulas.encontros') }}" class="search-panel-item encontros-filtros__celula" id="encontros-filtro-celula">
                <label for="encontros-celula">Célula</label>
                <select name="celula_id" id="encontros-celula" @disabled($celulas->isEmpty()) onchange="this.form.submit()">
                    @forelse ($celulas as $celula)
                        <option value="{{ $celula->id }}" @selected($celula->id === $selectedCelulaId)>{{ $celula->identificacao }}</option>
                    @empty
                        <option value="">Nenhuma célula cadastrada</option>
                    @endforelse
                </select>
                <input type="hidden" name="data" value="{{ $selectedDate }}">
            </form>

            <form method="get" action="{{ route('celulas.encontros') }}" class="search-panel-item encontros-filtros__data" id="encontros-data-form">
                <input type="hidden" name="celula_id" value="{{ $selectedCelulaId }}">
                <label for="encontros-data">Data</label>
                <input type="date" name="data" id="encontros-data" value="{{ $selectedDate }}" onchange="this.form.submit()">
            </form>

            <div class="search-panel-item encontros-filtros__acoes">
                <button type="button" class="btn" onclick="window.location.href='{{ route('celulas.painel') }}'">
                    <i class="bi bi-arrow-return-left"></i> Voltar
                </button>
            </div>
        </div>

        <div class="painel-encontros-cards">
            <div class="painel-card neutral">
                <span class="label">Data selecionada</span>
                <strong>{{ $dataFormatada }}</strong>
                <small>{{ $diaLegenda }}</small>
            </div>
            <div class="painel-card neutral">
                <span class="label">Célula</span>
                <strong>{{ $celulaNome }}</strong>
                <small>{{ $celulaEndereco ?? 'Endereço não informado' }}</small>
            </div>
            <div class="painel-card neutral">
                <span class="label">Status do encontro</span>
                <strong>{{ $statusLabel }}</strong>
                @if ($encontroDoDia)
                    <small>{{ $horaEncontro }} · {{ $quantidadeInformada }} informados</small>
                @else
                    <small>Registre a presença para esta célula</small>
                @endif
            </div>

            @if ($encontroDoDia)
                <div class="painel-card painel-card-detalhes">
                    <span class="label">Detalhes gerais</span>
                    <div class="painel-card-detalhes-grid">
                        <div>
                            <strong>{{ optional($encontroDoDia->preletor)->nome ?? '—' }}</strong>
                            <small>Responsável</small>
                        </div>
                        <div>
                            <strong>{{ $horaEncontro }}</strong>
                            <small>Horário</small>
                        </div>
                        <div>
                            <strong>{{ $encontroDoDia->tema ?: '—' }}</strong>
                            <small>Tema</small>
                        </div>
                        <div>
                            <strong>{{ $totalPresentes }}</strong>
                            <small>Presentes registrados</small>
                        </div>
                    </div>

                    @if ($encontroDoDia->observacoes)
                        <div class="painel-card-detalhes-observacoes">
                            <small>Observações</small>
                            <p>{{ $encontroDoDia->observacoes }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="info">
        <h3>Registro de encontro</h3>
        <form class="card encontros-form" data-encontro-form="principal" onsubmit="return false;">
            <div class="form-control">
                <div class="form-item">
                    <label for="encontros-status">Status</label>
                    <select id="encontros-status" name="status" @disabled(!$selectedCelulaId)>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected($encontroDoDia && $encontroDoDia->status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-item">
                    <label for="encontros-hora">Hora do encontro</label>
                    <input type="time" id="encontros-hora" name="hora_encontro" value="{{ $encontroDoDia?->hora_encontro }}" @disabled(!$selectedCelulaId)>
                </div>

                <div class="form-item">
                    <label for="encontros-tema">Tema</label>
                    <input type="text" id="encontros-tema" name="tema" value="{{ $encontroDoDia?->tema }}" placeholder="Tema do encontro" @disabled(!$selectedCelulaId)>
                </div>

                <div class="form-item">
                    <label for="encontros-observacoes">Observações</label>
                    <textarea id="encontros-observacoes" name="observacoes" rows="3" placeholder="Informações adicionais sobre o encontro" @disabled(!$selectedCelulaId)>{{ $encontroDoDia?->observacoes }}</textarea>
                </div>

                <div class="form-options">
                    <button class="btn" type="submit" @disabled(!$selectedCelulaId)>
                        <i class="bi bi-save"></i> Salvar encontro
                    </button>
                    <button class="btn btn-outline" type="reset">
                        <i class="bi bi-arrow-counterclockwise"></i> Limpar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="info">
        <h3>Presentes em {{ $dataFormatada }}</h3>
        <div class="search-panel encontros-presentes-actions">
            <div class="search-panel-item">
                <button type="button" class="btn" id="encontros-btn-adicionar-presente" data-modal-url="{{ route('celulas.encontros.presentes.modal', $modalParams) }}" @disabled(!$selectedCelulaId)>
                    <i class="bi bi-person-plus"></i> Adicionar presente
                </button>
            </div>
            <div class="search-panel-item">
                <div class="stat-block">
                    <span class="stat-label">Presentes registrados</span>
                    <strong>{{ $totalPresentes }}</strong>
                </div>
                <div class="stat-block">
                    <span class="stat-label">Quantidade informada</span>
                    <strong>{{ $quantidadeInformada }}</strong>
                </div>
            </div>
        </div>

        @if (! $selectedCelulaId)
            <div class="card">
                <p><i class="bi bi-info-circle"></i> Escolha uma célula para registrar o encontro e seus participantes.</p>
            </div>
        @elseif (! $encontroDoDia)
            <div class="card">
                <p><i class="bi bi-calendar-x"></i> Ainda não há encontro registrado para {{ $dataFormatada }} nesta célula.</p>
            </div>
        @elseif ($presentesColecao->isEmpty())
            <div class="card">
                <p><i class="bi bi-people"></i> Nenhum presente adicionado para este encontro.</p>
            </div>
        @else
            <div class="list encontros-presentes-list">
                <div class="list-title">
                    <div class="item item-15"><b>Nome</b></div>
                    <div class="item item-1"><b>Tipo</b></div>
                    <div class="item item-1 nao-imprimir"><b>Ações</b></div>
                </div>
                <div id="encontros-presentes-itens">
                    @foreach ($presentesColecao as $presenca)
                        <div class="list-item" data-presenca-id="{{ $presenca->id }}">
                            <div class="item item-15">
                                <p>{{ optional($presenca->membro)->nome ?? $presenca->nome ?? 'Participante sem identificação' }}</p>
                            </div>
                            <div class="item item-1">
                                <p>{{ optional($presenca->membro)->id ? 'Membro' : 'Visitante' }}</p>
                            </div>
                            <div class="item item-1 nao-imprimir">
                                <button type="button" class="taggable-action" title="Remover">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="info">
        <h3>Histórico recente</h3>
        @if ($historicoEncontros->isEmpty())
            <div class="card">
                <p><i class="bi bi-info-circle"></i> Nenhum encontro registrado para esta célula.</p>
            </div>
        @else
            <div class="list encontros-historico">
                <div class="list-title">
                    <div class="item item-1"><b>Data</b></div>
                    <div class="item item-1"><b>Status</b></div>
                    <div class="item item-1"><b>Presentes</b></div>
                </div>
                <div>
                    @foreach ($historicoEncontros as $historico)
                        @php
                            $historicoData = \Carbon\Carbon::parse($historico->data_encontro)->format('d/m/Y');
                            $historicoHora = $historico->hora_encontro ? \Carbon\Carbon::parse($historico->hora_encontro)->format('H:i') : '—';
                            $historicoPresentes = $historico->presentes->count();
                            $historicoStatus = $statusOptions[$historico->status] ?? ucfirst($historico->status);
                        @endphp
                        <div class="list-item">
                            <div class="item item-1">
                                <p>{{ $historicoData }} · {{ $historicoHora }}</p>
                            </div>
                            <div class="item item-1">
                                <p>{{ $historicoStatus }}</p>
                            </div>
                            <div class="item item-1">
                                <p>{{ $historicoPresentes }} / {{ $historico->quantidade_presentes }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const modalButton = document.getElementById('encontros-btn-adicionar-presente');

        if (modalButton) {
            modalButton.addEventListener('click', function () {
                const url = this.dataset.modalUrl;

                if (!url) {
                    console.warn('URL do modal de presentes não configurada.');
                    return;
                }

                abrirJanelaModal(url);
            });
        }
    })();
</script>
@endpush

<style>
    .painel-encontros-cards {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        margin-bottom: 1.5rem;
    }

    .painel-card {
        border-radius: 18px;
        padding: 18px 20px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 12px 24px rgba(17, 24, 39, 0.08);
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }

    .painel-card.neutral {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(0, 0, 0, 0.06);
    }

    .painel-card .label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        opacity: 0.7;
    }

    .painel-card strong {
        font-size: 1.75rem;
        font-weight: 600;
        line-height: 1.2;
    }

    .painel-card small {
        font-size: 0.85rem;
        opacity: 0.8;
    }

    .painel-card-detalhes {
        grid-column: 1 / -1;
        gap: 1rem;
    }

    .painel-card-detalhes-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    }

    .painel-card-detalhes-grid strong {
        font-size: 1.35rem;
    }

    .painel-card-detalhes-grid small {
        opacity: 0.7;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .painel-card-detalhes-observacoes {
        padding-top: 0.5rem;
        border-top: 1px solid rgba(0, 0, 0, 0.08);
    }

    .painel-card-detalhes-observacoes p {
        margin: 0.35rem 0 0;
        line-height: 1.4;
    }

    .encontros-filtros {
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
        margin-bottom: 15px;
    }

    .encontros-filtros__acoes {
        margin-left: auto;
    }

    .encontros-presentes-actions {
        align-items: center;
        gap: 1rem;
        margin-bottom: 15px;
    }

    .stat-block {
        display: flex;
        flex-direction: column;
        text-align: center;
        padding: 0.65rem 1.1rem;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(0, 0, 0, 0.05);
        min-width: 140px;
    }

    .stat-label {
        font-size: 0.75rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        opacity: 0.7;
        margin-bottom: 0.15rem;
    }

    .stat-block strong {
        font-size: 1.4rem;
    }

    .encontros-presentes-list .list-item {
        align-items: center;
    }

    .encontros-historico .list-item {
        cursor: default;
    }

    .btn.btn-outline {
        background: transparent;
        border: 1px solid currentColor;
    }

    .encontros-filtros__celula select,
    .encontros-filtros__data input[type="date"] {
        padding: 10px 14px;
        border-radius: 14px;
        border: 1px solid rgba(24, 24, 24, 0.18);
        background: rgba(255, 255, 255, 0.08);
        color: var(--text-font);
        min-height: 46px;
        line-height: 1.45;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .encontros-filtros__celula select:focus,
    .encontros-filtros__data input[type="date"]:focus {
        border-color: var(--secondary-color);
        background: rgba(255, 255, 255, 0.12);
        box-shadow: 0 0 0 3px rgba(100, 73, 162, 0.25);
        outline: none;
    }

    .encontros-filtros__celula select option {
        color: var(--text-font);
        background: rgba(0, 0, 0, 0.8);
    }
</style>
@endsection
