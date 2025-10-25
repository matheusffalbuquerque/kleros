<h1>Gestor de Imagens</h1>
<form action="/arquivos" enctype="multipart/form-data" method="post" id="form-gestor-imagens">
    @csrf
    <div class="acervo" id="acervo-imagens">
        @foreach ($arquivos as $item)
        <div class="card-arquivo" data-arquivo-id="{{$item->id}}" data-arquivo-url="{{ asset('storage/'.$item->caminho) }}">
            <img src="{{ asset('storage/'.$item->caminho) }}" alt="{{$item->nome}}">
            <div class="conteudo-arquivo">
                <span class="titulo">{{$item->nome}}</span>
                <div class="options">
                    <a href="#" class="botao delete-img" id="{{$item->id}}" title="Excluir"><i class="bi bi-trash"></i></a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="center w100">
        <button class="btn inactive" id="btn-selecionar-imagem" type="button" disabled><span><i class="bi bi-check2"></i> Selecionar</span></button>
        <label class="btn" for="upload-img"><i class="bi bi-upload"></i> Upload</label>
        <input type="file" name="upload" id="upload-img" class="hidden">
    </div>
</form>

<style>
    .card-arquivo {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .card-arquivo:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .card-arquivo.selected {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        border: 2px solid var(--secondary-color);
    }
    
    .btn.inactive {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .btn:not(.inactive) {
        opacity: 1;
        cursor: pointer;
    }
</style>

