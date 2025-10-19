@php
    use Illuminate\Support\Carbon;
@endphp

<div class="modal-header">
    <h1 class="modal-title"><i class="bi bi-bell"></i> Próximos cultos</h1>
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

<div class="info">
    @if ($cultos->isEmpty())
        <div class="card modal-list-item modal-list-item--empty">
            <p class="modal-description">Nenhum culto agendado em breve.</p>
        </div>
    @else
        @foreach ($cultos as $culto)
            @php
                $inicio = Carbon::parse($culto->data_culto);
            @endphp
            <div class="card modal-list-item">
                <div class="modal-list-item__header">
                    <span class="modal-list-item__badge"><i class="bi bi-bell"></i> Culto</span>
                    <h3 class="modal-list-item__title">{{ $culto->tema_sermao ?: 'Culto Especial' }}</h3>
                </div>
                <dl class="modal-list-item__details">
                    <div>
                        <dt><i class="bi bi-clock"></i> Data</dt>
                        <dd>{{ $inicio->translatedFormat('d \\d\\e F \\à\\s H\\hi') }}</dd>
                    </div>
                    @if (! empty($culto->preletor))
                        <div>
                            <dt><i class="bi bi-mic"></i> Preletor</dt>
                            <dd>{{ $culto->preletor }}</dd>
                        </div>
                    @endif
                    @if (! empty($culto->texto_base))
                        <div>
                            <dt><i class="bi bi-book"></i> Texto base</dt>
                            <dd>{{ $culto->texto_base }}</dd>
                        </div>
                    @endif
                </dl>
                <div class="modal-list-item__footer">
                    <button type="button" class="btn btn-small" onclick="abrirJanelaModal('{{ route('agenda.detalhes', ['tipo' => 'culto', 'id' => $culto->id]) }}')">
                        <i class="bi bi-eye"></i> Ver detalhes
                    </button>
                    <span class="modal-list-item__meta">{{ $culto->created_at?->diffForHumans() }}</span>
                </div>
            </div>
        @endforeach
    @endif
</div>
