<h1>Novo conteúdo pastoral</h1>
<form action="{{ route('areapastoral.store') }}" method="post">
    @csrf
    <div class="form-control">
        <div class="form-item">
            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo" value="{{ old('titulo') }}" required>
        </div>
        <div class="form-item">
            <label for="tipo_conteudo">Tipo de conteúdo</label>
            <select name="tipo_conteudo" id="tipo_conteudo" required>
                @foreach ($tiposConteudo as $valor => $label)
                    <option value="{{ $valor }}" @selected(old('tipo_conteudo') === $valor)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-item">
            <label for="status">Status</label>
            <select name="status" id="status" required>
                <option value="rascunho" @selected(old('status') === 'rascunho')>Rascunho</option>
                <option value="publicado" @selected(old('status', 'publicado') === 'publicado')>Publicado</option>
            </select>
        </div>
        <div class="form-item">
            <label for="publicado_em">Data de publicação (opcional)</label>
            <input type="datetime-local" name="publicado_em" id="publicado_em" value="{{ old('publicado_em') }}">
        </div>
        <div class="form-item">
            <label for="resumo">Resumo</label>
            <textarea name="resumo" id="resumo" rows="3" placeholder="Resumo curto sobre o conteúdo (opcional)">{{ old('resumo') }}</textarea>
        </div>
        <div class="form-item">
            <label for="conteudo">Conteúdo</label>
            <textarea name="conteudo" id="conteudo" rows="8" placeholder="Escreva ou cole o conteúdo principal">{{ old('conteudo') }}</textarea>
        </div>
        <div class="form-item">
            <label for="link_externo">Link externo</label>
            <input type="url" id="link_externo" name="link_externo" placeholder="https://..." value="{{ old('link_externo') }}">
        </div>
        <div class="form-item">
            <label for="video_url">URL de vídeo</label>
            <input type="url" id="video_url" name="video_url" placeholder="https://..." value="{{ old('video_url') }}">
        </div>
        <div class="form-item">
            <label for="arquivo_principal">Arquivo principal</label>
            <input type="text" id="arquivo_principal" name="arquivo_principal" placeholder="Caminho ou URL do arquivo" value="{{ old('arquivo_principal') }}">
        </div>
        <div class="form-item">
            <label for="imagem_capa">Imagem de capa</label>
            <input type="text" id="imagem_capa" name="imagem_capa" placeholder="Caminho ou URL da imagem" value="{{ old('imagem_capa') }}">
        </div>

        <div class="form-options">
            <button type="submit" class="btn"><i class="bi bi-save"></i> Salvar</button>
            <button type="button" class="btn" onclick="fecharJanelaModal()"><i class="bi bi-x-circle"></i> Cancelar</button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    (function () {
        const selectTipo = document.getElementById('tipo_conteudo');
        const campoLink = document.getElementById('link_externo').closest('.form-item');
        const campoArquivo = document.getElementById('arquivo_principal').closest('.form-item');
        const campoVideo = document.getElementById('video_url').closest('.form-item');

        const toggleCampos = () => {
            const tipo = selectTipo.value;
            campoLink.hidden = !['link', 'video'].includes(tipo);
            campoVideo.hidden = tipo !== 'video';
            campoArquivo.hidden = !['ebook', 'apostila'].includes(tipo);
        };

        selectTipo.addEventListener('change', toggleCampos);
        toggleCampos();
    })();
</script>
@endpush
