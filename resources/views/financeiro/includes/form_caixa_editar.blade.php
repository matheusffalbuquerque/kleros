<h1>Editar caixa</h1>
<form action="{{ route('financeiro.caixas.update', $caixa->id) }}" method="post">
    @csrf
    @method('PUT')
    <div class="form-control">
        <div class="form-item">
            <label for="nome">Nome do caixa</label>
            <input type="text" name="nome" id="nome" value="{{ old('nome', $caixa->nome) }}" required>
        </div>
        <div class="form-item">
            <label for="descricao">Descrição</label>
            <textarea name="descricao" id="descricao" rows="3">{{ old('descricao', $caixa->descricao) }}</textarea>
        </div>
        <div class="form-item">
            <label for="agrupamento_id">Agrupamento</label>
            <select name="agrupamento_id" id="agrupamento_id" class="select2" data-placeholder="Selecione um agrupamento">
                <option value="">Sem agrupamento</option>
                @foreach($agrupamentos as $agrupamento)
                    <option value="{{ $agrupamento->id }}" @selected(old('agrupamento_id', $caixa->agrupamento_id) == $agrupamento->id)>
                        {{ $agrupamento->nome }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-item">
            <label for="responsaveis">Responsáveis pelo caixa</label>
            <select name="responsaveis[]" id="responsaveis" class="select2 select2-membros" data-placeholder="Selecione os responsáveis" multiple>
                @foreach($membros as $membro)
                    <option value="{{ $membro->id }}" @selected(collect(old('responsaveis', $caixa->responsaveis ?? []))->contains($membro->id))>
                        {{ $membro->nome }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-options">
            <button type="submit" class="btn"><i class="bi bi-arrow-clockwise"></i> Atualizar caixa</button>
            <button type="button" class="btn" onclick="fecharJanelaModal()"><i class="bi bi-x-circle"></i> Cancelar</button>
        </div>
    </div>
</form>
