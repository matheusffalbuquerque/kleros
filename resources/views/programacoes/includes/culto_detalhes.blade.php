@php
    use Illuminate\Support\Carbon;

    $dataCulto = $culto->data_culto ? Carbon::parse($culto->data_culto) : null;
    $eventoRelacionado = optional($culto->evento);
@endphp

<div class="modal-header">
    <h1 class="modal-title"><i class="bi bi-broadcast-pin"></i> {{ $culto->tema_sermao ?? 'Culto Especial' }}</h1>
</div>

<div class="info">
    <div class="card">
        <dl class="modal-details">
            @if ($dataCulto)
                <div>
                    <dt><i class="bi bi-calendar-week"></i> Data</dt>
                    <dd>{{ $dataCulto->translatedFormat('d \\d\\e F \\à\\s H\\hi') }}</dd>
                </div>
            @endif

            @if (!empty($culto->preletor_label))
                <div>
                    <dt><i class="bi bi-mic"></i> Preletor</dt>
                    <dd>{{ $culto->preletor_label }}</dd>
                </div>
            @endif

            @if (!empty($culto->texto_base))
                <div>
                    <dt><i class="bi bi-book"></i> Texto base</dt>
                    <dd>{{ $culto->texto_base }}</dd>
                </div>
            @endif

            @if ($eventoRelacionado && $eventoRelacionado->titulo)
                <div>
                    <dt><i class="bi bi-calendar-event"></i> Evento associado</dt>
                    <dd>{{ $eventoRelacionado->titulo }}</dd>
                </div>
            @endif
        </dl>
    </div>

    @if (!empty($culto->observacoes))
        <div class="card">
            <h2 class="modal-section-title"><i class="bi bi-card-text"></i> Observações</h2>
            <p class="modal-description">{{ $culto->observacoes }}</p>
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
