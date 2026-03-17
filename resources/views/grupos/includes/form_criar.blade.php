@php
    $setores = isset($setores) ? $setores : collect();
    $agrupamentoConfig = optional(optional($congregacao)->config)->agrupamentos;
    $mostrarSelecaoSetor = $agrupamentoConfig === 'setor';
    $setorSelecionado = old('setor_id');
@endphp

<div class="container">
    <h1>Novo Grupo</h1>
    <form action="/grupos" method="post">
        @csrf
        <div class="form-control">
            <div class="form-item">
                <label for="nome">Nome do grupo: </label>
                <input type="text" name="nome" placeholder="Nome do grupo" value="{{ old('nome') }}" required>
            </div>
            <div class="form-item">
                <label for="descricao">Descrição do grupo: </label>
                <input type="text" name="descricao" placeholder="Descrição do grupo" value="{{ old('descricao') }}">
            </div>
            <div class="form-item">
                <label for="descricao">Líder: </label>
                <select name="lider_id" id="" required>
                    <option value="">Selecione um membro: </option>
                    @foreach ($membros as $item)
                        <option value="{{$item->id}}" @selected(old('lider_id') == $item->id)>{{$item->nome}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-item">
                <label for="descricao">Co-líder: </label>
                <select name="colider_id" id="">
                    <option value="">Selecione um membro: </option>
                    @foreach ($membros as $item)
                        <option value="{{$item->id}}" @selected(old('colider_id') == $item->id)>{{$item->nome}}</option>
                    @endforeach
                </select>
            </div>
            
            @if($mostrarSelecaoSetor)
            <div class="form-item">
                <label for="setor_id">Setor vinculado:</label>
                <select name="setor_id" id="setor_id" class="select2" data-placeholder="Selecione um setor">
                    <option value="">Nenhum setor</option>
                    @foreach($setores as $setor)
                        <option value="{{ $setor->id }}" @selected($setorSelecionado == $setor->id)>{{ $setor->nome }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="form-options">
                <button class="btn" type="submit"><i class="bi bi-plus-circle"></i> Adicionar Grupo</button>
                <a href="/cadastros#grupos"><button type="button" class="btn"><i class="bi bi-x-circle"></i> Cancelar</button></a>
            </div>
        </div>
    </form>
</div>
