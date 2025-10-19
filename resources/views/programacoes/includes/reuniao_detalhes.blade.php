@php
    use Illuminate\Support\Carbon;
    $inicio = Carbon::parse($reuniao->data_inicio);
    $fim = $reuniao->data_fim ? Carbon::parse($reuniao->data_fim) : null;
@endphp

<div class="modal-header">
    <h1 class="modal-title"><i class="bi bi-people"></i> {{ $reuniao->assunto }}</h1>
</div>

<div class="info">
    <div class="card">
        <dl class="modal-details">
            <div>
                <dt><i class="bi bi-calendar-event"></i> Início</dt>
                <dd>{{ $inicio->translatedFormat('d \\d\\e F \\à\\s H\\hi') }}</dd>
            </div>
            @if ($fim && $fim->greaterThan($inicio))
                <div>
                    <dt><i class="bi bi-clock-history"></i> Encerramento</dt>
                    <dd>{{ $fim->translatedFormat('d \\d\\e F \\à\\s H\\hi') }}</dd>
                </div>
            @endif
            @if (! empty($reuniao->local))
                <div>
                    <dt><i class="bi bi-geo-alt"></i> Local</dt>
                    <dd>{{ $reuniao->local }}</dd>
                </div>
            @endif
            @if (! empty($reuniao->tipo))
                <div>
                    <dt><i class="bi bi-tag"></i> Tipo</dt>
                    <dd>{{ ucfirst($reuniao->tipo) }}</dd>
                </div>
            @endif
            <div>
                <dt><i class="bi bi-shield-check"></i> Privacidade</dt>
                <dd>{{ $reuniao->privado ? 'Reunião privada' : 'Aberta aos participantes' }}</dd>
            </div>
            @if ($reuniao->online && ! empty($reuniao->link_online))
                <div>
                    <dt><i class="bi bi-camera-video"></i> Link online</dt>
                    <dd><a href="{{ $reuniao->link_online }}" target="_blank" rel="noopener" class="text-white underline">Acessar sala virtual</a></dd>
                </div>
            @endif
        </dl>
    </div>

    @if (! empty($reuniao->descricao))
        <div class="card">
            <h2 class="modal-section-title"><i class="bi bi-card-text"></i> Descrição</h2>
            <p class="modal-description">{{ $reuniao->descricao }}</p>
        </div>
    @endif

    <div class="modal-actions">
        <button type="button" class="btn" onclick="fecharJanelaModal()">
            <i class="bi bi-x-circle"></i> Fechar
        </button>
    </div>
</div>

<style>
    .modal-header {
        margin-bottom: 16px;
    }
    .modal-title {
        font-size: 1.6rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .modal-title i {
        color: var(--terciary-color);
    }
    .modal-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin: 0;
    }
    .modal-details dt {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: var(--secondary-color);
        margin-bottom: 6px;
    }
    .modal-details dd {
        margin: 0;
        font-size: 0.95rem;
        color: var(--text-color);
    }
    .modal-section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1rem;
        margin-bottom: 10px;
    }
    .modal-description {
        margin: 0;
        font-size: 0.95rem;
        line-height: 1.6;
        color: var(--text-color);
    }
    .modal-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
    }
</style>
