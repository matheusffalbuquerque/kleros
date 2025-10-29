<?php

namespace App\Livewire;

use App\Models\Arquivo;
use Livewire\Component;

class GestorImagens extends Component
{
    public $selectedId = null;
    public $arquivos = [];
    
    public function mount()
    {
        $this->loadImages();
    }
    
    public function loadImages()
    {
        $this->arquivos = Arquivo::where('congregacao_id', app('congregacao')->id)
            ->where('tipo', 'imagem')
            ->get();
    }
    
    public function selectImage($arquivoId)
    {
        if ($this->selectedId === $arquivoId) {
            $this->selectedId = null; // Desseleciona se clicar no mesmo
        } else {
            $this->selectedId = $arquivoId;
        }
    }
    
    public function confirmSelection()
    {
        if ($this->selectedId) {
            $arquivo = Arquivo::find($this->selectedId);
            
            if ($arquivo) {
                // Emite evento JavaScript personalizado
                $this->dispatch('imagemSelecionada', [
                    'id' => $arquivo->id,
                    'url' => asset('storage/' . $arquivo->caminho),
                    'nome' => $arquivo->nome
                ]);
                
                // Se estiver em modal, fecha
                $this->dispatch('fecharModal');
            }
        }
    }
    
    public function render()
    {
        return view('livewire.gestor-imagens');
    }
}
