<h1>Categoria de Culto</h1>
<div class="info">
    <form action="{{ route('cultos.categorias.store') }}" method="post">
        @csrf
        <div class="tabs">
            <ul class="tab-menu">
                <li class="active" data-tab="categoria-dados"><i class="bi bi-tag"></i> Nova categoria</li>
            </ul>
            <div class="tab-content card">
                <div id="categoria-dados" class="tab-pane form-control active">
                    <div class="form-item">
                        <label for="nome_categoria">Nome</label>
                        <input type="text" name="nome" id="nome_categoria" placeholder="Nome da categoria" required>
                    </div>
                    <div class="form-item">
                        <label for="descricao_categoria">Descrição (opcional)</label>
                        <input type="text" name="descricao" id="descricao_categoria" placeholder="Descrição da categoria">
                    </div>
                </div>
            </div>
            <div class="form-options center">
                <button type="submit" class="btn"><i class="bi bi-plus-circle"></i> Adicionar</button>
                <button type="button" class="btn" onclick="fecharJanelaModal()"><i class="bi bi-x-circle"></i> Cancelar</button>
            </div>
        </div>
    </form>
</div>
