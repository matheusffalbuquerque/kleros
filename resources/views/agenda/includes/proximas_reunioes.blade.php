@php
    use Illuminate\Support\Carbon;
@endphp

<div class="modal-header">
    <h1 class="modal-title"><i class="bi bi-people"></i> Próximas reuniões</h1>
</div>

<div class="info">
    @if ($reunioes->isEmpty())
        <div class="card modal-list-item modal-list-item--empty">
            <p class="modal-description">Nenhuma reunião agendada em breve.</p>
        </div>
    @else
        @foreach ($reunioes as $reuniao)
            @php
                $inicio = Carbon::parse($reuniao->data_inicio);
                $fim = $reuniao->data_fim ? Carbon::parse($reuniao->data_fim) : null;
            @endphp
            <div class="card modal-list-item">
                <div class="modal-list-item__header">
                    <span class="modal-list-item__badge"><i class="bi bi-people"></i> Reunião</span>
                    <h3 class="modal-list-item__title">{{ $reuniao->assunto }}</h3>
                </div>
                <dl class="modal-list-item__details">
                    <div>
                        <dt><i class="bi bi-clock"></i> Início</dt>
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
                        <dt><i class="bi bi-shield-lock"></i> Privacidade</dt>
                        <dd>{{ $reuniao->privado ? 'Reunião privada' : 'Aberta aos convidados' }}</dd>
                    </div>
                </dl>
                <div class="modal-list-item__footer">
                    <button type="button" class="btn btn-small" onclick="abrirJanelaModal('{{ route('agenda.detalhes', ['tipo' => 'reuniao', 'id' => $reuniao->id]) }}')">
                        <i class="bi bi-eye"></i> Ver detalhes
                    </button>
                    <span class="modal-list-item__meta">{{ $reuniao->created_at?->diffForHumans() }}</span>
                </div>
            </div>
        @endforeach
    @endif
</div>

<style>
    .modal-list-item {
        display: flex;
        flex-direction: column;
        gap: 12px;
        border: 1px solid var(--secondary-color);
        border-radius: var(--border-style);
        background: var(--background-color);
    }
    .modal-list-item + .modal-list-item {
        margin-top: 12px;
    }
    .modal-list-item--empty {
        text-align: center;
    }
    .modal-list-item__header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }
    .modal-list-item__badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: var(--border-style);
        border: 1px solid var(--secondary-color);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: var(--secondary-color);
    }
    .modal-list-item__title {
        margin: 0;
        font-size: 1rem;
        color: var(--text-color);
        font-weight: 600;
    }
    .modal-list-item__details {
        margin: 0;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
    }
    .modal-list-item__details dt {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: var(--secondary-color);
        margin-bottom: 4px;
    }
    .modal-list-item__details dd {
        margin: 0;
        font-size: 0.9rem;
        color: var(--text-color);
    }
    .modal-list-item__footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .btn.btn-small {
        padding: 6px 14px;
        font-size: 0.85rem;
    }
    .modal-list-item__meta {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: var(--secondary-color);
    }
</style>
