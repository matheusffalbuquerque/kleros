<h1>Registrar lançamento</h1>
<form action="{{ route('financeiro.lancamentos.store') }}" method="post" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="caixa_id" value="{{ $caixa->id }}">
    <div class="form-control">
        <div class="form-item">
            <label>Caixa</label>
            <input type="text" value="{{ $caixa->nome }}" disabled>
        </div>
        <div class="form-item">
            <label for="tipo_lancamento_id">Tipo de lançamento</label>
            <select name="tipo_lancamento_id" id="tipo_lancamento_id">
                <option value="">Selecione (opcional)</option>
                @foreach($tiposLancamento as $tipo)
                    <option value="{{ $tipo->id }}" @selected(old('tipo_lancamento_id') == $tipo->id)>{{ $tipo->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-item">
            <label for="valor">Valor</label>
            <input type="number" step="0.01" name="valor" id="valor" value="{{ old('valor') }}" placeholder="0,00" required>
        </div>
        <div class="form-item">
            <label>Tipo</label>
            <div class="form-square">
                <label><input type="radio" name="tipo" value="entrada" @checked(old('tipo', 'entrada') === 'entrada')> Entrada</label>
                <label><input type="radio" name="tipo" value="saida" @checked(old('tipo') === 'saida')> Saída</label>
            </div>
        </div>
        <div class="form-item">
            <label for="data_lancamento">Data</label>
            <input type="date" name="data_lancamento" id="data_lancamento" value="{{ old('data_lancamento', now()->toDateString()) }}">
        </div>
        <div class="form-item">
            <label for="descricao">Descrição</label>
            <textarea name="descricao" id="descricao" rows="3" placeholder="Observações do lançamento">{{ old('descricao') }}</textarea>
        </div>
        <div class="form-item">
            <label for="anexo">
                <p>Anexar comprovante</p>
                <small>Formatos aceitos: PDF, JPG ou PNG (até 5 MB).</small>
            </label>
            <div class="file-upload-control">
                <div class="file-upload-preview">
                    <i class="bi bi-file-earmark-arrow-up"></i>
                    <span id="anexo-filename" data-file-initial="Nenhum arquivo selecionado">Nenhum arquivo selecionado</span>
                </div>
                <label class="btn">
                    <i class="bi bi-upload"></i> Selecionar arquivo
                    <input type="file" name="anexo" id="anexo" class="hidden" accept=".pdf,image/png,image/jpeg" data-file-display="#anexo-filename">
                </label>
            </div>
        </div>
        <div class="form-options">
            <button type="submit" class="btn"><i class="bi bi-plus-circle"></i> Registrar</button>
            <button type="button" class="btn" onclick="fecharJanelaModal()"><i class="bi bi-x-circle"></i> Cancelar</button>
        </div>
    </div>
</form>
