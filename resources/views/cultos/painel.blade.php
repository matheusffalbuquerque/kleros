@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . config('app.name'))

@section('content')
@php
    $dataFormatada = \Carbon\Carbon::parse($selectedDate)->format('d/m/Y');
    $horarioFormatado = $horarioInicio ? $horarioInicio : '—';

    $cards = $dashboardCards ?? [];
    $todayLabel = data_get($cards, 'today.label', 'Hoje');
    $serviceCard = $cards['service'] ?? [];
    $serviceLabel = data_get($serviceCard, 'label', 'Culto');
    $serviceUnknownPreacher = data_get($serviceCard, 'unknown_preacher', 'Preletor não informado');
    $serviceNoEvent = data_get($serviceCard, 'no_event', 'Nenhum evento associado');
    $serviceNoService = data_get($serviceCard, 'no_service', 'Nenhum culto registrado');
    $serviceCta = data_get($serviceCard, 'cta', 'Agendar culto');
    $selectedDateLabel = $selectedDateFull ?? $dataFormatada;
    $diaLegenda = $dashboardDayName ?? \Carbon\Carbon::parse($selectedDate)->translatedFormat('l');
    $diaLegenda = mb_convert_case($diaLegenda, MB_CASE_TITLE, 'UTF-8');
@endphp

<div class="container">
    <h1>Painel de Culto</h1>

    <div class="info">
        <h3>Informações do culto</h3>

        <div class="painel-culto-cards">
            <div class="painel-card neutral">
                <span class="label">{{ $todayLabel }}</span>
                <strong>{{ $selectedDateLabel }}</strong>
                <small>{{ $diaLegenda }}</small>
            </div>
            <div class="painel-card neutral">
                <span class="label">{{ $serviceLabel }}</span>
                @if ($culto)
                    <strong>{{ $culto->preletor ?? $serviceUnknownPreacher }}</strong>
                    <small>
                        @if ($culto->evento_id && $culto->evento)
                            {{ $culto->evento->titulo }}
                        @else
                            {{ $serviceNoEvent }}
                        @endif
                    </small>
                @else
                    <strong>{{ $serviceNoService }}</strong>
                    <small>
                        <span class="link-standard" onclick="abrirJanelaModal('{{ route('cultos.form_criar') }}')">
                            {{ $serviceCta }}
                        </span>
                    </small>
                @endif
            </div>

            @if ($culto)
                <div class="painel-card painel-card-detalhes">
                    <span class="label">Detalhes</span>
                    <div class="painel-card-detalhes-grid">
                        <div>
                            <strong>{{ $culto->tema_sermao ?: '—' }}</strong>
                            <small>Tema do sermão</small>
                        </div>
                        <div>
                            <strong>{{ $culto->texto_base ?: '—' }}</strong>
                            <small>Texto-base</small>
                        </div>
                        <div>
                            <strong>{{ $culto->quant_adultos ?? 0 }}</strong>
                            <small>Adultos</small>
                        </div>
                        <div>
                            <strong>{{ $culto->quant_criancas ?? 0 }}</strong>
                            <small>Crianças</small>
                        </div>
                        <div>
                            <strong>{{ $culto->quant_visitantes ?? 0 }}</strong>
                            <small>Visitantes</small>
                        </div>
                    </div>
                    @if ($culto->observacoes)
                        <div class="painel-card-detalhes-observacoes">
                            <small>Observações</small>
                            <p>{{ $culto->observacoes }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="search-panel painel-culto-search painel-culto-meta-actions">
            <form action="{{ route('cultos.painel') }}" method="get" class="search-panel-item painel-culto-meta-date" id="painel-culto-data-form">
                <label for="culto-data">Data</label>
                <input type="date" id="culto-data" name="data" value="{{ $selectedDate }}">
            </form>
            <div class="search-panel-item">
                <button class="btn" type="button" id="painel-btn-editar-culto"
                    @if(!$culto) data-fallback="true" @endif>
                    <i class="bi bi-pencil-square"></i> Editar culto
                </button>
            </div>
            <div class="search-panel-item">
                <button class="btn" type="button" onclick="window.location.href='{{ route('cultos.agenda') }}'">
                    <i class="bi bi-calendar3"></i> Cultos previstos
                </button>
            </div>
            <div class="search-panel-item">
                <button class="btn" type="button" onclick="window.location.href='{{ route('cultos.historico') }}'">
                    <i class="bi bi-clock-history"></i> Histórico
                </button>
            </div>
        </div>
    </div>

    <div class="info">
        <h3>Controle do culto de {{ $dataFormatada }}</h3>

        <div class="card painel-culto-resumo">
            @if ($culto)
                <p><i class="bi bi-person-circle"></i> Preletor: <strong>{{ $culto->preletor }}</strong></p>
                <p><i class="bi bi-clock"></i> Início: <strong>{{ $horarioFormatado }}</strong></p>
                <p><i class="bi bi-book"></i> Tema: <strong>{{ $culto->tema_sermao ?: '—' }}</strong></p>
                <p><i class="bi bi-journal-text"></i> Evento: <strong>{{ optional($culto->evento)->titulo ?? 'Nenhum' }}</strong></p>
            @else
                <p><i class="bi bi-info-circle"></i> Nenhum culto registrado para este dia. Utilize <strong>Editar culto</strong> para cadastrar os dados.</p>
            @endif
        </div>

        <div class="search-panel painel-culto-search">
            <div class="search-panel-item painel-culto-search__input">
                <select id="painel-visitante-select"
                    class="painel-select2"
                    data-placeholder="Buscar visitante por nome ou telefone">
                    <option value=""></option>
                </select>
                <button class="btn" type="button" id="painel-btn-registrar-visitante">
                    <i class="bi bi-person-check"></i> Registrar visitante
                </button>
            </div>
            <div class="search-panel-item">
                <button class="btn" type="button" id="painel-btn-novo-visitante">
                    <i class="bi bi-person-plus"></i> Novo visitante
                </button>
            </div>
        </div>

        <div class="painel-culto-feedback card" id="painel-visitante-feedback" hidden></div>
    </div>

    <div class="info">
        <h3>Visitantes registrados em {{ $dataFormatada }}</h3>
        <div id="painel-visitantes-registrados-wrapper">
            @if ($visitantesDia->isEmpty())
            <div class="card" id="painel-visitantes-registrados-empty" @if ($visitantesDia->isNotEmpty()) hidden @endif>
                <p><i class="bi bi-exclamation-circle"></i> Nenhum visitante registrado para este culto.</p>
            </div>
            @else
            <div class="list" id="painel-visitantes-registrados-list" @if ($visitantesDia->isEmpty()) hidden @endif>
            <div class="list-title">
                <div class="item item-15"><b>Nome</b></div>
                <div class="item item-1"><b>Telefone</b></div>
                <div class="item item-15"><b>Situação</b></div>
                <div class="item item-05"><b>Visitas</b></div>
            </div>
            <div id="painel-visitantes-registrados-itens">
                @foreach ($visitantesDia as $visitante)
                    <div class="list-item taggable-item" data-visitante-id="{{ $visitante->id }}">
                        <div class="item item-15" data-visitante-field="nome">
                            <p><i class="bi bi-person-raised-hand"></i> {{ $visitante->nome }}</p>
                        </div>
                        <div class="item item-1" data-visitante-field="telefone">
                            <p>{{ $visitante->telefone }}</p>
                        </div>
                        <div class="item item-15" data-visitante-field="situacao">
                            <p>{{ optional($visitante->sit_visitante)->titulo ?? 'Não informado' }}</p>
                        </div>
                        <div class="item item-05" data-visitante-field="visitas">
                            <p>{{ $visitante->visit_count ?? 1 }}</p>
                        </div>
                        <div class="taggable-actions nao-imprimir">
                                <div class="taggable">
                                    <button class="taggable-action" type="button"
                                        data-role="editar-visitante"
                                        title="Editar visitante"
                                        data-edit-url="{{ route('visitantes.form_editar', ['id' => $visitante->id, 'return_to' => $panelUrl]) }}"
                                        onclick="abrirJanelaModal(this.dataset.editUrl)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('visitantes.destroy', $visitante->id) }}"
                                    method="POST"
                                    class="painel-remover-visitante"
                                    data-visitante-id="{{ $visitante->id }}"
                                    data-visitante-nome="{{ $visitante->nome }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="taggable-action" type="submit" title="Remover visitante" data-role="remover-visitante">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    @if (module_enabled('recados'))
        <a href="{{ url('/recados/adicionar') }}" class="float-btn nao-imprimir" title="Recados">
            <i class="bi bi-chat-left-dots"></i>
        </a>
    @endif

</div>

<style>
    .painel-culto-cards {
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
        font-size: 1.4rem;
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

    .painel-culto-search {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
    }

    .painel-culto-search .search-panel-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .painel-culto-meta-date {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0;
        margin: 0;
    }

    .painel-culto-meta-date label {
        font-size: 0.85rem;
        font-weight: 600;
    }

    .painel-culto-meta-date input[type="date"] {
        padding: 0.45rem 0.65rem;
        border-radius: 6px;
        background-color: rgba(255, 255, 255, 0.08);
    }

    .painel-culto-search__input {
        flex: 1 1 320px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .painel-culto-search__input .select2-container {
        flex: 1;
    }

    .painel-culto-search__input .select2-selection--single {
        height: 40px;
        display: flex;
        align-items: center;
        border-radius: 6px;
        border: 1px solid var(--border-color, #d9d9d9);
    }

    .painel-culto-search__input .select2-selection__rendered {
        line-height: 38px;
    }

    .painel-culto-search__input .select2-selection__arrow {
        height: 38px;
    }

    .painel-culto-feedback {
        margin-top: 1rem;
        padding: 0.9rem 1rem;
        border-left: 4px solid transparent;
    }

    .painel-culto-feedback[hidden] {
        display: none !important;
    }

    .painel-culto-feedback.info {
        background: #eef5ff;
        border-left-color: #3d8bfd;
    }

    .painel-culto-feedback.success {
        background: #e9f8ec;
        border-left-color: #23914a;
    }

    .painel-culto-feedback.warning {
        background: #fff6e6;
        border-left-color: #f0ad4e;
    }

    .painel-culto-feedback.error {
        background: #fde8e8;
        border-left-color: #dc3545;
    }

    .painel-select-option .option-title {
        font-weight: 600;
        margin-bottom: 2px;
    }

    .painel-select-option .option-subtitle {
        font-size: 0.82rem;
        opacity: 0.75;
    }

    .item-05 {
        width: 10%;
    }

    .taggable-item {
        gap: 1rem;
        align-items: center;
    }

    .taggable-actions {
        display: flex;
        gap: 0.4rem;
        align-items: center;
    }

    .taggable {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.2rem 0.4rem;
        border-radius: 999px;
        background: rgba(0, 0, 0, 0.05);
    }

    .taggable-actions form {
        margin: 0;
    }

    .taggable-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: none;
        background: transparent;
        color: inherit;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .taggable-action:hover {
        background: rgba(0, 0, 0, 0.12);
    }

    .taggable-action i {
        font-size: 1rem;
    }

    .painel-culto-resumo p {
        margin: 0.2rem 0;
    }

    .btn-small {
        padding: 0.35rem 0.75rem;
        font-size: 0.85rem;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #eef5ff;
        color: #0d6efd;
    }

    .badge-success {
        background: #e9f8ec;
        color: #1f7a3b;
    }

    .painel-visitante-highlight {
        animation: painelHighlight 1.5s ease;
    }

    @keyframes painelHighlight {
        0% { background-color: rgba(61, 139, 253, 0.2); }
        100% { background-color: transparent; }
    }

    @media (max-width: 600px) {
        .painel-culto-search {
            gap: 8px !important;
            padding: 12px !important;
        }

        .painel-culto-search .search-panel-item {
            gap: 6px !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .painel-culto-search__input {
            gap: 6px !important;
            margin: 0 !important;
            flex: none !important;
        }

        /* Remove todos os espaçamentos entre filhos diretos */
        .painel-culto-search > .search-panel-item {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        .painel-culto-search .select2-container {
            margin: 0 !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dateForm = document.getElementById('painel-culto-data-form');
        const dateInput = document.getElementById('culto-data');
        const registrarButton = document.getElementById('painel-btn-registrar-visitante');
        const novoVisitanteButton = document.getElementById('painel-btn-novo-visitante');
        const editarCultoButton = document.getElementById('painel-btn-editar-culto');
        const feedbackBox = document.getElementById('painel-visitante-feedback');
        const registradosList = document.getElementById('painel-visitantes-registrados-list');
        const registradosItens = document.getElementById('painel-visitantes-registrados-itens');
        const registradosEmpty = document.getElementById('painel-visitantes-registrados-empty');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const registrarPresencaUrl = @json(route('visitantes.registrar_presenca'));
        const quickSearchRoute = @json(route('visitantes.quick_search'));
        const novoVisitanteUrl = @json(route('visitantes.form_criar', ['return_to' => $panelUrl]));
        const editarCultoUrl = @json($culto ? route('cultos.form_editar', $culto->id) : route('cultos.form_criar'));
        const panelUrl = @json($panelUrl);
        const selectedDate = @json($selectedDate);

        let selectedVisitante = null;
        let $visitanteSelect = null;
        let suppressClearFeedback = false;

        if (dateForm && dateInput) {
            dateInput.addEventListener('change', () => dateForm.submit());
        }

        function showFeedback(message, type = 'info') {
            if (!feedbackBox) {
                return;
            }
            feedbackBox.textContent = message;
            feedbackBox.hidden = false;
            feedbackBox.classList.remove('info', 'success', 'warning', 'error');
            feedbackBox.classList.add(type);
        }

        function clearFeedback() {
            if (!feedbackBox) {
                return;
            }
            feedbackBox.hidden = true;
            feedbackBox.textContent = '';
            feedbackBox.classList.remove('info', 'success', 'warning', 'error');
        }

        function removeVisitanteFromList(visitanteId) {
            if (!registradosItens) {
                return;
            }

            const item = visitanteId
                ? registradosItens.querySelector(`[data-visitante-id="${visitanteId}"]`)
                : null;

            if (item) {
                item.remove();
            }

            const aindaPossuiItens = registradosItens.querySelector('.list-item');

            if (!aindaPossuiItens) {
                if (registradosList) {
                    registradosList.hidden = true;
                }
                if (registradosEmpty) {
                    registradosEmpty.hidden = false;
                }
            }
        }

        async function executarExclusaoAjax(form, visitanteId) {
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: form.getAttribute('method') || 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData,
                });

                if (!response.ok) {
                    throw new Error('Request failed');
                }

                const payload = await response.json().catch(() => ({}));
                removeVisitanteFromList(visitanteId);
                showFeedback(payload.message || 'Visitante removido do culto.', 'success');
            } catch (error) {
                console.error(error);
                showFeedback('Não foi possível remover o visitante. Tente novamente.', 'error');
            }
        }

        function attachDeleteHandler(form) {
            if (!form || form.dataset.deleteHandlerBound === 'true') {
                return;
            }

            form.dataset.deleteHandlerBound = 'true';

            form.addEventListener('submit', (event) => {
                event.preventDefault();

                const visitanteId = form.dataset.visitanteId;
                const visitanteNome = form.dataset.visitanteNome || 'este visitante';
                const mensagem = `Deseja remover ${visitanteNome}?`;

                const confirmar = typeof confirmarAcao === 'function'
                    ? confirmarAcao(mensagem)
                    : Promise.resolve(window.confirm(mensagem));

                confirmar.then((confirmed) => {
                    if (!confirmed) {
                        return;
                    }

                    executarExclusaoAjax(form, visitanteId);
                });
            });
        }

        function templateOption(item) {
            if (!item.id) {
                return item.text;
            }

            const data = item.data || {};
            const container = document.createElement('div');
            container.className = 'painel-select-option';

            const title = document.createElement('div');
            title.className = 'option-title';
            title.textContent = item.text;
            container.appendChild(title);

            const subtitlePieces = [];
            if (data.telefone) {
                subtitlePieces.push(data.telefone);
            }
            if (data.situacao) {
                subtitlePieces.push(data.situacao);
            }
            if (Number.isFinite(data.visitas) || (data.visitas && !Number.isNaN(parseInt(data.visitas, 10)))) {
                subtitlePieces.push(`${data.visitas} visitas`);
            }
            if (data.already_registered) {
                subtitlePieces.push('Já registrado');
            }

            if (subtitlePieces.length) {
                const subtitle = document.createElement('div');
                subtitle.className = 'option-subtitle';
                subtitle.textContent = subtitlePieces.join(' • ');
                container.appendChild(subtitle);
            }

            return container;
        }

        if (window.jQuery && window.jQuery.fn.select2) {
            const elementoSelect = document.getElementById('painel-visitante-select');

            if (elementoSelect) {
                $visitanteSelect = window.jQuery(elementoSelect);
                const placeholder = $visitanteSelect.data('placeholder') || 'Buscar visitante';

                $visitanteSelect.select2({
                    placeholder,
                    allowClear: true,
                    width: 'resolve',
                    ajax: {
                        url: quickSearchRoute,
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            term: params.term || '',
                            data: dateInput ? dateInput.value : selectedDate,
                        }),
                        processResults: data => ({
                            results: (data.results || []).map(item => ({
                                id: item.id,
                                text: item.nome,
                                data: item,
                                disabled: Boolean(item.already_registered),
                            })),
                        }),
                        cache: true,
                    },
                    templateResult: templateOption,
                    templateSelection: item => item.text || item.id || placeholder,
                    escapeMarkup: markup => markup,
                });

                $visitanteSelect.on('select2:select', event => {
                    selectedVisitante = event.params.data.data || null;
                    if (selectedVisitante && selectedVisitante.already_registered) {
                        showFeedback('Este visitante já está registrado para o culto.', 'info');
                    } else {
                        showFeedback('Visitante selecionado. Clique em registrar para adicioná-lo ao culto.', 'info');
                    }
                });

                $visitanteSelect.on('select2:clear', () => {
                    selectedVisitante = null;
                    if (suppressClearFeedback) {
                        suppressClearFeedback = false;
                        return;
                    }
                    clearFeedback();
                });
            }
        }

        document.querySelectorAll('.painel-remover-visitante').forEach(attachDeleteHandler);

        function createRegistradoItem(visitante) {
            const linha = document.createElement('div');
            linha.className = 'list-item taggable-item painel-visitante-highlight';
            linha.dataset.visitanteId = visitante.id;

            const nomeCol = document.createElement('div');
            nomeCol.className = 'item item-15';
            nomeCol.dataset.visitanteField = 'nome';
            nomeCol.innerHTML = `<p><i class="bi bi-person-raised-hand"></i> ${visitante.nome}</p>`;

            const telefoneCol = document.createElement('div');
            telefoneCol.className = 'item item-1';
            telefoneCol.dataset.visitanteField = 'telefone';
            telefoneCol.innerHTML = `<p>${visitante.telefone ?? '—'}</p>`;

            const situacaoCol = document.createElement('div');
            situacaoCol.className = 'item item-15';
            situacaoCol.dataset.visitanteField = 'situacao';
            situacaoCol.innerHTML = `<p>${visitante.situacao ?? 'Não informado'}</p>`;

            const visitasCol = document.createElement('div');
            visitasCol.className = 'item item-05';
            visitasCol.dataset.visitanteField = 'visitas';
            const totalVisitas = visitante.visitas ?? visitante.visit_count ?? 1;
            visitasCol.innerHTML = `<p>${totalVisitas}</p>`;

            const actionsWrapper = document.createElement('div');
            actionsWrapper.className = 'taggable-actions nao-imprimir';

            const taggable = document.createElement('div');
            taggable.className = 'taggable';

            const editButton = document.createElement('button');
            editButton.type = 'button';
            editButton.className = 'taggable-action';
            editButton.dataset.role = 'editar-visitante';
            editButton.title = 'Editar visitante';
            editButton.innerHTML = '<i class="bi bi-pencil"></i>';
            let editUrl = visitante.edit_url;
            if (panelUrl) {
                const urlObj = new URL(editUrl, window.location.origin);
                urlObj.searchParams.set('return_to', panelUrl);
                editUrl = urlObj.pathname + urlObj.search;
            }
            editButton.onclick = () => abrirJanelaModal(editUrl);
            editButton.dataset.editUrl = editUrl;
            taggable.appendChild(editButton);

            if (visitante.destroy_url) {
                const deleteForm = document.createElement('form');
                deleteForm.action = visitante.destroy_url;
                deleteForm.method = 'POST';
                deleteForm.className = 'painel-remover-visitante';
                deleteForm.dataset.visitanteId = visitante.id;
                deleteForm.dataset.visitanteNome = visitante.nome;

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = csrfToken;
                deleteForm.appendChild(tokenInput);

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                deleteForm.appendChild(methodInput);

                const deleteButton = document.createElement('button');
                deleteButton.type = 'submit';
                deleteButton.className = 'taggable-action';
                deleteButton.dataset.role = 'remover-visitante';
                deleteButton.title = 'Remover visitante';
                deleteButton.innerHTML = '<i class="bi bi-trash"></i>';
                deleteForm.appendChild(deleteButton);

                taggable.appendChild(deleteForm);
                attachDeleteHandler(deleteForm);
            }

            actionsWrapper.appendChild(taggable);

            linha.appendChild(nomeCol);
            linha.appendChild(telefoneCol);
            linha.appendChild(situacaoCol);
            linha.appendChild(visitasCol);
            linha.appendChild(actionsWrapper);

            return linha;
        }

        function addRegistrado(visitante, highlightExisting = false) {
            if (!visitante || !visitante.id) {
                return;
            }

            if (!registradosItens) {
                return;
            }

            const existente = registradosItens.querySelector(`[data-visitante-id="${visitante.id}"]`);

            if (existente) {
                if (highlightExisting) {
                    existente.classList.remove('painel-visitante-highlight');
                    void existente.offsetWidth;
                    existente.classList.add('painel-visitante-highlight');
                }
                return;
            }

            const novoItem = createRegistradoItem(visitante);
            registradosItens.prepend(novoItem);

            if (registradosEmpty) {
                registradosEmpty.hidden = true;
            }

            if (registradosList) {
                registradosList.hidden = false;
            }
        }

        function updateVisitanteInList(visitante) {
            if (!visitante || !visitante.id || !registradosItens) {
                return;
            }

            let item = registradosItens.querySelector(`[data-visitante-id="${visitante.id}"]`);

            if (!item) {
                addRegistrado(visitante, false);
                return;
            }

            const nomeCol = item.querySelector('[data-visitante-field="nome"] p');
            if (nomeCol) {
                nomeCol.innerHTML = `<i class="bi bi-person-raised-hand"></i> ${visitante.nome}`;
            }

            const telefoneCol = item.querySelector('[data-visitante-field="telefone"] p');
            if (telefoneCol) {
                telefoneCol.textContent = visitante.telefone ?? '—';
            }

            const situacaoCol = item.querySelector('[data-visitante-field="situacao"] p');
            if (situacaoCol) {
                situacaoCol.textContent = visitante.situacao ?? 'Não informado';
            }

            const visitasCol = item.querySelector('[data-visitante-field="visitas"] p');
            if (visitasCol) {
                const totalVisitas = visitante.visitas ?? visitante.visit_count ?? visitasCol.textContent;
                visitasCol.textContent = totalVisitas;
            }

            const editButton = item.querySelector('[data-role="editar-visitante"]');
            if (editButton) {
                let editUrl = visitante.edit_url;
                if (panelUrl && editUrl) {
                    const urlObj = new URL(editUrl, window.location.origin);
                    urlObj.searchParams.set('return_to', panelUrl);
                    editUrl = urlObj.pathname + urlObj.search;
                }
                if (editUrl) {
                    editButton.dataset.editUrl = editUrl;
                    editButton.onclick = () => abrirJanelaModal(editUrl);
                }
            }

            const deleteForm = item.querySelector('.painel-remover-visitante');
            if (deleteForm) {
                deleteForm.dataset.visitanteNome = visitante.nome;
                if (visitante.destroy_url) {
                    deleteForm.action = visitante.destroy_url;
                }
                attachDeleteHandler(deleteForm);
            }

            item.classList.remove('painel-visitante-highlight');
            void item.offsetWidth;
            item.classList.add('painel-visitante-highlight');
        }

        async function registrarPresenca(button, visitanteId) {
            if (!visitanteId) {
                return;
            }

            button.disabled = true;

            try {
                const response = await fetch(registrarPresencaUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        visitante_id: visitanteId,
                        data: dateInput && dateInput.value ? dateInput.value : selectedDate,
                    }),
                });

                if (!response.ok) {
                    throw new Error('Erro ao registrar presença');
                }

                const payload = await response.json();

                if (!payload.visitante) {
                    throw new Error('Resposta inesperada do servidor');
                }

                if (payload.row_html) {
                    insertRowFromHtml(payload.row_html, payload.visitante.id, payload.already_registered);
                } else {
                    addRegistrado(payload.visitante, payload.already_registered);
                }

                if (payload.already_registered) {
                    showFeedback('Visitante já consta como registrado para este culto.', 'info');
                } else {
                    showFeedback('Visitante registrado no culto com sucesso.', 'success');
                }

                if ($visitanteSelect) {
                    suppressClearFeedback = true;
                    $visitanteSelect.val(null).trigger('change');
                }
                selectedVisitante = null;
            } catch (error) {
                console.error(error);
                showFeedback('Não foi possível registrar o visitante. Verifique a conexão e tente novamente.', 'error');
            } finally {
                button.disabled = false;
            }
        }

        if (registrarButton) {
            registrarButton.addEventListener('click', () => {
                if (!selectedVisitante) {
                    showFeedback('Selecione um visitante na lista antes de registrar.', 'warning');
                    return;
                }

                registrarPresenca(registrarButton, selectedVisitante.id);
            });
        }

        if (novoVisitanteButton) {
            novoVisitanteButton.addEventListener('click', () => {
                abrirJanelaModal(novoVisitanteUrl);
            });
        }

        if (editarCultoButton) {
            editarCultoButton.addEventListener('click', () => {
                abrirJanelaModal(editarCultoUrl);
            });
        }

        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!form.matches('.painel-visitante-editar-form')) {
                return;
            }

            event.preventDefault();

            const visitanteId = form.dataset.visitanteId;
            const formData = new FormData(form);

            fetch(form.action, {
                method: form.getAttribute('method') || 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData,
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Request failed');
                    }
                    return response.json();
                })
                .then((payload) => {
                    if (typeof fecharJanelaModal === 'function') {
                        fecharJanelaModal();
                    }

                    if (payload.visitante) {
                        updateVisitanteInList(payload.visitante);
                    }

                    showFeedback(payload.message || 'Visitante atualizado com sucesso.', 'success');
                })
                .catch((error) => {
                    console.error(error);
                    showFeedback('Não foi possível atualizar o visitante. Tente novamente.', 'error');
                });
        });
    });
</script>
@endsection
