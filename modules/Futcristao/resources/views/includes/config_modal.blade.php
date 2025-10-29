@php
    $tab = request('tab', 'tab-geral');
@endphp
<div class="config-modal">
    <h1><i class="bi bi-sliders"></i> Configurações do Futcristão</h1>
    <div class="tabs">
        <ul class="tab-menu">
            <li class="{{ $tab === 'tab-geral' ? 'active' : '' }}" data-tab="tab-geral"><i class="bi bi-gear"></i> Geral</li>
            <li class="{{ $tab === 'tab-regras' ? 'active' : '' }}" data-tab="tab-regras"><i class="bi bi-book"></i> Regras</li>
        </ul>
        <div class="tab-content card">
            <form action="{{ route('futcristao.config.update') }}" method="POST" class="form-control">
                @csrf
                @method('PUT')
                <div id="tab-geral" class="tab-pane {{ $tab === 'tab-geral' ? 'active' : '' }}">
                    <div class="form-item">
                        <label for="numero_jogadores">Número padrão de jogadores</label>
                        <input type="number" min="5" max="30" id="numero_jogadores" name="numero_jogadores" value="{{ old('numero_jogadores', $config->numero_jogadores) }}" required>
                        <small class="hint">Utilizado para montar as escalações rápidas e sugerir times equilibrados.</small>
                        @error('numero_jogadores')
                            <small class="hint text-error">{{ $message }}</small>
                        @enderror
                    </div>
                    <p class="tip"><i class="bi bi-info-circle"></i> Esta configuração se aplica apenas à congregação <strong>{{ $congregacao->nome_curto ?? $congregacao->identificacao }}</strong>.</p>
                </div>
                <div id="tab-regras" class="tab-pane {{ $tab === 'tab-regras' ? 'active' : '' }}">
                    <div class="form-item">
                        <label for="regras_gerais">Regras gerais e observações</label>
                        <textarea id="regras_gerais" name="regras_gerais" rows="8" placeholder="Descreva combinações de uniforme, escala disciplinar, critérios de presença, etc.">{{ old('regras_gerais', $config->regras_gerais) }}</textarea>
                        @error('regras_gerais')
                            <small class="hint text-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-options">
                    <button type="submit" class="btn"><i class="bi bi-check"></i> Salvar ajustes</button>
                    <button type="button" class="btn" onclick="fecharJanelaModal()"><i class="bi bi-x"></i> Fechar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .config-modal {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        min-width: min(680px, 100%);
        color: var(--text-color);
    }
    .config-modal h1 {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin: 0;
    }
    .config-modal .tab-content {
        padding: 1.5rem;
        background: color-mix(in srgb, var(--background-color, #fff) 85%, white);
        border-radius: 18px;
        border: 1px solid color-mix(in srgb, var(--text-color, #0f172a) 12%, transparent);
        box-shadow: 0 15px 30px rgba(15,23,42,0.12);
    }
    .config-modal .form-item {
        display: flex;
        flex-direction: column;
        gap: .35rem;
        margin-bottom: 1rem;
    }
    .config-modal .form-item label {
        font-weight: 600;
    }
    .config-modal .form-item input,
    .config-modal .form-item textarea {
        border: 1px solid color-mix(in srgb, var(--text-color) 20%, transparent);
        border-radius: 10px;
        padding: .65rem .9rem;
        font-size: 1rem;
        background: rgba(255,255,255,0.95);
        color: var(--text-color);
    }
    .config-modal .form-options {
        display: flex;
        gap: .75rem;
        justify-content: flex-end;
        margin-top: 1rem;
        flex-wrap: wrap;
    }
    .config-modal .tip {
        background: color-mix(in srgb, var(--primary-color, #1d4ed8) 10%, transparent);
        padding: .75rem 1rem;
        border-radius: 10px;
        margin: 0;
        font-size: .9rem;
        color: var(--text-color);
    }
</style>
