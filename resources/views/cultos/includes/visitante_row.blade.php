@php
    $visitanteId = data_get($visitante, 'id');
    $nome = data_get($visitante, 'nome');
    $telefone = data_get($visitante, 'telefone');
    $situacao = data_get($visitante, 'situacao')
        ?? optional(data_get($visitante, 'sit_visitante'))->titulo
        ?? 'Não informado';
    $visitas = $totalVisitas
        ?? data_get($visitante, 'visitas')
        ?? data_get($visitante, 'visit_count', 1);
    $panelUrl = $panelUrl ?? route('cultos.painel');
@endphp

<div class="list-item taggable-item" data-visitante-id="{{ $visitanteId }}">
    <div class="item item-15" data-visitante-field="nome">
        <p><i class="bi bi-person-raised-hand"></i> {{ $nome }}</p>
    </div>
    <div class="item item-1" data-visitante-field="telefone">
        <p>{{ $telefone }}</p>
    </div>
    <div class="item item-15" data-visitante-field="situacao">
        <p>{{ $situacao }}</p>
    </div>
    <div class="item item-05" data-visitante-field="visitas">
        <p>{{ $visitas }}</p>
    </div>
    <div class="taggable-actions nao-imprimir">
        <div class="taggable">
            <button class="taggable-action"
                type="button"
                data-role="editar-visitante"
                data-edit-url="{{ route('visitantes.form_editar', ['id' => $visitanteId, 'return_to' => $panelUrl]) }}"
                title="Editar visitante"
                onclick="abrirJanelaModal(this.dataset.editUrl)">
                <i class="bi bi-pencil"></i>
            </button>
            <form action="{{ route('visitantes.destroy', $visitanteId) }}"
                method="POST"
                class="painel-remover-visitante"
                data-visitante-id="{{ $visitanteId }}"
                data-visitante-nome="{{ $nome }}">
                @csrf
                @method('DELETE')
                <button class="taggable-action" type="submit" title="Remover visitante" data-role="remover-visitante">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </div>
</div>
