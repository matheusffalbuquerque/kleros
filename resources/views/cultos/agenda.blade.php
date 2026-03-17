@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')

<div class="container">
    <h1>Próximos cultos</h1>
    <div class="info nao-imprimir">
        <h3>Pesquisar</h3>
        <div class="search-panel">
            <div class="search-panel-item">
                <label>Preletor: </label>
                <select name="" id="preletor">
                    <option value="">Todos</option>
                    @if (isset($preletores) && $preletores->isNotEmpty())
                        @foreach ($preletores as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    @else
                        <option value="" disabled>Nenhum cadastro de preletores.</option>
                    @endif
                </select>
            </div>
            <div class="search-panel-item">
                <label>Evento: </label>
                <select name="" id="evento">
                    <option value="">Todos</option>
                    @if (isset($eventosFiltro) && $eventosFiltro->isNotEmpty())
                        @foreach ($eventosFiltro as $evento)
                        <option value="{{ $evento->id }}">{{ $evento->titulo }}</option>
                        @endforeach
                    @else
                        <option value="" disabled>Nenhum evento cadastrado</option>
                    @endif
                </select>
            </div>
            <div class="search-panel-item">
                <button class="" id="btn_filtrar"><i class="bi bi-search"></i> Procurar</button>
                <a onclick="abrirJanelaModal('{{route('cultos.form_criar')}}')"><button><i class="bi bi-plus-circle"></i> Adicionar</button></a>
                <button class="options-menu__trigger" type="button" data-options-target="cultosAgendaOptions"><i class="bi bi-three-dots-vertical"></i> Opções</button>
            </div>
        </div>
        <div class="options-menu" id="cultosAgendaOptions" hidden>
            <button type="button" class="btn" data-action="redirect" data-url="{{ route('cultos.historico') }}"><i class="bi bi-card-list"></i> Histórico</button>
            <button type="button" class="btn" data-action="print"><i class="bi bi-printer"></i> Imprimir</button>
            <button type="button" class="btn" data-action="back"><i class="bi bi-arrow-return-left"></i> Voltar</button>
        </div>
    </div>
    <div class="list">
        @if ($cultos)
            <div class="list-title">
                <div class="item-15">
                    <b>Data do Culto</b>
                </div>
                <div class="item-15">
                    <b>Preletor</b>
                </div>
                <div class="item-15">
                    <b>Evento associado</b>
                </div>
            </div><!--list-item-->
            <div id="content">
                @foreach ($cultos as $item)
                    <div onclick="abrirJanelaModal('{{route('cultos.form_editar', $item->id)}}')" class="list-item">
                        <div class="item item-15">
                            <p><i class="bi bi-bell"></i> {{ formatarData($item->data_culto) }}</p>
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
                @if($cultos->total() > 10)
                    <div class="pagination">
                        {{ $cultos->links('pagination::default') }}
                    </div>
                @endif
            </div><!--content-->
        @else
            <div class="card">
                <p><i class="bi bi-exclamation-triangle"></i> Ainda não há cultos previstos para exibição.</p>
            </div>
        @endif
    </div> 
</div>

@endsection

@push('scripts')

<script>
    $(document).ready(function(){
        $('#btn_filtrar').click(function(){
            const _token = $('meta[name="csrf-token"]').attr('content');
            const origin = 'agenda';
            let preletor = $('#preletor').val();
            let evento = $('#evento').val();
            
            $.post('/cultos/search', { _token, origin, preletor, evento }, function(response){
                
                var view = response.view
                
                $('#content').html(view)

            }).catch((err) => {console.log(err)})

        });

    })
</script>

    
@endpush
