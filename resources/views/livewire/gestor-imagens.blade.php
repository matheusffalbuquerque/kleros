<div>
    <h1>Gestor de Imagens</h1>
    
    <div class="acervo">
        @foreach($arquivos as $arquivo)
        <div class="card-arquivo {{ $selectedId === $arquivo->id ? 'selected' : '' }}" 
             wire:click="selectImage({{ $arquivo->id }})">
            <img src="{{ asset('storage/' . $arquivo->caminho) }}" alt="{{ $arquivo->nome }}">
            <div class="conteudo-arquivo">
                <span class="titulo">{{ $arquivo->nome }}</span>
                <div class="options">
                    <a href="#" class="botao delete-img" title="Excluir">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="center w100">
        <button 
            class="btn {{ $selectedId ? '' : 'inactive' }}" 
            wire:click="confirmSelection" 
            {{ $selectedId ? '' : 'disabled' }}>
            <span><i class="bi bi-check2"></i> Selecionar</span>
        </button>
        <label class="btn" for="upload-img">
            <i class="bi bi-upload"></i> Upload
        </label>
        <input type="file" name="upload" id="upload-img" class="hidden">
    </div>

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
</div>

@script
<script>
    // Este código é executado automaticamente pelo Livewire
    $wire.on('imagemSelecionada', (event) => {
        const dados = event[0];
        console.log('Imagem selecionada via Livewire:', dados);
        
        // Envia mensagem para a página pai
        window.parent.postMessage({
            type: 'imagemSelecionada',
            arquivoId: dados.id,
            arquivoUrl: dados.url
        }, '*');
    });
    
    $wire.on('fecharModal', () => {
        if (typeof window.parent.fecharJanelaModal === 'function') {
            window.parent.fecharJanelaModal();
        }
    });
</script>
@endscript

