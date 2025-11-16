@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')
@push('styles')
<style>
    #dados-gerais .dashboard-grid {
        display: grid;
        gap: 20px;
        margin-bottom: 25px;
    }

    #dados-gerais .dashboard-cards {
        display: grid;
        gap: 15px;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    #dados-gerais .dashboard-card {
        position: relative;
        border-radius: 18px;
        padding: 18px 20px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.16), rgba(255, 255, 255, 0)) var(--primary-color);
        color: var(--primary-contrast);
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 14px 28px rgba(17, 24, 39, 0.18);
    }

    #dados-gerais .dashboard-card.neutral {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-color);
        border-color: rgba(255, 255, 255, 0.12);
    }

    #dados-gerais .dashboard-card span.label {
        display: block;
        font-size: 0.75rem;
        letter-spacing: 2px;
        text-transform: uppercase;
        opacity: 0.7;
        margin-bottom: 6px;
    }

    #dados-gerais .dashboard-card strong {
        display: block;
        font-size: 2rem;
        font-weight: 600;
        line-height: 1.2;
    }

    #dados-gerais .dashboard-card small {
        display: block;
        font-size: 0.85rem;
        margin-top: 8px;
        opacity: 0.8;
    }

    #dados-gerais .dashboard-side {
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 22px 20px;
        box-shadow: 0 12px 24px rgba(17, 24, 39, 0.2);
    }

    #dados-gerais .chart-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    #dados-gerais .chart-item:last-child {
        border-bottom: none;
    }

    #dados-gerais .chart-item header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.85);
    }

    #dados-gerais .chart-item header span.value {
        font-weight: 600;
        color: var(--secondary-color);
    }

    #dados-gerais .chart-bar {
        position: relative;
        height: 10px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.18);
        overflow: hidden;
    }

    #dados-gerais .chart-bar span {
        position: absolute;
        inset: 0;
        border-radius: inherit;
        background: linear-gradient(90deg, var(--secondary-color), var(--terciary-color));
    }

    @media (max-width: 960px) {
        #dados-gerais .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Previne overflow horizontal global */
    .container {
        overflow-x: hidden;
        max-width: 100%;
    }

    /* Estilos da Galeria de Aniversariantes */
    .aniversariantes-container,
    .visitantes-container {
        position: relative;
        width: 100%;
        max-width: 100%;
    }

    .galeria-viewport {
        overflow: visible; /* Permite que box-shadow seja visível */
        width: 100%;
        max-width: 100%;
        padding: 20px 0; /* Aumentado para acomodar box-shadow */
        box-sizing: border-box;
    }

    .galeria-track {
        display: flex;
        gap: 15px;
        padding: 0 15px; /* Padding interno para não cortar cards */
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: transform;
        box-sizing: border-box;
    }

    .galeria-item {
        flex: 0 0 auto;
        scroll-snap-align: start;
        min-width: 220px;
        max-width: 280px;
        box-sizing: border-box;
        /* Estilos herdados de .card mas sem background/border */
        position: relative;
        padding: clamp(16px, 2.5vw, 22px);
        border-radius: 20px;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.18);
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: transparent !important;
        border: none !important;
    }
    
    /* Override adicional com máxima especificidade */
    #aniversariantes-galeria .galeria-item,
    #visitantes-galeria .galeria-item,
    .galeria-track .galeria-item {
        background: transparent !important;
        border: none !important;
    }

    .galeria-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.95);
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: all 0.3s ease;
        font-size: 1.3rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .galeria-nav:hover:not(:disabled) {
        background: var(--primary-color);
        color: white;
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
    }

    .galeria-nav:disabled {
        opacity: 0.3;
        cursor: not-allowed;
        border-color: #ccc;
        color: #ccc;
    }

    .galeria-prev {
        left: 5px;
    }

    .galeria-next {
        right: 5px;
    }

    .galeria-indicators {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 20px;
    }

    .galeria-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        border: 2px solid var(--primary-color);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .galeria-indicator.active {
        background: var(--primary-color);
        transform: scale(1.2);
    }

    /* Responsividade da Galeria */
    @media (max-width: 768px) {
        .galeria-item {
            min-width: 200px;
            max-width: 240px;
        }
        
        .galeria-nav {
            width: 38px;
            height: 38px;
            font-size: 1.1rem;
        }
        
        .galeria-prev {
            left: -10px;
        }
        
        .galeria-next {
            right: -10px;
        }
    }

    @media (max-width: 480px) {
        .galeria-item {
            min-width: 180px;
            max-width: 220px;
        }
        
        .galeria-nav {
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }
        
        .galeria-prev {
            left: -5px;
        }
        
        .galeria-next {
            right: -5px;
        }
    }
</style>
@endpush

@php
    use Illuminate\Support\Carbon;

    $dashboard = trans('dashboard');
    $cards = $dashboard['general']['cards'];
    $numbers = $dashboard['numbers'];
    $days = $dashboard['days'];
    $intlLocale = $dashboard['intl_locale'] ?? str_replace('_', '-', app()->getLocale());
    $carbonLocale = str_replace('-', '_', app()->getLocale());
    Carbon::setLocale($carbonLocale);

    $formatNumber = function ($value) use ($numbers) {
        return number_format((int) ($value ?? 0), 0, $numbers['decimal'], $numbers['thousand']);
    };

    $dayName = $days[Carbon::now()->dayOfWeek] ?? '';
    $periodFormat = $cards['new_month']['period_format'] ?? 'MMM/Y';
    $periodLabel = Carbon::now()->translatedFormat($periodFormat);

    $groupsChartData = ($gruposDestaque ?? collect())->map(fn ($grupo) => [
        'label' => $grupo->nome,
        'value' => (int) ($grupo->integrantes_count ?? 0),
    ]);
@endphp

<div class="container">
    <h1>{{ $dashboard['title'] }}</h1>

    <div class="info" id="dados-gerais">
        <h3>{{ $dashboard['general']['heading'] }}</h3>
        <div class="dashboard-grid">
            <div class="dashboard-cards">
                <div class="dashboard-card neutral">
                    <span class="label">{{ $cards['today']['label'] }}</span>
                    <strong>{{ Carbon::now()->format('d/m/Y') }}</strong>
                    <small>{{ $dayName }}</small>
                </div>
                <div class="dashboard-card neutral">
                    <span class="label">{{ $cards['service']['label'] }}</span>
                    @if ($culto_hoje && isset($culto_hoje[0]))
                        <strong>{{ $culto_hoje[0]->preletor ?? $cards['service']['unknown_preacher'] }}</strong>
                        <small>
                            @if ($culto_hoje[0]->evento_id)
                                {{ $culto_hoje[0]->evento->titulo }}
                            @else
                                {{ $cards['service']['no_event'] }}
                            @endif
                        </small>
                    @else
                        <strong>{{ $cards['service']['no_service'] }}</strong>
                        <small>
                            <span class="link-standard" onclick="abrirJanelaModal('{{ route('cultos.form_criar') }}')">
                                {{ $cards['service']['cta'] }}
                            </span>
                        </small>
                    @endif
                </div>
                <div class="dashboard-card">
                    <span class="label">{{ $cards['members_active']['label'] }}</span>
                    <strong>{{ $formatNumber($dashboardStats['membros_ativos'] ?? 0) }}</strong>
                    <small>{{ __('dashboard.general.cards.members_active.small', ['total' => $formatNumber($dashboardStats['membros_total'] ?? 0)]) }}</small>
                </div>
                <div class="dashboard-card">
                    <span class="label">{{ $cards['new_month']['label'] }}</span>
                    <strong>{{ $formatNumber($dashboardStats['membros_novos'] ?? 0) }}</strong>
                    <small>{{ __('dashboard.general.cards.new_month.small', ['period' => $periodLabel]) }}</small>
                </div>
                <div class="dashboard-card">
                    <span class="label">{{ $cards['members_groups']['label'] }}</span>
                    <strong>{{ $formatNumber($dashboardStats['membros_em_grupos'] ?? 0) }}</strong>
                    <small>{{ __('dashboard.general.cards.members_groups.small', ['count' => $formatNumber($dashboardStats['membros_sem_grupo'] ?? 0)]) }}</small>
                </div>
                <div class="dashboard-card">
                    <span class="label">{{ $cards['visitors']['label'] }}</span>
                    <strong>{{ $formatNumber($dashboardStats['visitantes_mes'] ?? 0) }}</strong>
                    <small>{{ __('dashboard.general.cards.visitors.small', ['count' => $formatNumber($dashboardStats['visitantes_total'] ?? 0)]) }}</small>
                </div>
                <div class="dashboard-card">
                    <span class="label">{{ $cards['structure']['label'] }}</span>
                    @php
                        $structureTotal = ($dashboardStats['grupos_total'] ?? 0) + ($dashboardStats['celulas_total'] ?? 0);
                    @endphp
                    <strong>{{ $formatNumber($structureTotal) }}</strong>
                    <small>{{ __('dashboard.general.cards.structure.small', [
                        'groups' => $formatNumber($dashboardStats['grupos_total'] ?? 0),
                        'cells' => $formatNumber($dashboardStats['celulas_total'] ?? 0),
                    ]) }}</small>
                </div>
                <div class="dashboard-card">
                    <span class="label">{{ $cards['organization']['label'] }}</span>
                    @php
                        $organizationTotal = ($dashboardStats['departamentos_total'] ?? 0) + ($dashboardStats['setores_total'] ?? 0);
                    @endphp
                    <strong>{{ $formatNumber($organizationTotal) }}</strong>
                    <small>{{ __('dashboard.general.cards.organization.small', [
                        'departments' => $formatNumber($dashboardStats['departamentos_total'] ?? 0),
                        'sectors' => $formatNumber($dashboardStats['setores_total'] ?? 0),
                    ]) }}</small>
                </div>
            </div>
            <div class="dashboard-side">
                <h4 style="margin-bottom: 12px;">{{ $dashboard['chart']['title'] }}</h4>
                <div id="groupsDashboardChart" data-chart='@json($groupsChartData)'></div>
                <div style="margin-top: 16px; font-size: 0.85rem; color: rgba(255, 255, 255, 0.75);">
                    {{ __('dashboard.chart.subtitle', [
                        'services' => $formatNumber($dashboardStats['cultos_proximos'] ?? 0),
                        'events' => $formatNumber($dashboardStats['eventos_proximos'] ?? 0),
                    ]) }}
                </div>
            </div>
        </div>
    </div>

    @if (module_enabled('recados'))
        @php
            $recadoDeleteRoute = Route::has('recados.excluir');
        @endphp
        <div class="info" id="recados">
            <h3>{{ $dashboard['recados']['heading'] }}</h3>
            <div class="card-container">
                @if ($recados && $recados->count())
                    @foreach ($recados as $item)
                        <div class="card card-recado info_item center" style="max-width: 50vw;">
                            @if ($recadoDeleteRoute)
                                <form action="{{ route('recados.excluir', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-confirm"><i class="bi bi-check-circle"></i></button>
                                </form>
                            @endif
                            <p><i class="bi bi-exclamation-triangle"></i> {{ $item->mensagem }}</p>
                            @if ($item->membro)
                                <small class="hint right">{{ __('dashboard.recados.sent_by', ['name' => $item->membro->nome]) }}</small>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="card">
                        <p><i class="bi bi-exclamation-triangle"></i> {{ $dashboard['recados']['empty'] }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="info">
        <h3>{{ $dashboard['events']['heading'] }}</h3>
        <div class="card-container">
            @if ($eventos && $eventos->count())
                @foreach ($eventos as $item)
                    @php $eventDate = new DateTime($item->data_inicio); @endphp
                    <div class="card">
                        <div class="card-date"><i class="bi bi-calendar-event"></i> {{ $eventDate->format('d/m') }}</div>
                        <div class="card-title">{{ $item->titulo }}</div>
                        <div class="card-owner">{{ optional($item->grupo)->nome ?? $dashboard['events']['general_owner'] }}</div>
                        <div class="card-description">{{ $item->descricao ?? '' }}</div>
                    </div>
                @endforeach
            @else
                <div class="card">
                    <p><i class="bi bi-exclamation-triangle"></i> {{ $dashboard['events']['empty'] }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="info">
        <h3>{{ $dashboard['birthdays']['heading'] }}</h3>
        <div id="aniversariantes-galeria" class="aniversariantes-container">
            @if ($aniversariantes && $aniversariantes->count())
                <button class="galeria-nav galeria-prev" aria-label="Anterior">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <div class="galeria-viewport">
                    <div class="galeria-track">
                        @foreach ($aniversariantes as $item)
                            @php $birthDate = new DateTime($item->data_nascimento); @endphp
                            <div class="galeria-item">
                                <div class="card-date"><i class="bi bi-cake2"></i> {{ $birthDate->format('d/m') }}</div>
                                <div class="card-title">{{ $item->nome }}</div>
                                <div class="card-owner">
                                    @if ($item->ministerio)
                                        {{ $item->ministerio->titulo }}
                                    @else
                                        {{ $dashboard['birthdays']['no_ministry'] }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <button class="galeria-nav galeria-next" aria-label="Próximo">
                    <i class="bi bi-chevron-right"></i>
                </button>
                <div class="galeria-indicators"></div>
            @else
                <div class="card">
                    <p><i class="bi bi-cake2"></i> {{ $dashboard['birthdays']['empty'] }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="info">
        <h3>{{ $dashboard['visitors']['heading'] }}</h3>
        <div id="visitantes-galeria" class="visitantes-container">
            @if ($visitantes && $visitantes->count())
                <button class="galeria-nav galeria-prev" aria-label="Anterior">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <div class="galeria-viewport">
                    <div class="galeria-track">
                        @foreach ($visitantes as $visitante)
                            <div class="galeria-item">
                                <div class="card-title"><i class="bi bi-person-raised-hand"></i> {{ $visitante->nome }}</div>
                                <div class="card-owner">{{ optional($visitante->sit_visitante)->titulo }}</div>
                                <div class="card-description">{{ $visitante->observacoes }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <button class="galeria-nav galeria-next" aria-label="Próximo">
                    <i class="bi bi-chevron-right"></i>
                </button>
                <div class="galeria-indicators"></div>
            @else
                <div class="card">
                    <p><i class="bi bi-exclamation-triangle"></i> {{ $dashboard['visitors']['empty'] }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Gráfico de Grupos
        const chartEl = document.getElementById('groupsDashboardChart');
        if (chartEl) {
            let data = [];
            try {
                data = JSON.parse(chartEl.dataset.chart || '[]');
            } catch (error) {
                console.warn('Chart data parse error.', error);
            }

            if (!Array.isArray(data) || data.length === 0) {
                chartEl.innerHTML = `<p class="hint">{{ $dashboard['chart']['empty'] }}</p>`;
            } else {
                const formatter = new Intl.NumberFormat(@json($intlLocale));
                const maxValue = data.reduce((max, item) => Math.max(max, Number(item.value) || 0), 0) || 1;

                chartEl.innerHTML = data.map((item) => {
                    const absolute = Number(item.value) || 0;
                    const width = Math.max(8, Math.round((absolute / maxValue) * 100));
                    return `
                        <div class="chart-item">
                            <header>
                                <span>${item.label}</span>
                                <span class="value">${formatter.format(absolute)}</span>
                            </header>
                            <div class="chart-bar"><span style="width: ${width}%;"></span></div>
                        </div>
                    `;
                }).join('');
            }
        }

        // Galeria de Aniversariantes
        const galeriaContainer = document.getElementById('aniversariantes-galeria');
        if (galeriaContainer) {
            const track = galeriaContainer.querySelector('.galeria-track');
            const prevBtn = galeriaContainer.querySelector('.galeria-prev');
            const nextBtn = galeriaContainer.querySelector('.galeria-next');
            const indicators = galeriaContainer.querySelector('.galeria-indicators');
            const items = galeriaContainer.querySelectorAll('.galeria-item');

            if (track && items.length > 0) {
                let currentPage = 0;
                let itemsPerPage = 1;

                // Calcula quantos items completos cabem na tela
                function calculateItemsPerPage() {
                    const viewport = galeriaContainer.querySelector('.galeria-viewport');
                    const viewportWidth = viewport.offsetWidth;
                    const itemWidth = items[0].offsetWidth;
                    const gap = 15;
                    
                    // Calcula quantos items completos cabem (arredonda para baixo)
                    itemsPerPage = Math.floor(viewportWidth / (itemWidth + gap));
                    itemsPerPage = Math.max(1, itemsPerPage); // No mínimo 1
                    
                    return itemsPerPage;
                }

                // Calcula total de páginas
                function getTotalPages() {
                    return Math.ceil(items.length / itemsPerPage);
                }

                // Atualiza a posição da galeria
                function updateGallery() {
                    const itemWidth = items[0].offsetWidth;
                    const gap = 15;
                    const trackPadding = 15; // Padding do track
                    const offset = (currentPage * itemsPerPage * (itemWidth + gap)) + trackPadding;
                    track.style.transform = `translateX(-${offset}px)`;
                    
                    // Atualiza botões
                    prevBtn.disabled = currentPage === 0;
                    nextBtn.disabled = currentPage >= getTotalPages() - 1;
                    
                    // Atualiza indicadores
                    updateIndicators();
                }

                // Cria indicadores de página
                function createIndicators() {
                    const totalPages = getTotalPages();
                    if (totalPages <= 1) {
                        indicators.innerHTML = '';
                        return;
                    }
                    
                    indicators.innerHTML = '';
                    for (let i = 0; i < totalPages; i++) {
                        const indicator = document.createElement('div');
                        indicator.className = 'galeria-indicator';
                        if (i === currentPage) indicator.classList.add('active');
                        indicator.addEventListener('click', () => {
                            currentPage = i;
                            updateGallery();
                        });
                        indicators.appendChild(indicator);
                    }
                }

                // Atualiza indicadores ativos
                function updateIndicators() {
                    const allIndicators = indicators.querySelectorAll('.galeria-indicator');
                    allIndicators.forEach((ind, idx) => {
                        ind.classList.toggle('active', idx === currentPage);
                    });
                }

                // Navegação
                prevBtn.addEventListener('click', () => {
                    if (currentPage > 0) {
                        currentPage--;
                        updateGallery();
                    }
                });

                nextBtn.addEventListener('click', () => {
                    if (currentPage < getTotalPages() - 1) {
                        currentPage++;
                        updateGallery();
                    }
                });

                // Suporte a touch/swipe
                let touchStartX = 0;
                let touchEndX = 0;
                
                track.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                });
                
                track.addEventListener('touchend', (e) => {
                    touchEndX = e.changedTouches[0].screenX;
                    handleSwipe();
                });
                
                function handleSwipe() {
                    const swipeThreshold = 50;
                    if (touchStartX - touchEndX > swipeThreshold) {
                        // Swipe left (next)
                        nextBtn.click();
                    } else if (touchEndX - touchStartX > swipeThreshold) {
                        // Swipe right (prev)
                        prevBtn.click();
                    }
                }

                // Inicialização e resize
                function init() {
                    calculateItemsPerPage();
                    currentPage = 0;
                    createIndicators();
                    updateGallery();
                }

                init();
                
                let resizeTimeout;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(init, 250);
                });
            }
        }

        // Galeria de Visitantes (mesma lógica da galeria de aniversariantes)
        const visitantesContainer = document.getElementById('visitantes-galeria');
        if (visitantesContainer) {
            const track = visitantesContainer.querySelector('.galeria-track');
            const prevBtn = visitantesContainer.querySelector('.galeria-prev');
            const nextBtn = visitantesContainer.querySelector('.galeria-next');
            const indicators = visitantesContainer.querySelector('.galeria-indicators');
            const items = visitantesContainer.querySelectorAll('.galeria-item');

            if (track && items.length > 0) {
                let currentPage = 0;
                let itemsPerPage = 1;

                function calculateItemsPerPage() {
                    const viewport = visitantesContainer.querySelector('.galeria-viewport');
                    const viewportWidth = viewport.offsetWidth;
                    const itemWidth = items[0].offsetWidth;
                    const gap = 15;
                    itemsPerPage = Math.floor(viewportWidth / (itemWidth + gap));
                    itemsPerPage = Math.max(1, itemsPerPage);
                    return itemsPerPage;
                }

                function getTotalPages() {
                    return Math.ceil(items.length / itemsPerPage);
                }

                function updateGallery() {
                    const itemWidth = items[0].offsetWidth;
                    const gap = 15;
                    const trackPadding = 15; // Padding do track
                    const offset = (currentPage * itemsPerPage * (itemWidth + gap)) + trackPadding;
                    track.style.transform = `translateX(-${offset}px)`;
                    prevBtn.disabled = currentPage === 0;
                    nextBtn.disabled = currentPage >= getTotalPages() - 1;
                    updateIndicators();
                }

                function createIndicators() {
                    const totalPages = getTotalPages();
                    if (totalPages <= 1) {
                        indicators.innerHTML = '';
                        return;
                    }
                    indicators.innerHTML = '';
                    for (let i = 0; i < totalPages; i++) {
                        const indicator = document.createElement('div');
                        indicator.className = 'galeria-indicator';
                        if (i === currentPage) indicator.classList.add('active');
                        indicator.addEventListener('click', () => {
                            currentPage = i;
                            updateGallery();
                        });
                        indicators.appendChild(indicator);
                    }
                }

                function updateIndicators() {
                    const allIndicators = indicators.querySelectorAll('.galeria-indicator');
                    allIndicators.forEach((ind, idx) => {
                        ind.classList.toggle('active', idx === currentPage);
                    });
                }

                prevBtn.addEventListener('click', () => {
                    if (currentPage > 0) {
                        currentPage--;
                        updateGallery();
                    }
                });

                nextBtn.addEventListener('click', () => {
                    if (currentPage < getTotalPages() - 1) {
                        currentPage++;
                        updateGallery();
                    }
                });

                let touchStartX = 0;
                let touchEndX = 0;
                
                track.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                });
                
                track.addEventListener('touchend', (e) => {
                    touchEndX = e.changedTouches[0].screenX;
                    handleSwipe();
                });
                
                function handleSwipe() {
                    const swipeThreshold = 50;
                    if (touchStartX - touchEndX > swipeThreshold) {
                        nextBtn.click();
                    } else if (touchEndX - touchStartX > swipeThreshold) {
                        prevBtn.click();
                    }
                }

                function init() {
                    calculateItemsPerPage();
                    currentPage = 0;
                    createIndicators();
                    updateGallery();
                }

                init();
                
                let resizeTimeout;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(init, 250);
                });
            }
        }
    });
</script>
@endpush
@endsection
