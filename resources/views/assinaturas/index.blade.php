@extends('layouts.main')

@section('title', 'Assinaturas | ' . $appName)

@section('content')
<div class="container">
    <h1>Assinaturas</h1>
    <div class="info">
        <h3>Catálogo de produtos recorrentes</h3>
        <p class="card-description">
            Explore as opções disponíveis para assinatura, filtre por tipo de conteúdo e descubra novas formas de engajar a sua congregação.
        </p>

        <form method="GET" action="{{ route('assinaturas.index') }}">
            <div class="search-panel">
                <div class="search-panel-item">
                    <label for="tipo">Tipo de assinatura</label>
                    <select name="tipo" id="tipo" class="form-control">
                        <option value="0" {{ $tipoSelecionado === 0 ? 'selected' : '' }}>Todas as categorias</option>
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo->id }}" {{ $tipoSelecionado === $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nome }}
                                @php
                                    $totalTipo = $resumoTipos[$tipo->id] ?? 0;
                                @endphp
                                @if ($totalTipo)
                                    ({{ $totalTipo }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="search-panel-item search-panel-item--stretch">
                    <label for="q">Buscar</label>
                    <div class="search-input-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="search" name="q" id="q" placeholder="Título ou descrição"
                            value="{{ $busca }}">
                    </div>
                </div>

                <div class="search-panel-item search-panel-item--compact">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>

        @if ($tipos->isNotEmpty())
            <div class="subscription-type-chips">
                <a href="{{ route('assinaturas.index') }}"
                    class="chip {{ $tipoSelecionado === 0 ? 'chip--active' : '' }}">
                    <span class="chip-label">Todos</span>
                    <span class="chip-count">{{ $resumoTipos->sum() ?: $produtos->count() }}</span>
                </a>
                @foreach ($tipos as $tipo)
                    <a href="{{ route('assinaturas.index', ['tipo' => $tipo->id] + ($busca ? ['q' => $busca] : [])) }}"
                        class="chip {{ $tipoSelecionado === $tipo->id ? 'chip--active' : '' }}">
                        <span class="chip-label">{{ $tipo->nome }}</span>
                        <span class="chip-count">{{ $resumoTipos[$tipo->id] ?? 0 }}</span>
                    </a>
                @endforeach
            </div>
        @endif

        <div class="subscription-collection">
            <div class="product-card-grid">
                @forelse ($produtos as $produto)
                    @php
                        $priceLabel = 'R$ ' . number_format($produto->preco, 2, ',', '.');
                        $statusLabel = $produto->ativo ? 'Disponível' : 'Indisponível';
                        $statusClass = $produto->ativo ? 'product-status--live' : 'product-status--inactive';
                        $launchDate = $produto->data_lancamento?->format('d/m/Y');
                    @endphp

                    <article class="product-card {{ $produto->ativo ? 'is-active' : 'is-inactive' }}">
                        <div class="product-card-top">
                            <span class="product-type">{{ $produto->tipo->nome ?? 'Sem categoria' }}</span>
                            <span class="product-status {{ $statusClass }}">{{ $statusLabel }}</span>
                        </div>

                        {{-- Descomentar quando tiver imagem (se disponível) --}}
                        {{-- @if ($produto->capa_url)
                            <div class="product-cover">
                                <img src="{{ $produto->capa_url }}" alt="Capa do produto {{ $produto->titulo }}" loading="lazy">
                            </div>
                        @endif --}}

                        <h4 class="product-title">{{ $produto->titulo }}</h4>
                        @if ($produto->descricao)
                            <p class="product-description">{{ \Illuminate\Support\Str::limit($produto->descricao, 150) }}</p>
                        @endif

                        <div class="product-meta">
                            <div class="product-meta-item">
                                <span class="product-meta-label">Valor base</span>
                                <span class="product-meta-value">{{ $priceLabel }}</span>
                            </div>

                            @if ($launchDate)
                                <div class="product-meta-item">
                                    <span class="product-meta-label">Lançamento</span>
                                    <span class="product-meta-value">{{ $launchDate }}</span>
                                </div>
                            @endif

                            @if ($produto->arquivo_url)
                                <div class="product-meta-item">
                                    <span class="product-meta-label">Conteúdo</span>
                                    <span class="product-meta-value">
                                        <a class="link-standard" href="{{ $produto->arquivo_url }}" target="_blank" rel="noopener">
                                            Ver conteúdo
                                        </a>
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="product-actions">
                            <button type="button" class="btn btn-secondary">
                                <i class="bi bi-credit-card"></i>
                                Gerenciar oferta
                            </button>
                        </div>
                    </article>
                @empty
                    <div class="card card-empty">
                        <p><i class="bi bi-exclamation-triangle"></i> Nenhum produto de assinatura encontrado.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@once
    @push('styles')
        <style>
            .subscription-collection {
                margin-top: 2rem;
            }

            .product-card-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
                gap: 1.5rem;
            }

            .product-card {
                --card-bg: rgba(var(--kleros-bg-rgb, 255, 255, 255), 0.82);
                --card-border: rgba(var(--kleros-text-rgb, 10, 25, 41), 0.16);
                --card-text: rgba(13, 23, 37, 0.92);
                --card-muted: rgba(13, 23, 37, 0.66);

                border: 1px solid var(--card-border);
                border-radius: 14px;
                padding: 1.5rem;
                background: var(--card-bg);
                color: var(--card-text);
                display: flex;
                flex-direction: column;
                gap: 1rem;
                box-shadow: 0 18px 35px -32px rgba(0, 0, 0, 0.35);
                backdrop-filter: blur(4px);
                transition: transform 0.2s ease, box-shadow 0.2s ease, border 0.2s ease;
                position: relative;
                min-height: 280px;
            }

            .product-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 26px 48px -36px rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.45);
                border-color: rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.35);
            }

            .product-type {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.35rem 0.75rem;
                border-radius: 999px;
                font-weight: 600;
                font-size: 0.75rem;
                background: rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.12);
                color: rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.9);
            }

            .product-status {
                font-size: 0.75rem;
                font-weight: 600;
                padding: 0.3rem 0.8rem;
                border-radius: 999px;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .product-status--live {
                background: rgba(16, 165, 80, 0.14);
                color: #0f7a46;
            }

            .product-status--inactive {
                background: rgba(165, 16, 16, 0.14);
                color: #7a0f0f;
            }

            .product-title {
                margin: 0;
                font-size: 1.25rem;
                font-weight: 700;
                color: var(--card-text);
            }

            .product-description {
                margin: 0;
                color: var(--card-muted);
                line-height: 1.45;
            }

            .product-details {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
                gap: 1rem;
                margin: 0;
            }

            .product-details dt {
                font-size: 0.75rem;
                font-weight: 600;
                color: rgba(13, 23, 37, 0.58);
                text-transform: uppercase;
                letter-spacing: 0.06em;
                margin-bottom: 0.25rem;
            }

            .product-details dd {
                margin: 0;
                font-weight: 600;
                color: var(--card-text);
            }

            .product-media-preview {
                display: flex;
                gap: 0.75rem;
                align-items: center;
            }

            .product-media-preview .media-thumb {
                width: 64px;
                height: 64px;
                border-radius: 12px;
                overflow: hidden;
                border: 1px solid rgba(var(--kleros-text-rgb, 10, 25, 41), 0.12);
                background: rgba(0, 0, 0, 0.08);
                flex-shrink: 0;
            }

            .product-media-preview img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .product-media-preview .media-link {
                color: rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.9);
                font-weight: 600;
                text-decoration: none;
            }

            .product-meta {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
                gap: 0.75rem;
            }

            .product-meta-item {
                display: flex;
                flex-direction: column;
                gap: 0.15rem;
            }

            .product-meta-label {
                font-size: 0.75rem;
                font-weight: 600;
                color: rgba(13, 23, 37, 0.54);
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .product-meta-value {
                font-weight: 600;
                color: var(--card-text);
            }

            .product-cover {
                border-radius: 12px;
                overflow: hidden;
                border: 1px solid rgba(0, 0, 0, 0.08);
                max-height: 140px;
            }

            .product-cover img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .product-actions {
                margin-top: auto;
                display: flex;
                justify-content: flex-end;
            }

            .subscription-type-chips {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
                margin-top: 1.5rem;
            }

            .chip {
                --chip-bg: rgba(var(--kleros-bg-rgb, 255, 255, 255), 0.75);
                --chip-border: rgba(var(--kleros-text-rgb, 10, 25, 41), 0.18);
                --chip-text: rgba(var(--kleros-text-rgb, 10, 25, 41), 0.85);
                --chip-count-bg: rgba(var(--kleros-text-rgb, 10, 25, 41), 0.12);
                --chip-accent: var(--kleros-primary, #1060a5);

                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.45rem 0.85rem;
                border-radius: 999px;
                border: 1px solid var(--chip-border);
                background: var(--chip-bg);
                color: var(--chip-text);
                text-decoration: none;
                transition: background 0.2s ease, color 0.2s ease, border 0.2s ease, transform 0.2s ease;
                backdrop-filter: blur(4px);
            }

            .chip:hover {
                border-color: rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.45);
                background: rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.12);
                color: rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.9);
                transform: translateY(-1px);
            }

            .chip--active {
                background: rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.16);
                border-color: rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.55);
                color: rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.95);
                box-shadow: 0 6px 16px -14px rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.9);
            }

            .chip-label {
                font-weight: 600;
            }

            .chip-count {
                font-weight: 600;
                font-size: 0.75rem;
                background: var(--chip-count-bg);
                color: inherit;
                padding: 0.15rem 0.5rem;
                border-radius: 999px;
            }

            .search-panel-item--stretch {
                flex: 1;
            }

            .search-panel-item--compact {
                width: 160px;
            }

            .search-input-wrapper {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                border: 1px solid rgba(10, 25, 41, 0.2);
                border-radius: 10px;
                padding: 0.35rem 0.75rem;
                background: #fff;
            }

            .search-input-wrapper input {
                border: none;
                outline: none;
                flex: 1;
            }

            .search-input-wrapper i {
                color: rgba(10, 25, 41, 0.5);
            }

            .card-empty {
                margin-top: 2rem;
                text-align: center;
                padding: 2rem;
            }
        </style>
    @endpush
@endonce
