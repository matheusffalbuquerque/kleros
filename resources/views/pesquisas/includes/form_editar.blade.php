@php
    $activeTab = session('tab', request('tab', 'config'));
@endphp
<h1>Editar Pesquisa</h1>
<div class="info">
    <div class="tabs">
        <ul class="tab-menu">
            <li class="{{ $activeTab === 'config' ? 'active' : '' }}" data-tab="pesquisa-config"><i class="bi bi-gear"></i> Configurações</li>
            <li class="{{ $activeTab === 'perguntas' ? 'active' : '' }}" data-tab="pesquisa-perguntas"><i class="bi bi-list-check"></i> Perguntas</li>
        </ul>

        <div class="tab-content card">
            <div id="pesquisa-config" class="tab-pane form-control {{ $activeTab === 'config' ? 'active' : '' }}">
                <form action="{{ route('pesquisas.update', $pesquisa->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-item">
                        <label for="titulo">Título</label>
                        <input type="text" name="titulo" id="titulo" required value="{{ old('titulo', $pesquisa->titulo) }}">
                    </div>
                    <div class="form-item">
                        <label for="descricao">Descrição</label>
                        <textarea name="descricao" id="descricao" rows="4">{{ old('descricao', $pesquisa->descricao) }}</textarea>
                    </div>
                    <div class="form-item">
                        <label for="criada_por">Responsável</label>
                        <select name="criada_por" id="criada_por" required>
                            <option value="">Selecione um membro</option>
                            @foreach($membros as $membro)
                                <option value="{{ $membro->id }}" @selected(old('criada_por', $pesquisa->criada_por) == $membro->id)>{{ $membro->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-item">
                        <label for="data_inicio">Data de início</label>
                        <input type="date" name="data_inicio" id="data_inicio" value="{{ old('data_inicio', optional($pesquisa->data_inicio)->format('Y-m-d')) }}">
                    </div>
                    <div class="form-item">
                        <label for="data_fim">Data de encerramento</label>
                        <input type="date" name="data_fim" id="data_fim" value="{{ old('data_fim', optional($pesquisa->data_fim)->format('Y-m-d')) }}">
                    </div>
                    <div class="form-options">
                        <button type="submit" class="btn"><i class="bi bi-check-circle"></i> Salvar alterações</button>
                        <button type="button" class="btn danger" onclick="handleSubmit(event, document.getElementById('delete-pesquisa-{{ $pesquisa->id }}'), 'Deseja realmente excluir esta pesquisa?')"><i class="bi bi-trash"></i> Excluir</button>
                        <button type="button" class="btn" onclick="fecharJanelaModal()"><i class="bi bi-x-circle"></i> Cancelar</button>
                    </div>
                </form>
            </div>

            <div id="pesquisa-perguntas" class="tab-pane {{ $activeTab === 'perguntas' ? 'active' : '' }}">
                <div class="tab-pane-content">
                    <section class="pergunta-card">
                        <h4>Nova pergunta</h4>
                        <form action="{{ route('pesquisas.perguntas.store', $pesquisa->id) }}" method="POST" data-pergunta-form="nova">
                            @csrf
                            <div class="form-control">
                                <div class="form-item">
                                    <label for="texto-novo">Enunciado</label>
                                    <textarea name="texto" id="texto-novo" rows="3" required>{{ old('texto') }}</textarea>
                                    <small class="hint text-error" data-error-target="texto" hidden></small>
                                    @error('texto')
                                        <small class="hint text-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                @php
                                    $tipoNovo = old('tipo', 'texto');
                                @endphp
                                <div class="form-item">
                                    <label for="tipo-novo">Tipo de resposta</label>
                                    <select name="tipo" id="tipo-novo" data-toggle-options="#options-novo">
                                        <option value="texto" @selected($tipoNovo === 'texto')>Texto livre</option>
                                        <option value="radio" @selected($tipoNovo === 'radio')>Escolha única</option>
                                        <option value="checkbox" @selected($tipoNovo === 'checkbox')>Múltipla escolha</option>
                                    </select>
                                    <small class="hint text-error" data-error-target="tipo" hidden></small>
                                </div>
                                <div class="form-item options-box" id="options-novo" style="display: {{ in_array($tipoNovo, ['radio','checkbox']) ? 'block' : 'none' }};">
                                    <label for="options">Opções (uma por linha) <br> <small class="">As respostas serão criadas conforme as opções listadas.</small></label>
                                    <textarea name="options" id="options">{{ old('options') }}</textarea>
                                    <small class="hint text-error" data-error-target="options" hidden></small>
                                    @error('options')
                                        <small class="hint text-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-options">
                                    <button type="submit" class="btn"><i class="bi bi-plus-circle"></i> Adicionar pergunta</button>
                                </div>
                                <div class="form-feedback" data-pergunta-feedback hidden></div>
                            </div>
                        </form>
                    </section>

                    <section class="pergunta-list">
                        <h4>Perguntas cadastradas</h4>
                        @php
                            $perguntas = $pesquisa->perguntas;
                            $perPage = 5;
                            $currentPage = max(1, (int) request('pergunta_page', 1));
                            $totalPerguntas = $perguntas->count();
                            $totalPages = max(1, (int) ceil($totalPerguntas / $perPage));
                            if ($currentPage > $totalPages) {
                                $currentPage = $totalPages;
                            }
                            $slice = $perguntas->slice(($currentPage - 1) * $perPage, $perPage)->values();
                        @endphp

                        <div data-perguntas-list>
                        @forelse($slice as $pergunta)
                            @php
                                $isCurrent = old('pergunta_id') == $pergunta->id;
                                $textoAnterior = $isCurrent ? old('texto') : $pergunta->texto;
                                $tipoAnterior = $isCurrent ? old('tipo') : $pergunta->tipo;
                                $optionsAnterior = $isCurrent ? old('options') : $pergunta->opcoes->pluck('texto')->implode("\n");
                            @endphp
                            @include('pesquisas.includes.partials.pergunta_card', [
                                'pesquisa' => $pesquisa,
                                'pergunta' => $pergunta,
                                'isCurrent' => $isCurrent,
                                'textoAnterior' => $textoAnterior,
                                'tipoAnterior' => $tipoAnterior,
                                'optionsAnterior' => $optionsAnterior,
                                'showErrors' => $isCurrent,
                            ])
                        @empty
                            <div class="card" data-perguntas-empty>
                                <p><i class="bi bi-info-circle"></i> Ainda não há perguntas cadastradas para esta pesquisa.</p>
                            </div>
                        @endforelse
                        </div>

                        @if($totalPages > 1)
                            <div class="pagination pagination-compact" data-perguntas-pagination>
                                @for($page = 1; $page <= $totalPages; $page++)
                                    <button type="button" class="page-btn {{ $page === $currentPage ? 'active' : '' }}" onclick="abrirJanelaModal('{{ route('pesquisas.form_editar', ['id' => $pesquisa->id, 'pergunta_page' => $page, 'tab' => 'perguntas']) }}')">{{ $page }}</button>
                                @endfor
                            </div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="delete-pesquisa-{{ $pesquisa->id }}" action="{{ route('pesquisas.destroy', $pesquisa->id) }}" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<style>
    .tabs {
        width: 100%;
    }
    .tab-menu {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0 0 1rem 0;
        border-bottom: 2px solid var(--secondary-color);
        gap: .25rem;
    }
    .tab-menu li {
        display: flex;
        align-items: center;
        gap: .35rem;
        cursor: pointer;
        background: rgba(15, 23, 42, .08);
        color: var(--secondary-color);
        padding: .5rem 1rem;
        border-radius: .75rem .75rem 0 0;
        font-weight: 500;
        transition: all .2s ease;
    }
    .tab-menu li i {
        font-size: 1rem;
    }
    .tab-menu li:hover,
    .tab-menu li.active {
        background: var(--secondary-color);
        color: var(--secondary-contrast);
    }
    .tab-content.card {
        border-radius: 0 .75rem .75rem .75rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 18px 32px rgba(17, 24, 39, 0.16);
    }
    .tab-pane {
        display: none;
        animation: fadeIn .3s ease-in-out;
    }
    .tab-pane.active {
        display: block;
    }
    .tab-pane-content {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    .pergunta-card {
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 16px;
        padding: 1.25rem;
        background: rgba(255, 255, 255, 0.06);
        box-shadow: 0 14px 30px rgba(17, 24, 39, 0.22);
        color: var(--text-font);
    }
    .pergunta-accordion {
        padding: 0;
        overflow: hidden;
    }
    .pergunta-toggle {
        width: 100%;
        background: none;
        border: none;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.25rem;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        color: var(--secondary-color);
        transition: background .2s ease, color .2s ease;
    }
    .pergunta-toggle:hover,
    .pergunta-accordion.open .pergunta-toggle {
        background: rgba(255, 255, 255, 0.08);
        color: var(--secondary-contrast);
    }
    .pergunta-toggle i {
        transition: transform .25s ease;
    }
    .pergunta-accordion.open .pergunta-toggle i {
        transform: rotate(180deg);
    }
    .pergunta-header {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
        text-align: left;
    }
    .pergunta-meta {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .85rem;
        color: rgba(255, 255, 255, .7);
    }
    .pergunta-meta .divider {
        opacity: .65;
    }
    .pergunta-body {
        display: none;
        padding: 0 1.25rem 1.25rem;
        border-top: 1px solid rgba(255, 255, 255, 0.12);
        animation: fadeIn .25s ease;
    }
    .pergunta-accordion.open .pergunta-body {
        display: block;
    }
    .pergunta-card h4,
    .pergunta-card h5 {
        margin-top: 0;
    }
    .pergunta-card .form-control {
        gap: 1rem;
    }
    .options-box textarea {
        min-height: 120px;
        resize: vertical;
    }
    .pergunta-card h4,
    .pergunta-card h5 {
        color: var(--text-font);
    }

    .hint {
        font-size: .85rem;
        color: rgba(255, 255, 255, .7);
    }
    .hint.text-error {
        color: #dc2626;
    }
    .pergunta-card .form-options {
        gap: .5rem;
        flex-wrap: wrap;
    }
    .form-feedback {
        margin-top: .5rem;
        font-size: .9rem;
        color: var(--text-font);
    }
    .pagination-compact {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 999px;
        padding: 0.4rem 0.6rem;
        width: fit-content;
        margin-left: auto;
        margin-right: auto;
        box-shadow: 0 10px 22px rgba(46, 46, 46, 0.2);
    }
    .pagination-compact .page-btn {
        min-width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        background: transparent;
        color: var(--text-font);
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
    }
    .pagination-compact .page-btn:hover {
        background: rgba(255, 255, 255, 0.12);
        box-shadow: 0 6px 14px rgba(17, 24, 39, 0.18);
    }
    .pagination-compact .page-btn.active {
        background: var(--secondary-color);
        color: var(--secondary-contrast);
        box-shadow: 0 10px 18px rgba(37, 37, 37, 0.35);
    }
    .pagination-compact .page-btn:focus-visible {
        outline: 2px solid var(--secondary-color);
        outline-offset: 3px;
    }
</style>
