@php
    use Illuminate\Support\Carbon;
@endphp

<div class="modal-header">
    <h1 class="modal-title"><i class="bi bi-calendar-event"></i> Próximos eventos</h1>
</div>

<div class="info">
    @if ($eventos->isEmpty())
        <div class="card modal-list-item modal-list-item--empty">
            <p class="modal-description">Nenhum evento agendado em breve.</p>
        </div>
    @else
    @foreach ($eventos as $evento)
        @php
            $inicio = Carbon::parse($evento->data_inicio);
            $fim = $evento->data_encerramento ? Carbon::parse($evento->data_encerramento) : null;
            $datas = $inicio->translatedFormat('d \\d\\e F \\à\\s H\\hi');

            if ($fim && $fim->greaterThan($inicio)) {
                $datas .= ' - ' . $fim->translatedFormat('d \\d\\e F \\à\\s H\\hi');
            }
        @endphp
        <div class="card modal-list-item">
            <div class="modal-list-item__header">
                <span class="modal-list-item__badge"><i class="bi bi-calendar-event"></i> Evento</span>
                <h3 class="modal-list-item__title">{{ $evento->titulo }}</h3>
            </div>
            <dl class="modal-list-item__details">
                <div>
                    <dt><i class="bi bi-clock"></i> Quando</dt>
                    <dd>{{ $datas }}</dd>
                </div>
                @if (! empty($evento->local))
                    <div>
                        <dt><i class="bi bi-geo-alt"></i> Local</dt>
                        <dd>{{ $evento->local }}</dd>
                    </div>
                @endif
            </dl>
            <div class="modal-list-item__footer">
                <button type="button" class="btn btn-small" onclick="abrirJanelaModal('{{ route('agenda.detalhes', ['tipo' => 'evento', 'id' => $evento->id]) }}')">
                    <i class="bi bi-eye"></i> Ver detalhes
                </button>
                <span class="modal-list-item__meta">{{ $evento->created_at?->diffForHumans() }}</span>
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
