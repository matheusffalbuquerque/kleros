<?php

namespace App\Livewire;

use Livewire\Component;

class AudioPlayer extends Component
{
    public $isPlaying = false;
    public $currentAudio = '';
    public $currentTitle = 'Selecione um episódio...';

    protected $listeners = ['playAudio'];

    public function playAudio($audioUrl, $title)
    {
        $this->currentAudio = $audioUrl;
        $this->currentTitle = $title;
        $this->isPlaying = true;
    }

    public function stopAudio()
    {
        $this->isPlaying = false;
    }

    public function render()
    {
        return view('livewire.audio-player');
    }
}
