<div id="audio-player-component-{{ $this->getId() }}" wire:ignore.self>
    @php
        $isOnPodcastPage = request()->routeIs('podcasts.painel');
        $shouldShow = $isOnPodcastPage || $isPlaying;
    @endphp

    @if($shouldShow)
    <div class="podcast-menu-bar" wire:ignore>
        <div class="podcast-player-container">
            <audio id="podcast-player-{{ $this->getId() }}" controls preload="metadata" playsinline>
                Seu navegador não suporta o elemento de áudio.
            </audio>
            <span class="podcast-title" id="podcast-title-{{ $this->getId() }}">{{ $currentTitle }}</span>
        </div>

        <!-- Menu -->
        <ul class="podcast-menu">
            <li><a href="#" class="play-btn"><i class="bi bi-share"></i></a></li>
            <li><a href="#" class="play-btn"><i class="bi bi-heart"></i></a></li>
        </ul>
    </div>
    @endif

    <style>
        .podcast-menu-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            display: flex;
            align-items: center;
            background: var(--secondary-color);
            justify-content: center;
            padding: 6px 12px;
            gap: 20px;
            z-index: 998;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);
        }

        .podcast-player-container {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            max-width: 600px;
        }

        .podcast-player-container .plyr {
            flex: 1;
            min-width: 350px;
            --plyr-color-main: var(--primary-color);
            --plyr-audio-controls-background: var(--secondary-color);
            --plyr-audio-control-color: var(--secondary-contrast);
        }
        
        .podcast-player-container .plyr--audio .plyr__controls {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 10px;
        }
        
        /* Garantir que o audio element fique visível até o Plyr inicializar */
        .podcast-player-container audio {
            flex: 1;
            min-width: 350px;
            max-width: 500px;
            height: 48px;
        }
        
        .podcast-player-container .plyr--audio .plyr__controls {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 10px;
        }
        
        .podcast-player-container .plyr--full-ui input[type=range] {
            color: var(--primary-color);
        }
        
        .podcast-player-container .plyr__control--overlaid {
            display: none;
        }

        .podcast-title {
            font-size: 0.7rem;
            color: #94a3b8;
            max-width: 140px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .podcast-menu {
            display: flex;
            gap: 15px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .podcast-menu li a {
            text-decoration: none;
            color: #e2e8f0;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .podcast-menu li a:hover {
            color: #38bdf8;
        }
    </style>

    <script>
    (function() {
        const componentId = '{{ $this->getId() }}';
        const playerId = 'podcast-player-' + componentId;
        let player = null;
        let initialized = false;
        
        console.log('🎵 Audio Player Component carregado. ID:', componentId);
        
        function initPlayer() {
            if (initialized) {
                console.log('⚠️ Player já inicializado');
                return;
            }
            
            const playerEl = document.getElementById(playerId);
            
            if (!playerEl) {
                console.error('❌ Elemento do player não encontrado:', playerId);
                return;
            }
            
            console.log('✅ Elemento do player encontrado:', playerId);
            
            if (typeof Plyr !== 'undefined') {
                try {
                    console.log('🔄 Inicializando Plyr...');
                    
                    player = new Plyr('#' + playerId, {
                        controls: ['play', 'progress', 'current-time', 'duration', 'mute', 'volume'],
                        hideControls: false,
                        loadSprite: true,
                        iconUrl: 'https://cdn.plyr.io/3.7.8/plyr.svg'
                    });
                    
                    initialized = true;
                    
                    player.on('ready', () => {
                        console.log('✅ Player Plyr pronto!');
                    });
                    
                    player.on('play', () => {
                        console.log('▶️ Áudio iniciado');
                    });
                    
                    player.on('ended', () => {
                        console.log('⏹️ Áudio finalizado');
                        try {
                            if (typeof Livewire !== 'undefined') {
                                const component = Livewire.find(componentId);
                                if (component) {
                                    component.call('stopAudio');
                                }
                            }
                        } catch(e) {
                            console.error('Erro ao chamar stopAudio:', e);
                        }
                    });
                } catch(e) {
                    console.error('❌ Erro ao inicializar Plyr:', e);
                    initialized = false;
                }
            } else {
                console.warn('⚠️ Plyr não encontrado. Usando player nativo.');
            }
        }

        // Aguarda o Plyr estar disponível
        let attempts = 0;
        const maxAttempts = 50;
        const checkPlyr = setInterval(() => {
            attempts++;
            if (typeof Plyr !== 'undefined' && document.getElementById(playerId)) {
                clearInterval(checkPlyr);
                console.log('✅ Plyr disponível, inicializando...');
                initPlayer();
            } else if (attempts >= maxAttempts) {
                clearInterval(checkPlyr);
                console.error('❌ Timeout: Plyr ou elemento não encontrado após 5s');
            }
        }, 100);

        // Escuta evento global para tocar áudio
        window.addEventListener('playAudio', (event) => {
            console.log('🎵 Evento playAudio recebido!', event.detail);
            
            const { audioUrl, title } = event.detail;
            
            if (!audioUrl) {
                console.error('❌ URL do áudio não fornecida');
                return;
            }
            
            console.log('📻 URL do áudio:', audioUrl);
            console.log('🏷️ Título:', title);
            
            // Atualiza o título imediatamente no DOM
            const titleEl = document.getElementById('podcast-title-' + componentId);
            if (titleEl && title) {
                titleEl.textContent = title;
                console.log('✅ Título atualizado no DOM');
            }
            
            try {
                if (typeof Livewire !== 'undefined') {
                    const component = Livewire.find(componentId);
                    if (component) {
                        console.log('🔄 Atualizando componente Livewire...');
                        component.call('playAudio', audioUrl, title);
                        
                        setTimeout(() => {
                            const audioElement = document.getElementById(playerId);
                            
                            if (!audioElement) {
                                console.error('❌ Elemento de áudio não encontrado');
                                return;
                            }
                            
                            if (player && initialized) {
                                console.log('🎵 Usando Plyr para tocar');
                                player.source = {
                                    type: 'audio',
                                    sources: [{ src: audioUrl }]
                                };
                                setTimeout(() => {
                                    player.play().then(() => {
                                        console.log('▶️ Reproduzindo com Plyr:', audioUrl);
                                    }).catch(err => {
                                        console.error('❌ Erro ao reproduzir com Plyr:', err);
                                    });
                                }, 300);
                            } else {
                                console.log('🎵 Usando player nativo');
                                audioElement.src = audioUrl;
                                audioElement.play().then(() => {
                                    console.log('▶️ Reproduzindo com player nativo:', audioUrl);
                                }).catch(err => {
                                    console.error('❌ Erro ao reproduzir:', err);
                                });
                            }
                        }, 800);
                    } else {
                        console.error('❌ Componente Livewire não encontrado');
                    }
                } else {
                    console.error('❌ Livewire não disponível');
                }
            } catch(e) {
                console.error('❌ Erro ao processar playAudio:', e);
            }
        });
        
        console.log('✅ Event listener "playAudio" registrado');
    })();
    </script>
</div>
