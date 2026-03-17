<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>
        <link rel="shortcut icon" href="/storage/{{$congregacao->config->logo_caminho}}" type="image/x-icon">
        <link rel="manifest" href="{{ route('pwa.manifest') }}">
        <meta name="theme-color" content="{{ $congregacao->config->conjunto_cores['primaria'] ?? '#677b96' }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Teko" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Roboto" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Oswald" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Saira" rel="stylesheet">
        
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        
        <!-- CSS Estilo Geral-->
        @vite(['resources/css/app.scss', 'resources/js/app.js'])

        <!-- CSS do Select2 -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-dropdown,
            .select2-container--default .select2-results__option,
            .select2-container--default .select2-selection__rendered {
                color: #111 !important;
            }
            .select2-container--default .select2-results__option--highlighted {
                color: #fff !important;
            }
        </style>

        <!-- Swipper para interações -->
        <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

        <!-- Link para o calendário -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css" />

        <!-- Plyr.js para áudios -->
        <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

        <style>
        /* CSS dinâmico injetado aqui */
        :root {
            
            --primary-color: {{$congregacao->config->conjunto_cores['primaria'] ?? '#677b96'}};
            --secondary-color: {{$congregacao->config->conjunto_cores['secundaria'] ?? '#0a1929'}};
            --terciary-color: {{$congregacao->config->conjunto_cores['terciaria'] ?? '#f44916'}};
            --primary-contrast: {{ getContrastTextColor($congregacao->config->conjunto_cores['primaria'])}};
            --secondary-contrast: {{ getContrastTextColor($congregacao->config->conjunto_cores['secundaria'])}};
            --terciary-contrast: {{ getContrastTextColor($congregacao->config->conjunto_cores['terciaria'])}};
            --text-font: {{$congregacao->config->font_family}};

            --background-color: {{$congregacao->config->tema->propriedades['cor-fundo']}};
            --background-contrast: {{ getContrastTextColor($congregacao->config->tema->propriedades['cor-fundo'])}};
            --text-color: {{$congregacao->config->tema->propriedades['cor-texto']}};
            --border-style: {{$congregacao->config->tema->propriedades['borda']}}
        }
        </style>
        @stack('styles')
        @livewireStyles
    </head>
    <body>
        <div class="layout-wrapper">
            <div class="popup">
                <h2><i class="bi bi-exclamation-triangle"></i> Aviso</h2>
                <p id="msg_content"></p>
                <button id="confirmaBtn"><i class="bi bi-check"></i> Confirmar</button>
                <button id="cancelaBtn"><i class="bi bi-x"></i> Cancelar</button> 
            </div>
            <header class="nao-imprimir">
                <nav class="main-navbar">
                    <div class="nav-logo">
                        <img src="/storage/{{$congregacao->config->logo_caminho}}" alt="{{$congregacao->denominacao->nome}} Logo">
                    </div>
                    <div class="nav-menu">
                        <ul id="menu-express">
                            @if(auth()->check() && auth()->user()->hasAnyRole(['gestor', 'admin', 'kleros']))
                                <a href="{{ route('index') }}"><li><i class="bi bi-kanban"></i> Controle</li></a>
                                <a href="{{ route('membros.painel') }}"><li><i class="bi bi-people"></i> Membros</li></a>
                                <a href="{{ route('cadastros.index') }}"><li><i class="bi bi-journals"></i> Cadastros</li></a>
                                <a href="{{ route('cultos.painel') }}"><li><i class="bi bi-bell"></i> Culto do dia</li></a>
                                <a href="{{ route('programacoes.painel') }}"><li><i class="bi bi-collection"></i> Programações</li></a>
                            @else
                                <a href="{{ route('index') }}"><li><i class="bi bi-kanban"></i> Controle</li></a>
                                <a href="{{ route('agenda.read') }}"><li><i class="bi bi-calendar3"></i> Agenda</li></a>
                                <a href="{{ route('noticias.painel') }}"><li><i class="bi bi-newspaper"></i> Notícias</li></a>
                                <a href="{{ route('programacoes.painel') }}"><li><i class="bi bi-collection"></i> Programações</li></a>
                            @endif
                        </ul>
                    </div>
                    <div class="login_info">
                        <div class="menu-mobile-container">
                            <button class="menu-mobile-btn" id="menuMobileBtn" title="Menu" aria-controls="menuMobileDropdown" aria-expanded="false">
                                <i class="bi bi-grid"></i>
                            </button>
                            <div class="menu-mobile-dropdown" id="menuMobileDropdown" role="menu" aria-label="Menu rápido">
                                <nav class="menu-mobile-content">
                                    <ul id="menuMobileList"></ul>
                                    <ul class="menu-mobile-extra">
                                        <li class="menu-mobile-logout"><a href="/logout"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        
                        <div class="notificacao-container">
                            <!-- Botão -->
                            <button class="notificacao-btn" id="notificacaoBtn" aria-controls="notificacaoDropdown" title="Notificações">
                                <i class="bi bi-bell-fill"></i>
                                <span class="notif-badge" id="notifBadge">3</span>
                            </button>
                            <!-- Dropdown -->
                            <div class="notificacao-dropdown" id="notificacaoDropdown" role="menu" aria-label="Notificações">
                                <div class="notif-header">
                                <h4>Notificações</h4>
                                <div class="notif-actions">
                                    <button type="button" id="clearAll">Limpar</button>
                                </div>
                                </div>
                                <div class="notif-list" id="notifList">
                                <!-- Itens de notificação serão inseridos aqui dinamicamente -->
                                </div>

                                <div class="notif-empty" id="notifEmpty" style="display:none;">
                                Sem novas notificações.
                                </div>

                                <div class="notif-footer">
                                <a href="/notificacoes">Ver todas</a>
                                <a href="/preferencias">Preferências</a>
                                </div>
                            </div>
                        </div>

                        @php
                            $profileBalance = null;
                            if (auth()->check()
                                && module_enabled('moedas')
                                && class_exists(\Modules\Moedas\Models\Moeda::class)
                                && class_exists(\Modules\Moedas\Models\Carteira::class)
                            ) {
                                $congregacaoAtual = app()->bound('congregacao') ? app('congregacao') : null;
                                if ($congregacaoAtual) {
                                    $moedaAtiva = \Modules\Moedas\Models\Moeda::query()
                                        ->where('congregacao_id', $congregacaoAtual->id)
                                        ->where('ativo', true)
                                        ->orderBy('nome')
                                        ->first();

                                    if ($moedaAtiva) {
                                        $carteiraUsuario = \Modules\Moedas\Models\Carteira::query()
                                            ->where('moeda_id', $moedaAtiva->id)
                                            ->where('usuario_id', auth()->id())
                                            ->first();

                                        $saldo = $carteiraUsuario?->saldo ?? 0;
                                        $profileBalance = sprintf(
                                            'Saldo (%s): %s %s',
                                            $moedaAtiva->nome,
                                            number_format($saldo, 2, ',', '.'),
                                            $moedaAtiva->simbolo
                                        );
                                    }
                                }
                            }
                        @endphp
                        <div class="profile-container">
                            <img class="avatar" id="profileBtn" src="{{ auth()->user()?->membro?->foto 
                                ? asset('storage/'.auth()->user()->membro->foto) 
                                : asset('storage/images/newuser.png') }}" title="{{optional(auth()->user()->membro)->nome}}" alt="">
                            <div class="profile-dropdown" id="profileDropdown">
                                <div class="profile-info">
                                    <div class="profile-header">
                                        <small>Conectado como</small>
                                    </div>
                                    <p><i class="bi bi-person"></i> {{optional(auth()->user()->membro)->nome ?? 'Admin'}}</p>
                                </div>
                                @if($profileBalance)
                                    <div class="profile-balance">
                                        <i class="bi bi-coin"></i> {{ $profileBalance }}
                                    </div>
                                @endif
                                <a href="/perfil"><i class="bi bi-pencil"></i> Editar perfil</a>
                                <a href="{{ route('favoritos.index') }}"><i class="bi bi-bookmark"></i> Favoritos</a>
                                <a href="#" data-pwa-install><i class="bi bi-phone"></i> Baixar App</a>
                                <a href="/logout" title="Sair"><i class="bi bi-box-arrow-right"></i> Logout</a>
                            </div>
                        </div>
                    </div>
                </nav>
                
            </header>
            <main class="content">
                @if (session('msg'))
                    <div class="msg">
                        <div class="success"><div class="close"><i class="bi bi-x"></i></div><i class="bi bi-check-circle"></i> {{ session('msg') }}</div>
                    </div>
                @endif
                @if (session('msg-error'))
                    <div class="msg">
                        <div class="error"><i class="bi bi-exclamation-diamond"></i> {{ session('msg-error') }}</div>
                    </div>
                @endif
                <nav class="left-navbar nao-imprimir">
                    <div class="menu-btn">
                        @if(auth()->check() && auth()->user()->hasAnyRole(['gestor', 'admin', 'kleros']))
                            <a href="{{route('tutoriais.index')}}"><span title="Tutoriais" id="btn-tutorial"><i class="bi bi-question-octagon"></i></span></a>
                            <a href="{{route('configuracoes.atualizar', $congregacao->id)}}"><span title="Configurações" id="btn-config"><i class="bi bi-gear"></i></span></a>
                        @endif
                        <span title="Menu Principal" id="btn-menu"><i class="bi bi-list"></i></span>
                    </div>
                    <ul class="menu-content">
                        @php
                            $usuarioLogado = auth()->user();
                            $temRolePrincipal = $usuarioLogado?->hasRole('principal');
                            $podeGerirAreaPastoral = $usuarioLogado && $temRolePrincipal && $usuarioLogado->hasAnyRole(['gestor', 'admin', 'kleros']);
                            $areaPastoralRoute = null;
                            if (Route::has('areapastoral.index')) {
                                $areaPastoralRoute = $podeGerirAreaPastoral && Route::has('areapastoral.painel')
                                    ? route('areapastoral.painel')
                                    : route('areapastoral.index');
                            }
                        @endphp
                        <a href="{{route('index')}}"><li><span title="Controle"><i class="bi bi-kanban"></i></span><span>Controle</span></li></a>
                        @if(auth()->check() && auth()->user()->hasAnyRole(['gestor', 'admin', 'kleros']))
                            <a href="{{route('membros.painel')}}"><li><span title="Membros"><i class="bi bi-people"></i></span><span>Membros</span></li></a>
                            <a href="{{route('agenda.index')}}"><li><span title="Agenda"><i class="bi bi-calendar3"></i></span><span>Agenda</span></li></a>
                            <a href="{{route('eventos.agenda')}}"><li><span title="Eventos"><i class="bi bi-calendar-event"></i></span><span>Eventos</span></li></a>
                            <a href="{{route('cultos.painel')}}"><li><span title="Cultos"><i class="bi bi-bell"></i></span><span>Cultos</span></li></a>
                            <a href="{{route('reunioes.painel')}}"><li><span title="Reuniões"><i class="bi bi-people-fill"></i></span><span>Reuniões</span></li></a>
                            <a href="{{route('avisos.painel')}}"><li><span title="Mensagens"><i class="bi bi-envelope"></i></span><span>Mensagens</span></li></a>
                            <a href="{{route('visitantes.historico')}}"><li><span title="Visitantes"><i class="bi bi-person-raised-hand"></i></span><span>Visitantes</span></li></a>
                            @if(module_enabled('batismo') && Route::has('batismo.painel'))
                                <a href="{{ route('batismo.painel') }}"><li><span title="Batismos"><i class="bi bi-water"></i></span><span>Batismos</span></li></a>
                            @endif
                            <a href="{{route('departamentos.painel')}}"><li><span title="Departamentos"><i class="bi bi-intersect"></i></span><span>Departamentos</span></li></a>
                            @if(module_enabled('celulas') && Route::has('celulas.painel'))
                                <a href="{{ route('celulas.painel') }}"><li><span title="GCA - Células"><i class="bi bi-cup-hot"></i></span><span>GCA - Células</span></li></a>
                            @endif
                            @if(module_enabled('cursos'))
                                <a href="{{route('cursos.index')}}" style="display: none;"><li><span title="Escola Virtual"><i class="bi bi-mortarboard"></i></span><span>Escola Virtual</span></li></a>
                            @endif
                            <a href="{{ route('financeiro.painel') }}"><li><span title="Financeiro"><i class="bi bi-currency-exchange"></i></span><span>Financeiro</span></li></a>
                            <a href="{{route('noticias.painel')}}"><li><span title="Notícias"><i class="bi bi-newspaper"></i></span><span>Notícias</span></li></a>
                            <a href="{{route('podcasts.painel')}}"><li><span title="Podcasts"><i class="bi bi-mic-fill"></i></span><span>Podcasts</span></li></a>
                            @if($areaPastoralRoute)
                                <a href="{{ $areaPastoralRoute }}" style="display: none;"><li><span title="Área Pastoral"><i class="bi bi-align-top"></i></span><span>Área Pastoral</span></li></a>
                            @endif
                            <a href="{{route('livraria.index')}}" style="display: none;"><li><span title="Livraria"><i class="bi bi-book"></i></span><span>Livraria</span></li></a>
                            <a href="{{route('relatorios.painel')}}"><li><span title="Relatórios"><i class="bi bi-pie-chart"></i></span><span>Relatórios</span></li></a>
                            @if(module_enabled('projetos') && Route::has('projetos.painel'))
                                <a href="{{ route('projetos.painel') }}" style="display: none;"><li><span title="Projetos"><i class="bi bi-kanban"></i></span><span>Projetos</span></li></a>
                            @endif
                            @if(module_enabled('moedas') && Route::has('moedas.painel'))
                                <a href="{{ route('moedas.painel') }}"><li><span title="Moeda Interna"><i class="bi bi-coin"></i></span><span>Moeda Interna</span></li></a>
                            @endif
                            <a href="{{route('livraria.index')}}" style="display: none;"><li><span title="Ação Social"><i class="bi bi-box2-heart"></i></span><span>Ação Social</span></li></a>
                            <a href="{{route('assinaturas.index')}}" style="display: none;"><li><span title="Assinaturas"><i class="bi bi-journal-plus"></i></span><span>Assinaturas</span></li></a>
                            <a href="{{route('pesquisas.replies.index')}}"><li><span title="Pesquisas"><i class="bi bi-bar-chart"></i></span><span>Pesquisas</span></li></a>
                            @if(module_enabled('biblia'))
                                <a href="{{route('biblia.index')}}"><li><span title="Bíblia"><x-icon title="Bíblia Sagrada" name="biblia" class="svg"/> </span><span>Bíblia Sagrada</span></li></a>
                            @endif
                            @if(module_enabled('recados') && Route::has('recados.historico'))
                                <a href="{{ route('recados.historico') }}"><li><span title="Recados"><i class="bi bi-chat-left-dots"></i></span><span>Recados</span></li></a>
                            @endif
                            @if(module_enabled('futcristao') && Route::has('futcristao.index'))
                                <a href="{{ route('futcristao.index') }}" style="display: none;"><li><span title="Futcristão"><x-icon title="Futcristão" name="soccer" class="svg"/> </span><span>Futcristão</span></li></a>
                            @endif
                            <a href="{{route('tutoriais.index')}}"><li><span title="Tutoriais"><i class="bi bi-question-octagon"></i></span><span>Tutoriais</span></li></a>
                            <a href="{{route('extensoes.painel')}}"><li><span title="Extensões"><i class="bi bi-nut"></i></span><span>Extensões</span></li></a>
                            @if(module_enabled('drive'))
                                @php($driveUrl = Route::has('drive.painel') ? route('drive.painel') : url('/drive'))
                                <a href="{{ $driveUrl }}"><li><span title="Drive"><i class="bi bi-hdd"></i></span><span>Drive</span></li></a>
                            @endif
                        @else
                            <a href="{{route('agenda.read')}}"><li><span title="Agenda"><i class="bi bi-calendar3"></i></span><span>Agenda</span></li></a>
                            <a href="{{route('noticias.painel')}}"><li><span title="Notícias"><i class="bi bi-newspaper"></i></span><span>Notícias</span></li></a>
                            <a href="{{ route('avisos.painel') }}"><li><span title="Avisos"><i class="bi bi-megaphone"></i></span><span>Avisos</span></li></a>
                            <a href="{{route('podcasts.painel')}}"><li><span title="Podcasts"><i class="bi bi-mic-fill"></i></span><span>Podcasts</span></li></a>
                            @if($areaPastoralRoute)
                                <a href="{{ $areaPastoralRoute }}" style="display: none;"><li><span title="Área Pastoral"><i class="bi bi-align-top"></i></span><span>Área Pastoral</span></li></a>
                            @endif
                            <a href="{{route('livraria.index')}}" style="display: none;"><li><span title="Livraria"><i class="bi bi-book"></i></span><span>Livraria</span></li></a>
                            @if(module_enabled('biblia'))
                                <a href="{{route('biblia.index')}}"><li><span title="Bíblia"><x-icon title="Bíblia Sagrada" name="biblia" class="svg"/> </span><span>Bíblia Sagrada</span></li></a>
                            @endif
                            <a href="{{ route('perfil') }}"><li><span title="Perfil"><i class="bi bi-person-badge"></i></span><span>Perfil</span></li></a>
                            <a href="{{ route('pesquisas.replies.index') }}"><li><span title="Pesquisas"><i class="bi bi-bar-chart"></i></span><span>Pesquisas</span></li></a>
                            @if(module_enabled('futcristao') && Route::has('futcristao.index'))
                                <a href="{{ route('futcristao.index') }}" style="display: none;"><li><span title="Futcristão"><x-icon title="Futcristão" name="soccer" class="svg"/> </span><span>Futcristão</span></li></a>
                            @endif
                        @endif
                    </ul>
                </nav>
                @yield('content')
                
            </main>
            
            <!-- Audio Player Global - Fora do Livewire -->
            @include('partials.audio-player')
            
            <footer>
                <p>Ecossistema Kleros | {{$congregacao->nome_curto}} - {{optional($congregacao->cidade)->nome}}/{{optional($congregacao->estado)->uf}}</p>
                @if($congregacao->cnpj) <h4>CNPJ {{$congregacao->cnpj}}</h4> @endif
            </footer>
        </div>
        <!-- Modal Flutuante Reutilizável -->
        @include('partials/janela-modal')

        <!--CDNs do Jquery-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

        <!-- JS do Select2 -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        
        <!--CDN do swipper para interações-->
        <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

        <!--CDN do fullcalendar para a agenda-->
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

        <!-- CDN do Plyr.js para áudio -->
        <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>

        <!--Scripts gerais-->
        <script>
            function fallbackInitTabs(container) {
                if (!container) {
                    return;
                }

                const legacyTabContainers = container.querySelectorAll('.tabs');
                legacyTabContainers.forEach((tabsContainer) => {
                    const menuItems = tabsContainer.querySelectorAll('.tab-menu li[data-tab]');
                    const panes = tabsContainer.querySelectorAll('.tab-pane');
                    if (!menuItems.length || !panes.length) {
                        return;
                    }

                    const activate = (tabId) => {
                        menuItems.forEach((item) => {
                            const isActive = item.dataset.tab === tabId;
                            item.classList.toggle('active', isActive);
                        });

                        panes.forEach((pane, index) => {
                            const isActive = pane.id === tabId || (!tabId && index === 0);
                            pane.classList.toggle('active', isActive);
                            pane.hidden = !isActive;
                            pane.style.display = isActive ? '' : 'none';
                        });
                    };

                    const initialTab = tabsContainer.querySelector('.tab-menu li.active[data-tab]');
                    if (initialTab) {
                        activate(initialTab.dataset.tab);
                    } else if (menuItems[0]) {
                        activate(menuItems[0].dataset.tab);
                    }
                });
            }

            function fallbackInitOptionsMenus(container) {
                if (!container) {
                    return;
                }

                const triggers = container.querySelectorAll('[data-options-target], [data-options-trigger]');
                triggers.forEach((trigger) => {
                    if (trigger.dataset.optionsFallbackInitialized) {
                        return;
                    }

                    const menuId = trigger.dataset.optionsTarget || trigger.dataset.optionsTrigger;
                    if (!menuId) {
                        return;
                    }

                    const menu = container.querySelector(`#${menuId}`) || document.getElementById(menuId);
                    if (!menu) {
                        return;
                    }

                    trigger.dataset.optionsFallbackInitialized = 'true';
                    const buttons = Array.from(menu.querySelectorAll('[data-action]'));

                    const handleAction = (button) => {
                        const action = button.dataset.action;
                        if (action === 'print') {
                            window.print();
                        } else if (action === 'back') {
                            window.history.back();
                        } else if (action === 'redirect') {
                            const url = button.dataset.url;
                            if (url) {
                                window.location.href = url;
                            }
                        }
                    };

                    trigger.addEventListener('click', (event) => {
                        event.preventDefault();
                        const willOpen = menu.hidden !== false && !menu.classList.contains('is-open');
                        if (willOpen) {
                            menu.hidden = false;
                            menu.classList.add('is-open');
                        } else {
                            menu.hidden = true;
                            menu.classList.remove('is-open');
                        }
                    });

                    buttons.forEach((button) => {
                        if (button.dataset.actionFallbackInitialized) {
                            return;
                        }
                        button.dataset.actionFallbackInitialized = 'true';
                        button.addEventListener('click', (event) => {
                            event.preventDefault();
                            menu.hidden = true;
                            menu.classList.remove('is-open');
                            handleAction(button);
                        });
                    });
                });
            }

            $(document).ready(function(){

                // Exemplo de input : <input type="tel" id="telefone" placeholder="(00) 00000-0000">
            
                $('#telefone').mask('(00) 00000-0000');
                $('#cep').mask('00000-000');
                $('#cnpj').mask('00.000.000/0000-00');
                
                // Fechar mensagens de aviso
                $('.msg .close').click(function(){
                    this.closest('.msg').remove();
                })

                if (typeof initOptionsMenus !== 'function') {
                    fallbackInitOptionsMenus(document);
                }
                
            });
        </script>

        <!--Controle do menu lateral-->
        <script>
            // estado inicial: fechado
            let menuAberto = false;
            const $left = $('.left-navbar');

            function aplicarEstado(){
                $left.toggleClass('expanded', menuAberto)
                    .toggleClass('collapsed', !menuAberto);
            }

            // inicializa
            aplicarEstado();

            // clique no botão do menu
            $('#btn-menu').on('click', function(){
                menuAberto = !menuAberto;
                aplicarEstado();
            });
        </script>

        <script>
            (function() {
                const swUrl = '/service-worker.js';
                if (!('serviceWorker' in navigator)) {
                    return;
                }

                let refreshing = false;

                window.addEventListener('load', () => {
                    navigator.serviceWorker.register(swUrl).then((registration) => {
                        if (registration.waiting) {
                            registration.waiting.postMessage({ type: 'SKIP_WAITING' });
                        }

                        registration.addEventListener('updatefound', () => {
                            const newWorker = registration.installing;
                            if (!newWorker) return;
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    newWorker.postMessage({ type: 'SKIP_WAITING' });
                                }
                            });
                        });

                        navigator.serviceWorker.addEventListener('controllerchange', () => {
                            if (refreshing) return;
                            refreshing = true;
                            window.location.reload();
                        });

                        setInterval(() => registration.update(), 60 * 60 * 1000);
                    }).catch((error) => console.error('SW registration failed', error));
                });
            })();

            (function() {
                let deferredPrompt = null;
                const installLink = document.querySelector('[data-pwa-install]');
                const manifestUrl = "{{ route('pwa.manifest') }}";

                function isMobile() {
                    return /Android|iPhone|iPad|iPod/i.test(navigator.userAgent) && window.innerWidth <= 1024;
                }

                if (!isMobile() && installLink) {
                    installLink.style.display = 'none';
                }

                window.addEventListener('beforeinstallprompt', (event) => {
                    event.preventDefault();
                    deferredPrompt = event;
                    if (isMobile()) {
                        installLink?.classList.add('pwa-install-visible');
                        installLink.style.display = '';
                    }
                });

                installLink?.addEventListener('click', async (event) => {
                    event.preventDefault();
                    if (deferredPrompt) {
                        deferredPrompt.prompt();
                        await deferredPrompt.userChoice;
                        deferredPrompt = null;
                        return;
                    }
                    window.location.href = manifestUrl;
                });
            })();
        </script>

        <!--Função para controle da janela flutuante-->
        <script>
            // Sistema de pilha de modais para navegação entre eles
            const modalStack = [];
            let currentModalIndex = -1;

            function abrirJanelaModal(url, options = {}) {
                const { iframe = false, title = 'Visualização' } = options;
                
                const modal = document.getElementById('janelaModal');
                const container = document.getElementById('conteudoModal');
                const modalEstaAberto = modal.style.display === 'flex';
                
                // Se já existe um modal aberto, salva-o na pilha
                if (modalEstaAberto && container.innerHTML.trim() !== '') {
                    const currentContent = container.innerHTML;
                    
                    modalStack.push({
                        url: window.currentModalUrl || url,
                        content: currentContent,
                        iframe: window.currentModalIsIframe || false,
                        title: window.currentModalTitle || title
                    });
                    currentModalIndex = modalStack.length - 1;
                    
                    console.log('Modal salvo na pilha. Total na pilha:', modalStack.length);
                }

                // Salva informações do modal atual
                window.currentModalUrl = url;
                window.currentModalIsIframe = iframe;
                window.currentModalTitle = title;

                let targetUrl = url;

                // Se for iframe com URL externa, mantém a URL completa
                if (iframe && (url.startsWith('http://') || url.startsWith('https://'))) {
                    targetUrl = url;
                } else if (typeof url === 'string') {
                    try {
                        if (url.startsWith('http://') || url.startsWith('https://')) {
                            const parsed = new URL(url, window.location.href);
                            targetUrl = parsed.pathname + parsed.search + parsed.hash;
                        }
                    } catch (error) {
                        targetUrl = url;
                    }
                }

                if (iframe) {
                    container.innerHTML = '';
                    const wrapper = document.createElement('div');
                    wrapper.className = 'modal-iframe-wrapper';

                    const iframeEl = document.createElement('iframe');
                    iframeEl.src = targetUrl;
                    iframeEl.title = title;
                    iframeEl.loading = 'lazy';
                    iframeEl.style.width = '100%';
                    iframeEl.style.height = '80vh';
                    iframeEl.style.border = '0';

                    wrapper.appendChild(iframeEl);
                    container.appendChild(wrapper);
                    modal.style.display = 'flex';
                    
                    // Adiciona botão de voltar se houver histórico
                    adicionarBotaoVoltar();
                    return;
                }

                container.innerHTML = '';

                fetch(targetUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro ao carregar o conteúdo.');
                        }

                        return response.text();
                    })
                    .then(html => {
                        container.innerHTML = html;
                        modal.style.display = 'flex';
                        
                        if (typeof initModalScripts === 'function') {
                            try {
                                initModalScripts(container);
                            } catch (error) {
                                console.error('Falha ao inicializar scripts do modal.', error);
                                fallbackInitTabs(container);
                            }
                        } else {
                            fallbackInitTabs(container);
                        }

                        if (typeof initOptionsMenus === 'function') {
                            try {
                                initOptionsMenus(container);
                            } catch (error) {
                                console.error('Falha ao inicializar menus do modal.', error);
                                fallbackInitOptionsMenus(container);
                            }
                        } else {
                            fallbackInitOptionsMenus(container);
                        }

                        if (typeof initOptionsMenus === 'function') {
                            try {
                                initOptionsMenus(container);
                            } catch (error) {
                                console.error('Falha ao inicializar menus do modal.', error);
                            }
                        }
                        
                        // Adiciona botão de voltar se houver histórico
                        adicionarBotaoVoltar();
                    })
                    .catch(() => {
                        container.innerHTML = '';
                        const message = document.createElement('p');
                        message.textContent = 'Não foi possível carregar o conteúdo.';
                        container.appendChild(message);
                        modal.style.display = 'flex';
                        
                        adicionarBotaoVoltar();
                    });                
            }

            function adicionarBotaoVoltar() {
                console.log('adicionarBotaoVoltar chamado. Modais na pilha:', modalStack.length);
                
                // Remove botão anterior se existir
                const btnExistente = document.getElementById('btnVoltarModal');
                if (btnExistente) {
                    console.log('Removendo botão voltar existente');
                    btnExistente.remove();
                }

                // Só adiciona se houver histórico
                if (modalStack.length === 0) {
                    console.log('Nenhum modal na pilha, não adicionando botão voltar');
                    return;
                }

                const modal = document.getElementById('janelaModal');
                const btnFechar = modal.querySelector('.close');
                
                console.log('Botão fechar encontrado:', btnFechar !== null);
                
                if (btnFechar) {
                    const btnVoltar = document.createElement('span');
                    btnVoltar.id = 'btnVoltarModal';
                    btnVoltar.className = 'btn-voltar-modal';
                    btnVoltar.innerHTML = '<i class="bi bi-arrow-left"></i>';
                    btnVoltar.title = 'Voltar ao modal anterior (' + modalStack.length + ' na pilha)';
                    btnVoltar.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        voltarModalAnterior();
                    };
                    
                    btnFechar.parentNode.insertBefore(btnVoltar, btnFechar);
                    console.log('Botão voltar adicionado com sucesso');
                } else {
                    console.error('Botão fechar (.close) não encontrado!');
                }
            }

            function voltarModalAnterior() {
                if (modalStack.length === 0) {
                    fecharJanelaModal();
                    return;
                }

                const modalAnterior = modalStack.pop();
                currentModalIndex--;

                const container = document.getElementById('conteudoModal');
                
                // Verifica se precisa recarregar o modal
                if (window.recarregarModalAnterior && modalAnterior.url) {
                    console.log('🔄 [MAIN] Recarregando modal anterior:', modalAnterior.url);
                    window.recarregarModalAnterior = false;
                    
                    // Recarrega o modal via AJAX
                    fetch(modalAnterior.url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        container.innerHTML = html;
                        
                        // Restaura informações do modal
                        window.currentModalUrl = modalAnterior.url;
                        window.currentModalIsIframe = false;
                        window.currentModalTitle = modalAnterior.title;
                        
                        // Reinicializa scripts
                        if (typeof initModalScripts === 'function') {
                            try {
                                initModalScripts(container);
                            } catch (error) {
                                console.error('Falha ao reinicializar scripts.', error);
                            }
                        }
                        
                        if (typeof initOptionsMenus === 'function') {
                            try {
                                initOptionsMenus(container);
                            } catch (error) {
                                console.error('Falha ao reinicializar menus.', error);
                            }
                        }
                        
                        // Atualiza botão de voltar
                        adicionarBotaoVoltar();
                        
                        // Dispara evento de que o modal foi restaurado
                        console.log('Modal anterior recarregado, disparando evento modalRestaurado');
                        setTimeout(() => {
                            window.dispatchEvent(new CustomEvent('modalRestaurado', {
                                detail: { recarregado: true }
                            }));
                        }, 100);
                    })
                    .catch(error => {
                        console.error('Erro ao recarregar modal:', error);
                        // Fallback: restaura do cache
                        container.innerHTML = modalAnterior.content;
                    });
                    
                    return;
                }
                
                // Comportamento normal: restaura do cache
                container.innerHTML = modalAnterior.content;

                // Restaura informações do modal
                window.currentModalUrl = modalAnterior.url;
                window.currentModalIsIframe = modalAnterior.iframe;
                window.currentModalTitle = modalAnterior.title;

                // Reinicializa scripts se não for iframe
                if (!modalAnterior.iframe) {
                    if (typeof initModalScripts === 'function') {
                        try {
                            initModalScripts(container);
                        } catch (error) {
                            console.error('Falha ao reinicializar scripts.', error);
                        }
                    }
                    
                    if (typeof initOptionsMenus === 'function') {
                        try {
                            initOptionsMenus(container);
                        } catch (error) {
                            console.error('Falha ao reinicializar menus.', error);
                        }
                    }
                }

                // Atualiza botão de voltar
                adicionarBotaoVoltar();
                
                // Dispara evento de que o modal foi restaurado
                console.log('Modal anterior restaurado, disparando evento modalRestaurado');
                setTimeout(() => {
                    window.dispatchEvent(new CustomEvent('modalRestaurado', {
                        detail: { container: container }
                    }));
                }, 100);
            }

            function fecharJanelaModal() {
                // Se houver modais na pilha, volta ao anterior em vez de fechar tudo
                if (modalStack.length > 0) {
                    console.log('Voltando ao modal anterior ao invés de fechar');
                    voltarModalAnterior();
                    return;
                }

                // Só fecha tudo se não houver histórico
                const container = document.getElementById('conteudoModal');
                container.innerHTML = '';
                document.getElementById('janelaModal').style.display = 'none';
                
                // Limpa a pilha de modais
                modalStack.length = 0;
                currentModalIndex = -1;
                window.currentModalUrl = null;
                window.currentModalIsIframe = null;
                window.currentModalTitle = null;
                
                // Remove botão de voltar se existir
                const btnVoltar = document.getElementById('btnVoltarModal');
                if (btnVoltar) {
                    btnVoltar.remove();
                }
                
                console.log('Modal fechado completamente');
            }
            
            // Função para fechar todos os modais de uma vez (força)
            function fecharTodosModais() {
                const container = document.getElementById('conteudoModal');
                container.innerHTML = '';
                document.getElementById('janelaModal').style.display = 'none';
                
                modalStack.length = 0;
                currentModalIndex = -1;
                window.currentModalUrl = null;
                window.currentModalIsIframe = null;
                window.currentModalTitle = null;
                
                const btnVoltar = document.getElementById('btnVoltarModal');
                if (btnVoltar) {
                    btnVoltar.remove();
                }
                
                console.log('Todos os modais fechados');
            }
        </script>

        <!--Script para o banner de destaques-->
        <script>
            console.log('Swiper:', typeof Swiper !== 'undefined' ? 'Loaded' : 'Not loaded');
            document.addEventListener('DOMContentLoaded', function () {
                const swiperContainer = document.querySelector('.mySwiper');
                
                // Só inicializa o Swiper se o container existir
                if (!swiperContainer) {
                    console.log('Container .mySwiper não encontrado - Swiper não será inicializado');
                    return;
                }
                
                const destaquesLength = document.querySelectorAll('.swiper-slide').length;
                console.log('Slides encontrados:', destaquesLength);

                const swiper = new Swiper(".mySwiper", {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    loop: destaquesLength > 4, // ativa loop só se houver mais de 4 slides
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    breakpoints: {
                        640: { slidesPerView: 2 },
                        768: { slidesPerView: 3 },
                        1024: { slidesPerView: 4 },
                    },
                    observer: true,
                    observeParents: true,
                    watchSlidesProgress: true,
                    watchSlidesVisibility: true,
                });

                setTimeout(() => swiper.update(), 300);
            });
        </script>

        <!--Script para o modal de confirmação-->
        <script>

            //Função usada para formulários que precisam de modal de confirmação, prevenindo o submit imediato
            function handleSubmit(event, form, message) {
                event.preventDefault(); // impede envio imediato

                confirmarAcao(message).then((confirmed) => {
                    if (confirmed) {
                        form.submit(); // só envia se o usuário confirmar
                    }
                });

                return false; // impede comportamento padrão
            }

            function confirmarAcao(message) {
            return new Promise((resolve) => {
                const $popup = $('.popup');
                $('#msg_content').text(message);
                $popup.show(); // ou removeClass('is-hidden')

                const confirmaBtn = document.getElementById('confirmaBtn');
                const cancelaBtn = document.getElementById('cancelaBtn');

                const onConfirm = () => { cleanup(); $popup.hide(); resolve(true); };
                const onCancel  = () => { cleanup(); $popup.hide(); resolve(false); };
                const onKey     = (e) => {
                if (e.key === 'Escape') onCancel();
                if (e.key === 'Enter')  onConfirm();
                };

                confirmaBtn.addEventListener('click', onConfirm);
                cancelaBtn.addEventListener('click', onCancel);
                document.addEventListener('keydown', onKey);

                function cleanup() {
                confirmaBtn.removeEventListener('click', onConfirm);
                cancelaBtn.removeEventListener('click', onCancel);
                document.removeEventListener('keydown', onKey);
                }
            });
            }
        </script>

        <!--Para controle das flash massages via JS-->
        <script>
            function flashMsg(text, type = 'success', opts = {}) {
                const timeout = Number.isFinite(opts.timeout) ? opts.timeout : 4000;

                // garante o wrapper .msg
                let $wrap = $('.msg');
                if (!$wrap.length) {
                    $wrap = $('<div class="msg" role="alert" aria-live="polite"></div>').appendTo('body');
                }

                // limpa conteúdo anterior e monta a caixa
                $wrap.empty();
                const $box    = $(`<div class="${type}"></div>`);
                const $close  = $('<span class="close" aria-label="Fechar">&times;</span>');
                const $content= $('<span class="content"></span>').text(text);

                $box.append($close).append($content);
                $wrap.append($box).hide().fadeIn(150);

                const close = () => $wrap.fadeOut(150, () => $wrap.empty());

                $close.on('click', close);

                // auto-fecha (pausa ao passar o mouse)
                if (timeout > 0) {
                    const t = setTimeout(close, timeout);
                    $wrap.on('mouseenter', () => clearTimeout(t));
                }
            }
        </script>

        <!--Script para o menu de perfil do usuário e notificaçoes-->
        <script>
            const profileBtn = document.getElementById('profileBtn');
            const profileDropdown = document.getElementById('profileDropdown');

            const notificacaoBtn = document.getElementById('notificacaoBtn');
            const notificacaoDropdown = document.getElementById('notificacaoDropdown');

            const menuMobileBtn = document.getElementById('menuMobileBtn');
            const menuMobileDropdown = document.getElementById('menuMobileDropdown');
            const mobileNavOverlay = document.getElementById('mobileNavOverlay');
            const mobileNavPanel = document.getElementById('mobileNavPanel');
            const mobileNavBackdrop = document.getElementById('mobileNavBackdrop');
            const mobileNavClose = document.getElementById('mobileNavClose');
            const mobileNavList = document.getElementById('mobileNavList') || document.getElementById('menuMobileList');
            const desktopMenu = document.getElementById('menu-express');

            if (desktopMenu && mobileNavList && mobileNavList.children.length === 0) {
                const links = desktopMenu.querySelectorAll('a');
                links.forEach((link) => {
                    const item = document.createElement('li');
                    const anchor = document.createElement('a');
                    anchor.href = link.href;

                    const sourceLi = link.querySelector('li');
                    anchor.innerHTML = sourceLi ? sourceLi.innerHTML : link.innerHTML;

                    item.appendChild(anchor);
                    mobileNavList.appendChild(item);
                });
            }

            const DROPDOWN_PADDING = 20;

            function resetAdjust(dropdown) {
                if (!dropdown) {
                    return;
                }
                dropdown.style.setProperty('--menu-adjust', '0px');
                dropdown.style.removeProperty('transform');
            }

            function adjustDropdown(dropdown) {
                if (!dropdown) {
                    return;
                }

                resetAdjust(dropdown);

                requestAnimationFrame(() => {
                    const rect = dropdown.getBoundingClientRect();
                    const viewportWidth = window.innerWidth || document.documentElement.clientWidth;

                    let adjust = 0;
                    if (rect.left < DROPDOWN_PADDING) {
                        adjust = DROPDOWN_PADDING - rect.left;
                    } else if (rect.right > viewportWidth - DROPDOWN_PADDING) {
                        adjust = (viewportWidth - DROPDOWN_PADDING) - rect.right;
                    }

                    dropdown.style.setProperty('--menu-adjust', `${adjust}px`);
                });
            }

            function closeMenuMobile() {
                if (!menuMobileDropdown || !menuMobileBtn) {
                    return;
                }
                menuMobileDropdown.classList.remove('show');
                menuMobileBtn.classList.remove('selected');
                menuMobileBtn.setAttribute('aria-expanded', 'false');
                resetAdjust(menuMobileDropdown);
            }

            function closeAll() {
                if (profileDropdown) {
                    profileDropdown.classList.remove('show');
                    resetAdjust(profileDropdown);
                    if (profileBtn) {
                        profileBtn.classList.remove('selected');
                    }
                }
                if (notificacaoDropdown) {
                    notificacaoDropdown.classList.remove('show');
                    resetAdjust(notificacaoDropdown);
                    if (notificacaoBtn) {
                        notificacaoBtn.classList.remove('selected');
                    }
                }
                closeMenuMobile();
            }

            if (profileBtn && profileDropdown) {
                profileBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const willShow = !profileDropdown.classList.contains('show');
                    closeAll();
                    if (willShow) {
                        profileDropdown.classList.add('show');
                        adjustDropdown(profileDropdown);
                        profileBtn.classList.add('selected');
                    }
                });
            }

            if (notificacaoBtn && notificacaoDropdown) {
                notificacaoBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const willShow = !notificacaoDropdown.classList.contains('show');
                    closeAll();
                    if (willShow) {
                        notificacaoDropdown.classList.add('show');
                        adjustDropdown(notificacaoDropdown);
                        notificacaoBtn.classList.add('selected');
                    }
                });
            }

            // fechar ao clicar fora
            document.addEventListener('click', () => closeAll());

            // opcional: fechar com ESC
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeAll();
            });

            if (menuMobileBtn && menuMobileDropdown) {
                menuMobileBtn.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    const willShow = !menuMobileDropdown.classList.contains('show');
                    closeAll();
                    if (willShow) {
                        menuMobileDropdown.classList.add('show');
                        menuMobileBtn.classList.add('selected');
                        menuMobileBtn.setAttribute('aria-expanded', 'true');
                        adjustDropdown(menuMobileDropdown);
                    }
                });

                menuMobileDropdown.addEventListener('click', (event) => {
                    if (event.target.closest('a')) {
                        closeMenuMobile();
                    }
                });
            }

            window.addEventListener('resize', () => {
                if (profileDropdown && profileDropdown.classList.contains('show')) {
                    adjustDropdown(profileDropdown);
                }

                if (notificacaoDropdown && notificacaoDropdown.classList.contains('show')) {
                    adjustDropdown(notificacaoDropdown);
                }

                if (menuMobileDropdown && menuMobileDropdown.classList.contains('show')) {
                    adjustDropdown(menuMobileDropdown);
                }

                if (window.innerWidth > 500) {
                    closeMenuMobile();
                }
            });
        </script>

        <!--Script para a seleção de abas-->
        <script>
            function activateTab(tab) {
                const menu = tab.closest(".tab-menu");
                const container = tab.closest(".tabs");
                if (!menu || !container) {
                    return;
                }

                const target = tab.getAttribute("data-tab");
                if (!target) {
                    return;
                }

                const tabs = menu.querySelectorAll("li[data-tab]");
                const panes = container.querySelectorAll(".tab-pane");

                tabs.forEach((item) => {
                    item.classList.toggle("active", item === tab);
                });

                panes.forEach((pane) => {
                    const isActive = pane.id === target;
                    pane.classList.toggle("active", isActive);
                    if (isActive) {
                        pane.hidden = false;
                        pane.style.display = "";
                    } else {
                        pane.hidden = true;
                        pane.style.display = "none";
                    }
                });
            }

            document.addEventListener("click", function (event) {
                const tab = event.target.closest(".tab-menu li[data-tab]");
                if (!tab) {
                    return;
                }

                event.preventDefault();
                activateTab(tab);
            });

            document.addEventListener("DOMContentLoaded", () => {
                document.querySelectorAll(".tab-menu li.active[data-tab]").forEach((tab) => activateTab(tab));
            });
        </script>

        <!-- Script para o dropdown de notificações -->
        <script>
            const list = document.getElementById('notifList');
            const empty = document.getElementById('notifEmpty');
            const badge = document.getElementById('notifBadge');
            const markAllRead = document.getElementById('markAllRead');
            const clearAll = document.getElementById('clearAll');

            // Só executa se os elementos existirem
            if (list && empty && badge && markAllRead && clearAll) {
                // Atualiza badge com base nos itens não lidos
                function updateBadge(){
                    const unread = list.querySelectorAll('.notif-item:not(.is-read)').length;
                    if (unread > 0) { badge.textContent = unread; badge.classList.remove('is-hidden'); }
                    else { badge.classList.add('is-hidden'); }
                    empty.style.display = list.children.length === 0 ? 'block' : (unread === 0 ? 'block' : 'none');
                    if(list.children.length === 0){ empty.textContent = 'Sem notificações.'; }
                }

                // Marca um item como lido ao clicar nele (antes de seguir o link)
                list.addEventListener('click', function(e){
                    const item = e.target.closest('.notif-item');
                    if(!item) return;
                    item.classList.add('is-read');
                    updateBadge();
                    // Se quiser bloquear a navegação para tratar via JS/AJAX:
                    // e.preventDefault();
                });

                // Marcar todas como lidas
                markAllRead.addEventListener('click', function(){
                    list.querySelectorAll('.notif-item').forEach(i => i.classList.add('is-read'));
                    updateBadge();
                });

                // Limpar todas
                clearAll.addEventListener('click', function(){
                    list.innerHTML = '';
                    updateBadge();
                });

                // Inicializa contagem
                updateBadge();
            }
        </script>

        @stack('scripts')
        <script>
            $(document).ready(function() {
                function fetchNotifications() {
                $.ajax({
                url: '{{ route('notificacoes.index') }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    updateNotificationUI(data);
                },
                error: function(error) {
                console.error('Error fetching notifications:', error);
            }
        }); 
    }

    function formatTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        let interval = seconds / 31536000;
        if (interval > 1) {
            return Math.floor(interval) + " anos atrás";
        }
        interval = seconds / 2592000;
        if (interval > 1) {
            return Math.floor(interval) + " meses atrás";
        }
        interval = seconds / 86400;
        if (interval > 1) {
            return Math.floor(interval) + " dias atrás";
        }
        interval = seconds / 3600;
        if (interval > 1) {
            return Math.floor(interval) + " horas atrás";
        }
        interval = seconds / 60;
        if (interval > 1) {
            return Math.floor(interval) + " minutos atrás";
        }
        return Math.floor(seconds) + " segundos atrás";
    }

    function updateNotificationUI(data) {
        const notifList = $('#notifList');
        const notifBadge = $('#notifBadge');
        const notifEmpty = $('#notifEmpty');

        notifList.empty(); // Clear existing notifications

        if (data.count > 0) {
            notifBadge.text(data.count).removeClass('is-hidden');
            notifEmpty.hide();

            const summaryMessage = `Você tem ${data.count} novos avisos.`;
            const summaryHtml = `
                <a href="{{ route('avisos.painel') }}" class="notif-item">
                    <div class="notif-icon"><i class="bi bi-envelope"></i></div>
                    <div class="notif-content">
                        <h5>Novos Avisos</h5>
                        <p>${summaryMessage}</p>
                    </div>
                </a>
            `;
            notifList.append(summaryHtml);

            data.avisos.forEach(function(aviso) {
                const avisoUrl = `/avisos#aviso-${aviso.id}`;
                const html = `
                    <a href="${avisoUrl}" class="notif-item" data-id="${aviso.id}">
                        <div class="notif-icon"><i class="bi bi-info-circle"></i></div>
                        <div class="notif-content">
                            <h5>${aviso.titulo}</h5>
                            <p>${aviso.mensagem.substring(0, 50)}...</p>
                            <div class="notif-meta">
                                <span class="notif-dot"></span>
                                <span>${formatTimeAgo(aviso.created_at)}</span>
                            </div>
                        </div>
                    </a>
                `;
                notifList.append(html);
            });

        } else {
            notifBadge.text('0').addClass('is-hidden');
            notifEmpty.show();
        }
    }

    fetchNotifications();

    setInterval(fetchNotifications, 60000);
});
        </script>
        @livewireScripts
    </body>
</html>
