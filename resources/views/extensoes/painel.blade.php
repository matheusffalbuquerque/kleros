@extends('layouts.main')

@section('title', 'Extensões | ' . $appName)

@section('content')
<div class="container">
    <h1>Extensões</h1>
    <div class="info">
        <h3>Integrações disponíveis</h3>
        <p class="card-description">Ative ou desative os módulos conforme a necessidade da congregação atual.</p>

        <div class="extension-grid">
            @forelse ($modules as $module)
                <div class="extension-card {{ $module['enabled'] ? 'is-active' : 'is-inactive' }}">
                    <div class="extension-card-header">
                        <h4>{{ $module['name'] }}</h4>
                        <span class="extension-status">
                            {{ $module['enabled'] ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    @if (!empty($module['description']))
                        <p class="extension-description">{{ $module['description'] }}</p>
                    @endif

                    <form action="{{ route('extensoes.update', $module['key']) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="enabled" value="{{ $module['enabled'] ? 0 : 1 }}">
                        <button class="btn" type="submit">
                            <i class="bi {{ $module['enabled'] ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                            {{ $module['enabled'] ? 'Desativar' : 'Ativar' }}
                        </button>
                    </form>
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
                background: rgba(238, 238, 238, 0.3);
                display: flex;
                flex-direction: column;
                gap: 1rem;
                box-shadow: 0 12px 30px -28px rgba(10, 25, 41, 0.7);
            }

            .extension-card.is-inactive {
                opacity: 0.75;
            }

            .extension-card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 0.5rem;
            }

            .extension-card-header h4 {
                margin: 0;
                font-size: 1.1rem;
            }

            .extension-status {
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                padding: 0.25rem 0.75rem;
                border-radius: 999px;
                background: var(--secondary-color);
                color: var(--secondary-contrast);
            }

            .extension-card.is-inactive .extension-status {
                background: rgba(10, 25, 41, 0.1);
                color: rgba(10, 25, 41, 0.6);
            }

            .extension-description {
                margin: 0;
                color: rgba(10, 25, 41, 0.75);
                line-height: 1.6;
            }

            .extension-card form {
                margin-top: auto;
            }

            .extension-card .btn {
                width: 100%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
            }
        </style>
    @endpush
@endonce
