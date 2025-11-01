@extends('layouts.main')

@section('title', 'Extensões | ' . $appName)

@section('content')
<div class="container">
    <h1>Extensões</h1>
    <div class="info">
        <h3>Integrações disponíveis</h3>
        <p class="card-description">Explore o catálogo de extensões, visualize valores e detalhes e habilite o que faz sentido para a congregação atual.</p>

        <div class="extension-grid">
            @forelse ($modules as $module)
                @php
                    $metadata = $module['metadata'] ?? [];
                    $rawHighlights = [];

                    if (is_array($metadata)) {
                        if (!empty($metadata['highlights']) && is_array($metadata['highlights'])) {
                            $rawHighlights = $metadata['highlights'];
                        } elseif (!empty($metadata['features']) && is_array($metadata['features'])) {
                            $rawHighlights = $metadata['features'];
                        } elseif (!empty($metadata['highlights']) && is_string($metadata['highlights'])) {
                            $rawHighlights = array_filter(array_map('trim', explode('|', $metadata['highlights'])));
                        }
                    }

                    $highlights = array_slice(array_filter($rawHighlights), 0, 3);
                    $iconPath = $module['icon'] ?? null;

                    if ($iconPath && ! \Illuminate\Support\Str::startsWith($iconPath, ['http://', 'https://', 'data:'])) {
                        $iconPath = asset($iconPath);
                    }

                    $priceLabel = isset($module['price']) ? 'R$ ' . number_format($module['price'], 2, ',', '.') : 'Gratuita';
                    $typeMap = [
                        'gratuita' => 'Plano gratuito',
                        'paga' => 'Compra única',
                        'assinatura' => 'Assinatura',
                        'one_time' => 'Licença vitalícia',
                    ];
                    $typeLabel = $typeMap[$module['type'] ?? ''] ?? ucfirst($module['type'] ?? 'gratuita');
                    $statusLabel = $module['enabled']
                        ? 'Ativo'
                        : ($module['is_available'] ? 'Disponível' : 'Em breve');
                    $statusClass = $module['enabled']
                        ? 'is-live'
                        : ($module['is_available'] ? 'is-open' : 'is-coming');

                    $initial = mb_strtoupper(mb_substr($module['name'] ?? 'E', 0, 1));

                    $buttonIcon = 'bi-cart-plus';
                    $buttonLabel = 'Adquirir';

                    if (! $module['enabled']) {
                        if (($module['type'] ?? 'gratuita') === 'gratuita') {
                            $buttonIcon = 'bi-download';
                            $buttonLabel = 'Ativar grátis';
                        } elseif (($module['type'] ?? '') === 'assinatura') {
                            $buttonIcon = 'bi-credit-card';
                            $buttonLabel = 'Assinar';
                        }
                    } else {
                        $buttonIcon = 'bi-pause-circle';
                        $buttonLabel = 'Desativar';
                    }
                @endphp

                <div class="extension-card {{ $module['enabled'] ? 'is-active' : 'is-inactive' }} {{ $module['is_available'] ? '' : 'is-unavailable' }}">
                    <div class="extension-card-header">
                        <div class="extension-card-meta">
                            <div class="extension-icon">
                                @if ($iconPath)
                                    <img src="{{ $iconPath }}" alt="Ícone da extensão {{ $module['name'] }}" loading="lazy">
                                @else
                                    <span>{{ $initial }}</span>
                                @endif
                            </div>
                            <div class="extension-card-meta-info">
                                @if (!empty($module['category']))
                                    <span class="extension-category">{{ $module['category'] }}</span>
                                @endif
                                <h4>{{ $module['name'] }}</h4>
                                <div class="extension-pricing">
                                    <span class="extension-price">{{ $priceLabel }}</span>
                                    <span class="extension-type">{{ $typeLabel }}</span>
                                </div>
                            </div>
                        </div>
                        <span class="extension-status {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    @if (!empty($module['description']))
                        <p class="extension-description">{{ $module['description'] }}</p>
                    @endif

                    @if (!empty($highlights))
                        <ul class="extension-highlights">
                            @foreach ($highlights as $highlight)
                                <li><i class="bi bi-check-circle"></i> {{ $highlight }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="extension-card-footer">
                        <div class="extension-footnote">
                            <span class="footnote-pill {{ $module['has_local_files'] ? 'is-installed' : 'is-remote' }}">
                                <i class="bi {{ $module['has_local_files'] ? 'bi-hdd-stack' : 'bi-cloud-download' }}"></i>
                                {{ $module['has_local_files'] ? 'Instalada no servidor' : 'Disponível para instalação' }}
                            </span>
                            @if (!empty($metadata['versao']))
                                <span class="footnote-pill">
                                    <i class="bi bi-tag"></i> Versão {{ $metadata['versao'] }}
                                </span>
                            @endif
                        </div>

                        @if ($module['is_available'] && $module['has_local_files'])
                            <form action="{{ route('extensoes.update', $module['key']) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="enabled" value="{{ $module['enabled'] ? 0 : 1 }}">
                                <button class="btn" type="submit">
                                    <i class="bi {{ $buttonIcon }}"></i>
                                    {{ $buttonLabel }}
                                </button>
                            </form>
                        @else
                            <button class="btn btn-disabled" type="button" disabled>
                                <i class="bi {{ $module['is_available'] ? 'bi-cloud-arrow-down' : 'bi-lock' }}"></i>
                                {{ $module['has_local_files'] ? 'Indisponível' : 'Instalar módulo' }}
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card">
                    <p><i class="bi bi-exclamation-triangle"></i> Nenhuma extensão encontrada.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@once
    @push('styles')
        <style>
            .extension-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
                gap: 1.5rem;
                margin-top: 1.5rem;
            }

            .extension-card {
                border: 1px solid rgba(10, 25, 41, 0.12);
                border-radius: 12px;
                padding: 1.5rem;
                background: rgba(255, 255, 255, 0.8);
                display: flex;
                flex-direction: column;
                gap: 1rem;
                box-shadow: 0 12px 30px -28px rgba(10, 25, 41, 0.7);
            }

            .extension-card.is-inactive {
                opacity: 0.85;
            }

            .extension-card.is-unavailable {
                opacity: 0.7;
            }

            .extension-card-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 1rem;
            }

            .extension-card-meta {
                display: flex;
                align-items: center;
                gap: 1rem;
                flex: 1;
                min-width: 0;
            }

            .extension-icon {
                width: 48px;
                height: 48px;
                border-radius: 12px;
                background: rgba(10, 25, 41, 0.08);
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                font-weight: 600;
                color: rgba(10, 25, 41, 0.65);
                flex-shrink: 0;
            }

            .extension-icon img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .extension-card-meta-info h4 {
                margin: 0;
                font-size: 1.1rem;
                color: rgba(10, 25, 41, 0.9);
            }

            .extension-category {
                display: inline-block;
                font-size: 0.7rem;
                text-transform: uppercase;
                letter-spacing: 0.12em;
                color: rgba(10, 25, 41, 0.5);
                margin-bottom: 0.15rem;
            }

            .extension-pricing {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                margin-top: 0.35rem;
                font-size: 0.9rem;
                flex-wrap: wrap;
            }

            .extension-price {
                font-weight: 600;
                color: rgba(10, 25, 41, 0.85);
            }

            .extension-type {
                color: rgba(10, 25, 41, 0.55);
            }

            .extension-status {
                font-size: 0.72rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                padding: 0.25rem 0.75rem;
                border-radius: 999px;
                background: rgba(10, 25, 41, 0.12);
                color: rgba(10, 25, 41, 0.65);
                white-space: nowrap;
            }

            .extension-status.is-live {
                background: rgba(40, 167, 69, 0.18);
                color: rgba(25, 135, 84, 0.95);
            }

            .extension-status.is-open {
                background: rgba(13, 110, 253, 0.15);
                color: rgba(13, 110, 253, 0.9);
            }

            .extension-status.is-coming {
                background: rgba(10, 25, 41, 0.08);
                color: rgba(10, 25, 41, 0.55);
            }

            .extension-description {
                margin: 0;
                color: rgba(10, 25, 41, 0.75);
                line-height: 1.6;
            }

            .extension-highlights {
                list-style: none;
                margin: 0;
                padding: 0;
                display: grid;
                gap: 0.4rem;
            }

            .extension-highlights li {
                display: flex;
                align-items: center;
                gap: 0.35rem;
                font-size: 0.85rem;
                color: rgba(10, 25, 41, 0.7);
            }

            .extension-highlights i {
                color: var(--secondary-color);
            }

            .extension-card-footer {
                margin-top: auto;
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }

            .extension-footnote {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
                font-size: 0.78rem;
                color: rgba(10, 25, 41, 0.6);
            }

            .footnote-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.25rem 0.65rem;
                border-radius: 999px;
                background: rgba(10, 25, 41, 0.08);
                color: inherit;
            }

            .footnote-pill.is-installed {
                background: rgba(100, 73, 162, 0.14);
                color: rgba(100, 73, 162, 0.95);
            }

            .footnote-pill.is-remote {
                background: rgba(10, 25, 41, 0.08);
                color: rgba(10, 25, 41, 0.55);
            }

            .extension-card form {
                margin-top: 0;
            }

            .extension-card .btn {
                width: 100%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
            }

            .extension-card .btn.btn-disabled {
                cursor: not-allowed;
                opacity: 0.6;
            }
        </style>
    @endpush
@endonce
