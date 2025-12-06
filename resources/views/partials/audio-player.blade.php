{{-- Audio Player Global - Persistente entre navegações --}}
<div id="global-audio-player" style="display: none;">
    <div class="podcast-menu-bar">
        <div class="podcast-player-container">
            <audio id="podcast-player" controls preload="metadata" playsinline>
                Seu navegador não suporta o elemento de áudio.
            </audio>
        </div>

        <!-- Menu com título e ícones -->
        <ul class="podcast-menu">
            <li><span class="podcast-title" id="podcast-title">Selecione um episódio...</span></li>
            <li><a href="#" class="play-btn" title="Compartilhar"><i class="bi bi-share"></i></a></li>
            <li><a href="#" class="play-btn" title="Favoritar"><i class="bi bi-heart"></i></a></li>
        </ul>
    </div>
</div>

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
    
    .podcast-player-container .plyr--full-ui input[type=range] {
        color: var(--primary-color);
    }
    
    .podcast-player-container .plyr__control--overlaid {
        display: none;
    }
    
    .podcast-player-container audio {
        flex: 1;
        min-width: 350px;
        max-width: 500px;
        height: 48px;
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

@push('scripts')
<script>
(function() {
    const playerContainer = document.getElementById('global-audio-player');
    const audioElement = document.getElementById('podcast-player');
    const titleElement = document.getElementById('podcast-title');
    let player = null;
    let initialized = false;
    let isOnPodcastPage = false;
    
    console.log('🎵 Audio Player Global carregado');
    
    // Verifica se está na página de podcasts
    function checkPodcastPage() {
        isOnPodcastPage = window.location.pathname.includes('/podcasts');
        updatePlayerVisibility();
    }
    
    // Atualiza visibilidade do player
    function updatePlayerVisibility() {
        const isPlaying = !audioElement.paused;
        const shouldShow = isOnPodcastPage || isPlaying;
        
        if (shouldShow) {
            playerContainer.style.display = 'block';
        } else {
            playerContainer.style.display = 'none';
        }
    }
    
    // Inicializa Plyr
    function initPlayer() {
        if (initialized || !audioElement) return;
        
        if (typeof Plyr !== 'undefined') {
            try {
                console.log('🔄 Inicializando Plyr...');
                player = new Plyr('#podcast-player', {
                    controls: ['play', 'progress', 'current-time', 'duration', 'mute', 'volume'],
                    hideControls: false
                });
                
                initialized = true;
                console.log('✅ Plyr inicializado');
                
                player.on('play', () => {
                    console.log('▶️ Áudio iniciado');
                    updatePlayerVisibility();
                });
                
                player.on('pause', () => {
                    console.log('⏸️ Áudio pausado');
                });
                
                player.on('ended', () => {
                    console.log('⏹️ Áudio finalizado');
                    updatePlayerVisibility();
                });
            } catch(e) {
                console.error('❌ Erro ao inicializar Plyr:', e);
            }
        } else {
            console.warn('⚠️ Plyr não disponível, usando player nativo');
            
            // Eventos para player nativo
            audioElement.addEventListener('play', updatePlayerVisibility);
            audioElement.addEventListener('pause', updatePlayerVisibility);
            audioElement.addEventListener('ended', updatePlayerVisibility);
        }
    }
    
    // Aguarda Plyr estar disponível
    setTimeout(() => {
        initPlayer();
        checkPodcastPage();
    }, 500);
    
    // Escuta mudanças de página (para SPAs ou navegação dinâmica)
    window.addEventListener('popstate', checkPodcastPage);
    
    // Escuta evento global para tocar áudio
    window.addEventListener('playAudio', (event) => {
        console.log('🎵 Evento playAudio recebido!', event.detail);
        
        const { audioUrl, title } = event.detail;
        
        if (!audioUrl) {
            console.error('❌ URL do áudio não fornecida');
            return;
        }
        
        console.log('📻 URL:', audioUrl);
        console.log('🏷️ Título:', title);
        
        // Atualiza título
        if (titleElement && title) {
            titleElement.textContent = title;
        }
        
        // Mostra player
        playerContainer.style.display = 'block';
        
        // Toca áudio
        setTimeout(() => {
            if (player && initialized) {
                console.log('🎵 Usando Plyr');
                player.source = {
                    type: 'audio',
                    sources: [{ src: audioUrl }]
                };
                setTimeout(() => {
                    player.play().then(() => {
                        console.log('▶️ Reproduzindo com Plyr');
                    }).catch(err => {
                        console.error('❌ Erro:', err);
                    });
                }, 200);
            } else {
                console.log('🎵 Usando player nativo');
                audioElement.src = audioUrl;
                audioElement.play().then(() => {
                    console.log('▶️ Reproduzindo com player nativo');
                }).catch(err => {
                    console.error('❌ Erro:', err);
                });
            }
        }, 300);
    });
    
    console.log('✅ Event listener registrado');
})();
</script>
@endpush
