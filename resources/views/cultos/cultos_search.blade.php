@if ($cultos)
    @if ($origin == 'historico')
        @foreach ($cultos as $item)
        <div class="list-item" onclick="abrirJanelaModal('{{ route('cultos.form_editar', $item->id) }}')">
            <div class="item item-1">
                <p>{{ formatarData($item->data_culto) }}</p>
            </div>
            <div class="item item-15">
                <p>{{ $item->preletor_label }}</p>
            </div>
            <div class="item item-1">
                <p>{{$item->quant_visitantes}}</p>
            </div>
            <div class="item item-1">
                <p>@if ($item->evento)
                        {{$item->evento->titulo}}
                    @else Nenhum @endif
                </p>
            </div>
        </div><!--list-item-->
        @endforeach  
    @elseif ($origin == 'agenda')
        @foreach ($cultos as $item)
        <div class="list-item" onclick="abrirJanelaModal('{{ route('cultos.form_editar', $item->id) }}')">
                <div class="item item-15">
                    <p>{{ formatarData($item->data_culto) }}</p>
                </div>
                <div class="item item-15">
                    <p>{{ $item->preletor_label }}</p>
                </div>
                <div class="item item-15">
                    <p>@if ($item->evento)
                            {{$item->evento->titulo}}
                        @else Nenhum @endif
                    </p>
                </div>
            </div><!--list-item-->
        @endforeach
    @endif

@else
    <div class="card">
        <p><i class="bi bi-exclamation-triangle"></i> Nenhum culto retornado para esta pesquisa.</p>
    </div>
@endif
