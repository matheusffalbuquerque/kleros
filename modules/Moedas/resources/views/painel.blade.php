@extends('layouts.main')

@section('title', ($congregacao->nome_curto ?? $congregacao->nome ?? 'Congregação') . ' | ' . $appName)

@section('content')
<div class="container">
    <h1>Painel Monetário</h1>

    <div>
        @if(session('success'))
            <div class="card alert-success">
                <p><i class="bi bi-check-circle"></i> {{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="card alert-danger">
                <p><i class="bi bi-exclamation-triangle"></i> Ajuste os campos abaixo para continuar.</p>
                <ul class="erro-lista">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    @if($moedas->isEmpty())
        <div>
            <h3>Configurar moeda da congregação</h3>
            <div class="card">
                <form action="{{ route('moedas.store') }}" method="post" class="painel-form-grid">
                    @csrf
                    <div class="form-group">
                        <label for="nome">Nome da moeda</label>
                        <input type="text" id="nome" name="nome" value="{{ old('nome') }}" maxlength="100" required>
                    </div>
                    <div class="form-group">
                        <label for="simbolo">Símbolo</label>
                        <input type="text" id="simbolo" name="simbolo" value="{{ old('simbolo') }}" maxlength="10" required>
                    </div>
                    <div class="form-group">
                        <label for="imagem_url">Imagem/Ícone (URL)</label>
                        <input type="url" id="imagem_url" name="imagem_url" value="{{ old('imagem_url') }}" maxlength="255" placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label for="taxa_conversao">Taxa de conversão (1 moeda = X reais)</label>
                        <input type="number" step="0.0001" min="0" id="taxa_conversao" name="taxa_conversao" value="{{ old('taxa_conversao') }}">
                    </div>
                    <div class="form-group">
                        <label for="responsavel_id">Responsável designado</label>
                        <select name="responsavel_id" id="responsavel_id">
                            <option value="">Selecione uma pessoa</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}" @selected(old('responsavel_id') == $usuario->id)>{{ $usuario->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ativo">Moeda ativa?</label>
                        <select name="ativo" id="ativo">
                            <option value="1" @selected(old('ativo', '1') == '1')>Sim</option>
                            <option value="0" @selected(old('ativo') === '0')>Não</option>
                        </select>
                    </div>
                    <div class="form-group form-group--wide">
                        <label for="descricao">Descrição / contextualização</label>
                        <textarea id="descricao" name="descricao" rows="4">{{ old('descricao') }}</textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn"><i class="bi bi-coin"></i> Criar moeda</button>
                    </div>
                </form>
            </div>
        </div>
    @else
        @php
            $moedaSelecionada ??= null;
            $regras = optional($moedaSelecionada)->regras;
            $totalCarteiras = $carteiras instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator ? $carteiras->total() : $carteiras->count();
            $totalTransacoes = $transacoes instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator ? $transacoes->total() : $transacoes->count();
        @endphp

        <div class="info">
            <h3>Resumo da moeda</h3>

            <div class="painel-cards">
                <div class="painel-card neutral">
                    <span class="label">Moeda</span>
                    <strong>{{ $moedaSelecionada->nome }}</strong>
                    <small>{{ $moedaSelecionada->simbolo }}</small>
                </div>
                <div class="painel-card neutral">
                    <span class="label">Saldo circulante</span>
                    <strong>{{ $moedaSelecionada->simbolo }} {{ number_format($saldoTotal, 2, ',', '.') }}</strong>
                    <small>Total distribuído entre as carteiras</small>
                </div>
                <div class="painel-card neutral">
                    <span class="label">Resumo</span>
                    <strong>{{ $totalCarteiras }} carteiras</strong>
                    <small>{{ $totalTransacoes }} transações registradas</small>
                </div>
                <div class="painel-card painel-card-detalhes">
                    <span class="label">Detalhes</span>
                    <div class="painel-card-detalhes-grid">
                        <div>
                            <strong>{{ $moedaSelecionada->taxa_conversao ? 'R$ ' . number_format($moedaSelecionada->taxa_conversao, 4, ',', '.') : '—' }}</strong>
                            <small>1 {{ $moedaSelecionada->simbolo }} equivale</small>
                        </div>
                        <div>
                            <strong>{{ optional($moedaSelecionada->responsavel)->name ?? 'Não definido' }}</strong>
                            <small>Responsável designado</small>
                        </div>
                        <div>
                            <strong>{{ $regras && $regras->permitir_transferencias ? 'Liberadas' : 'Restritas' }}</strong>
                            <small>Transferências entre usuários</small>
                        </div>
                        <div>
                            <strong>{{ $moedaSelecionada->ativo ? 'Ativa' : 'Inativa' }}</strong>
                            <small>Status</small>
                        </div>
                    </div>
                    @if($moedaSelecionada->descricao)
                        <div class="painel-card-detalhes-observacoes">
                            <small>Descrição</small>
                            <p>{{ $moedaSelecionada->descricao }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="search-panel painel-moedas-actions">
                <div class="search-panel-item">
                    @if($moedas->count() > 1)
                        <form action="{{ route('moedas.painel') }}" method="get" class="painel-moedas-select">
                            <label for="moeda_escolhida">Moeda</label>
                            <select name="moeda" id="moeda_escolhida" onchange="this.form.submit()">
                                @foreach($moedas as $moeda)
                                    <option value="{{ $moeda->id }}" @selected($moedaSelecionada && $moedaSelecionada->id === $moeda->id)>{{ $moeda->nome }}</option>
                                @endforeach
                            </select>
                        </form>
                    @endif
                </div>
                <div class="search-panel-item">
                    <button type="button" class="btn" data-modal-open="moedaEmitirModal">
                        <i class="bi bi-plus-circle"></i> Gerar moeda
                    </button>
                </div>
            </div>
        </div>

        <div class="info">
            <h3>Carteiras dos usuários</h3>
            @if($carteiras->isEmpty())
                <div class="card">
                    <p><i class="bi bi-info-circle"></i> Ainda não há usuários com saldo nesta moeda.</p>
                </div>
            @else
                <div class="list">
                    <div class="list-title">
                        <div class="item item-15"><b>Usuário</b></div>
                        <div class="item item-1"><b>Saldo</b></div>
                        <div class="item item-1"><b>Status</b></div>
                        <div class="item item-1"><b>Atualizado em</b></div>
                    </div>
                    <div id="moedas-carteiras-lista">
                        @foreach($carteiras as $carteira)
                            <div class="list-item">
                                <div class="item item-15">
                                    <p><i class="bi bi-person"></i> {{ $carteira->usuario?->name ?? 'Usuário removido' }}</p>
                                    <small>{{ $carteira->usuario?->email ?? '—' }}</small>
                                </div>
                                <div class="item item-1">
                                    <p>{{ $moedaSelecionada->simbolo }} {{ number_format($carteira->saldo, 2, ',', '.') }}</p>
                                </div>
                                <div class="item item-1">
                                    @if($carteira->bloqueado)
                                        <span class="status status--danger">Bloqueada</span>
                                    @else
                                        <span class="status status--success">Ativa</span>
                                    @endif
                                </div>
                                <div class="item item-1">
                                    <p>{{ optional($carteira->atualizado_em)->format('d/m/Y H:i') ?? '—' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($carteiras instanceof \Illuminate\Contracts\Pagination\Paginator)
                    <div class="pagination">
                        {{ $carteiras->withQueryString()->links('pagination::default') }}
                    </div>
                @endif
            @endif
        </div>

        <div class="info">
            <h3>Histórico de transações</h3>
            @if($transacoes->isEmpty())
                <div class="card">
                    <p><i class="bi bi-info-circle"></i> Nenhuma movimentação registrada até o momento.</p>
                </div>
            @else
                @php
                    $rotulos = [
                        'emissao' => 'Emissão',
                        'transferencia' => 'Transferência',
                        'recompensa' => 'Recompensa',
                        'compra' => 'Compra',
                        'resgate' => 'Resgate',
                        'ajuste_admin' => 'Ajuste administrativo',
                    ];
                @endphp
                <div class="list list-transacoes">
                    <div class="list-title">
                        <div class="item item-1"><b>Data</b></div>
                        <div class="item item-1"><b>Tipo</b></div>
                        <div class="item item-1"><b>Valor</b></div>
                        <div class="item item-15"><b>Origem</b></div>
                        <div class="item item-15"><b>Destino</b></div>
                        <div class="item item-2"><b>Descrição</b></div>
                    </div>
                    @foreach($transacoes as $transacao)
                        <div class="list-item">
                            <div class="item item-1">
                                <p>{{ optional($transacao->criado_em)->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="item item-1">
                                <p>{{ $rotulos[$transacao->tipo] ?? ucfirst($transacao->tipo) }}</p>
                            </div>
                            <div class="item item-1">
                                <p>{{ $moedaSelecionada->simbolo }} {{ number_format($transacao->valor, 2, ',', '.') }}</p>
                            </div>
                            <div class="item item-15">
                                <p>{{ $transacao->remetente?->name ?? 'Sistema' }}</p>
                            </div>
                            <div class="item item-15">
                                <p>{{ $transacao->destinatario?->name ?? '—' }}</p>
                            </div>
                            <div class="item item-2">
                                <p>{{ $transacao->descricao ?? '—' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($transacoes instanceof \Illuminate\Contracts\Pagination\Paginator)
                    <div class="pagination">
                        {{ $transacoes->withQueryString()->links('pagination::default') }}
                    </div>
                @endif
            @endif
        </div>

        <div class="info">
            <h3>Configurações</h3>
            <div class="painel-config-grid">
                <div class="card">
                    <h4>Informações gerais</h4>
                    <form action="{{ route('moedas.update', $moedaSelecionada) }}" method="post" class="painel-form-grid">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="nome_edit">Nome</label>
                            <input type="text" id="nome_edit" name="nome" value="{{ old('nome', $moedaSelecionada->nome) }}" maxlength="100" required>
                        </div>
                        <div class="form-group">
                            <label for="simbolo_edit">Símbolo</label>
                            <input type="text" id="simbolo_edit" name="simbolo" value="{{ old('simbolo', $moedaSelecionada->simbolo) }}" maxlength="10" required>
                        </div>
                        <div class="form-group">
                            <label for="imagem_url_edit">Imagem/Ícone (URL)</label>
                            <input type="url" id="imagem_url_edit" name="imagem_url" value="{{ old('imagem_url', $moedaSelecionada->imagem_url) }}" maxlength="255">
                        </div>
                        <div class="form-group">
                            <label for="taxa_conversao_edit">Taxa de conversão</label>
                            <input type="number" step="0.0001" min="0" id="taxa_conversao_edit" name="taxa_conversao" value="{{ old('taxa_conversao', $moedaSelecionada->taxa_conversao) }}">
                        </div>
                        <div class="form-group">
                            <label for="ativo_edit">Status</label>
                            <select name="ativo" id="ativo_edit">
                                <option value="1" @selected(old('ativo', $moedaSelecionada->ativo ? '1' : '0') == '1')>Ativa</option>
                                <option value="0" @selected(old('ativo', $moedaSelecionada->ativo ? '1' : '0') == '0')>Inativa</option>
                            </select>
                        </div>
                        <div class="form-group form-group--wide">
                            <label for="descricao_edit">Descrição</label>
                            <textarea id="descricao_edit" name="descricao" rows="4">{{ old('descricao', $moedaSelecionada->descricao) }}</textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn"><i class="bi bi-save"></i> Salvar</button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <h4>Regras da moeda</h4>
                    <form action="{{ route('moedas.regras.update', $moedaSelecionada) }}" method="post" class="painel-form-grid">
                        @csrf
                        @method('PUT')
                        <div class="form-group form-group--checkbox">
                            <label>
                                <input type="hidden" name="permitir_transferencias" value="0">
                                <input type="checkbox" name="permitir_transferencias" value="1" @checked(old('permitir_transferencias', $regras->permitir_transferencias ?? true))>
                                Permitir transferências entre usuários
                            </label>
                        </div>
                        <div class="form-group form-group--checkbox">
                            <label>
                                <input type="hidden" name="permitir_resgate" value="0">
                                <input type="checkbox" name="permitir_resgate" value="1" @checked(old('permitir_resgate', $regras->permitir_resgate ?? false))>
                                Permitir resgate em dinheiro
                            </label>
                        </div>
                        <div class="form-group form-group--checkbox">
                            <label>
                                <input type="hidden" name="permitir_uso_em_jogos" value="0">
                                <input type="checkbox" name="permitir_uso_em_jogos" value="1" @checked(old('permitir_uso_em_jogos', $regras->permitir_uso_em_jogos ?? false))>
                                Permitir uso em jogos/eventos
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="limite_diario">Limite diário por usuário</label>
                            <input type="number" step="0.01" min="0" id="limite_diario" name="limite_diario" value="{{ old('limite_diario', $regras->limite_diario ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label for="taxa_transacao">Taxa de transação (%)</label>
                            <input type="number" step="0.01" min="0" max="100" id="taxa_transacao" name="taxa_transacao" value="{{ old('taxa_transacao', $regras->taxa_transacao ?? 0) }}">
                        </div>
                        <div class="form-group">
                            <label for="minimo_resgate">Mínimo para resgate</label>
                            <input type="number" step="0.01" min="0" id="minimo_resgate" name="minimo_resgate" value="{{ old('minimo_resgate', $regras->minimo_resgate ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label for="responsavel_config">Responsável designado</label>
                            <select name="responsavel_id" id="responsavel_config">
                                <option value="">Selecione uma pessoa</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" @selected(old('responsavel_id', $moedaSelecionada->criado_por) == $usuario->id)>{{ $usuario->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group form-group--wide">
                            <label for="observacoes">Observações internas</label>
                            <textarea id="observacoes" name="observacoes" rows="4">{{ old('observacoes', $regras->observacoes ?? '') }}</textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-secondary"><i class="bi bi-shield-check"></i> Atualizar regras</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="moeda-modal" id="moedaEmitirModal" hidden>
            <div class="moeda-modal__overlay" data-modal-close></div>
            <div class="moeda-modal__dialog" role="dialog" aria-modal="true">
                <header class="moeda-modal__header">
                    <h3>Emitir {{ $moedaSelecionada->nome }}</h3>
                    <button type="button" class="moeda-modal__close" data-modal-close>&times;</button>
                </header>
                <form action="{{ route('moedas.emitir', $moedaSelecionada) }}" method="post" class="painel-form-grid">
                    @csrf
                    <div class="form-group">
                        <label for="usuario_id">Destinatário</label>
                        <select name="usuario_id" id="usuario_id" required>
                            <option value="">Selecione um usuário</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="valor">Quantidade</label>
                        <input type="number" step="0.01" min="0.01" id="valor" name="valor" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label for="descricao_modal">Descrição (opcional)</label>
                        <textarea id="descricao_modal" name="descricao" rows="3" placeholder="Justifique ou descreva esta emissão"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="referencia_id">Referência externa (opcional)</label>
                        <input type="number" min="1" id="referencia_id" name="referencia_id" placeholder="ID relacionado (evento, jogo, etc)">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn"><i class="bi bi-coin"></i> Emitir</button>
                        <button type="button" class="btn btn-secondary" data-modal-close>Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .alert-success {
        background: rgba(34, 197, 94, 0.12);
        border: 1px solid rgba(34, 197, 94, 0.25);
    }

    .alert-danger {
        background: rgba(248, 113, 113, 0.12);
        border: 1px solid rgba(248, 113, 113, 0.25);
    }

    .alert-success p,
    .alert-danger p {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .erro-lista {
        margin: 0.75rem 0 0;
        padding-left: 1.5rem;
    }

    .painel-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        align-items: start;
    }

    .painel-form-grid .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .painel-form-grid .form-group--wide {
        grid-column: 1 / -1;
    }

    .painel-form-grid input,
    .painel-form-grid select,
    .painel-form-grid textarea {
        width: 100%;
        border: 1px solid rgba(148, 163, 184, 0.35);
        border-radius: 8px;
        padding: 0.6rem 0.7rem;
        font-size: 0.95rem;
        background: rgba(255, 255, 255, 0.05);
        color: inherit;
    }

    .painel-form-grid textarea {
        resize: vertical;
    }

    .painel-form-grid input::placeholder,
    .painel-form-grid textarea::placeholder {
        color: rgba(226, 232, 240, 0.6);
    }

    .painel-form-grid input:focus,
    .painel-form-grid select:focus,
    .painel-form-grid textarea:focus {
        outline: none;
        border-color: rgba(59, 130, 246, 0.6);
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
    }

    .form-actions {
        grid-column: 1 / -1;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    .form-group--checkbox {
        flex-direction: row;
        align-items: center;
        gap: 0.6rem;
    }

    .painel-cards {
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
        font-size: 1.5rem;
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
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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

    .painel-moedas-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
    }

    .painel-moedas-select {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0;
    }

    .painel-moedas-select label {
        font-size: 0.85rem;
        font-weight: 600;
    }

    .painel-moedas-select select {
        padding: 0.45rem 0.65rem;
        border-radius: 6px;
        border: 1px solid var(--border-color, #d9d9d9);
        background: rgba(255, 255, 255, 0.05);
        color: inherit;
    }

    .status {
        font-weight: 600;
    }

    .status--success {
        color: #16a34a;
    }

    .status--danger {
        color: #dc2626;
    }

    .list-transacoes .item {
        align-self: flex-start;
    }

    .painel-config-grid {
        display: grid;
        gap: 1.5rem;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    }

    .painel-config-grid .card {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .painel-config-grid h4 {
        margin: 0;
    }

    .moeda-modal[hidden] {
        display: none;
    }

    .moeda-modal {
        position: fixed;
        inset: 0;
        z-index: 999;
    }

    .moeda-modal__overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.25);
        backdrop-filter: blur(2px);
    }

    .moeda-modal__dialog {
        position: relative;
        background: var(--background-color);
        width: min(480px, 90vw);
        border-radius: 16px;
        margin: 6vh auto;
        padding: 1.5rem;
        box-shadow: 0 24px 48px -32px rgba(15, 23, 42, 0.45);
        animation: fadeInScale .2s ease;
    }

    .moeda-modal__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .moeda-modal__close {
        border: none;
        background: transparent;
        font-size: 1.5rem;
        line-height: 1;
        cursor: pointer;
        color: rgba(15, 23, 42, 0.6);
    }

    @keyframes fadeInScale {
        from { opacity: 0; transform: scale(.95); }
        to { opacity: 1; transform: scale(1); }
    }

    @media (max-width: 768px) {
        .painel-form-grid {
            grid-template-columns: 1fr;
        }

        .painel-cards {
            grid-template-columns: 1fr;
        }

        .painel-card {
            padding: 16px;
        }

        .list .list-item {
            flex-direction: column;
            gap: 0.5rem;
        }

        .painel-moedas-actions {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-modal-open]').forEach(trigger => {
            const targetId = trigger.getAttribute('data-modal-open');
            const modal = document.getElementById(targetId);

            if (!modal) {
                return;
            }

            trigger.addEventListener('click', () => {
                modal.removeAttribute('hidden');
                const firstInput = modal.querySelector('input, select, textarea');
                if (firstInput) {
                    firstInput.focus();
                }
            });
        });

        document.querySelectorAll('[data-modal-close]').forEach(close => {
            close.addEventListener('click', () => {
                const modal = close.closest('.moeda-modal');
                if (modal) {
                    modal.setAttribute('hidden', 'hidden');
                }
            });
        });

        document.querySelectorAll('.moeda-modal__overlay').forEach(overlay => {
            overlay.addEventListener('click', () => {
                const modal = overlay.closest('.moeda-modal');
                if (modal) {
                    modal.setAttribute('hidden', 'hidden');
                }
            });
        });
    });
</script>
@endpush
