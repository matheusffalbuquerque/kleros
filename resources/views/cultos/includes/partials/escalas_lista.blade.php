@php
    $escalas = isset($escalas) ? $escalas : collect();
@endphp

@if($escalas->isEmpty())
    <div class="card">
        <p><i class="bi bi-info-circle"></i> Nenhuma escala cadastrada para este culto.</p>
    </div>
@else
    <div class="list">
        <div class="list-title">
            <div class="item-1"><b>Tipo</b></div>
            <div class="item-2"><b>Detalhes</b></div>
            <div class="item-1"><b>Ações</b></div>
        </div>
        @foreach($escalas as $escala)
            <div class="list-item">
                <div class="item item-1">
                    <p>{{ optional($escala->tipo)->nome ?? 'Sem tipo' }}</p>
                    <small class="hint">
                        @if($escala->data_hora)
                            {{ $escala->data_hora->format('d/m/Y H:i') }}
                        @else
                            Data não definida
                        @endif
                        @if($escala->local)
                            • {{ $escala->local }}
                        @endif
                    </small>
                </div>
                <div class="item item-2">
                    @if($escala->observacoes)
                        <p>{{ $escala->observacoes }}</p>
                    @endif
                    <ul class="hint">
                        @foreach($escala->itens as $item)
                            <li>
                                <strong>{{ $item->funcao }}</strong>
                                @if($item->membro)
                                    — {{ $item->membro->nome }}
                                @elseif($item->responsavel_externo)
                                    — {{ $item->responsavel_externo }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="item item-1">
                    <div class="form-options column">
                        <button type="button" class="btn" onclick="abrirJanelaModal('{{ route('escalas.form_editar', $escala->id) }}')"><i class="bi bi-pencil-square"></i> Editar</button>
                        <button type="button"
                            class="btn danger"
                            data-escala-delete
                            data-delete-url="{{ route('escalas.destroy', $escala->id) }}"
                            data-culto-id="{{ $escala->culto_id }}"
                            data-confirm-message="Deseja excluir esta escala?">
                            <i class="bi bi-trash"></i> Excluir
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
