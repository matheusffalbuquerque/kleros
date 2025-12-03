@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')
<div class="container">
    <h1>Projetos</h1>

    <div class="info projetos-wrapper">
        <h3>Catálogo de Projetos</h3>

        <form action="{{ route('projetos.painel') }}" method="get" class="projetos-search">
            <div class="search-panel">
                <div class="search-panel-item">
                    <label for="projeto-filtro"><i class="bi bi-funnel"></i></label>
                    <select name="filtro" id="projeto-filtro">
                        <option value="titulo" @selected(request('filtro') === 'titulo')>Título</option>
                        <option value="responsavel" @selected(request('filtro') === 'responsavel')>Responsável</option>
                    </select>
                </div>
                <div class="search-panel-item">
                    <label for="projeto-busca"><i class="bi bi-search"></i></label>
                    <input type="text" name="termo" id="projeto-busca" placeholder="Pesquisar projetos" value="{{ request('termo') }}">
                </div>
                <div class="search-panel-item">
                    <button type="submit"><i class="bi bi-search"></i> Buscar</button>
                    @if(auth()->check() && auth()->user()->hasAnyRole(['gestor', 'admin', 'kleros']))
                        <button type="button" class="btn btn-secondary" onclick="abrirJanelaModal('{{ route('projetos.form_criar') }}')"><i class="bi bi-plus-circle"></i> Novo projeto</button>
                    @endif
                </div>
            </div>
        </form>

        
        <section class="projetos-listagem">
            <header class="projetos-section-header">
                <span class="projetos-section-icon"><i class="bi bi-kanban"></i></span>
                <h4 class="projetos-section-title">Todos os projetos</h4>
            </header>

            <div class="projetos-grid">
                @forelse (($projects ?? collect()) as $project)
                    @php
                        $accentColor = $project->cor ?? '#1f2937';
                        $cover = $project->capa_url ?? null;
                        $coverVar = $cover ? "url('{$cover}')" : 'none';
                    @endphp
                    <article class="projeto-card" style="--accent-color: {{ $accentColor }}; --accent-cover: {{ $coverVar }};">
                        <div class="projeto-card__accent"></div>
                        <div class="projeto-card__body">
                            <h5 class="projeto-card__title">{{ substr($project->nome, 0, 50) ?? 'Projeto sem título' }}</h5>
                            <p class="projeto-card__meta">Atualizado em {{ optional($project->updated_at)->format('d/m/Y') }}</p>
                            <a href="{{ route('projetos.exibir', $project) }}" class="projeto-card__link">Abrir projeto <i class="bi bi-arrow-right-short"></i></a>
                        </div>
                    </article>
                @empty
                    <div class="projetos-empty">
                        <p>Nenhum projeto encontrado. Utilize os filtros para refinar sua busca.</p>
                    </div>
                @endforelse
            </div>

            @if(($projects ?? null) instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="projetos-pagination">
                    {{ $projects->links() }}
                </div>
            @endif
        </section>
    </div>
</div>
@endsection

@push('styles')
<style>
    .projetos-wrapper {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .projetos-search {
        margin-bottom: 1rem;
    }

    .projetos-section-header {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin-bottom: 1rem;
        color: var(--text-color);
    }

    .projetos-section-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--primary-color);
    }

    .projetos-section-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .projetos-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: flex-start;
        align-items: stretch;
    }

    .projeto-card {
        --accent-color: #1f2937;
        --accent-cover: none;
        display: flex;
        flex-direction: column;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 18px 32px -26px rgba(15, 23, 42, 0.4);
        transition: transform .2s ease, box-shadow .2s ease;
        overflow: hidden;
        border: 1px solid var(--secondary-color);
        width: 150px;
    }

    .projeto-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 28px 40px -28px rgba(15, 23, 42, 0.5);
    }

    .projeto-card__accent {
        height: 60px;
        background-color: var(--accent-color);
        background-image: linear-gradient(180deg, rgba(15, 23, 42, 0.12), rgba(15, 23, 42, 0)), var(--accent-cover);
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 18px 18px 0 0;
        position: relative;
        overflow: hidden;
    }

    .projeto-card__accent::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 22px;
        background: var(--accent-color);
        opacity: 0.92;
    }

    .projeto-card__body {
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .projeto-card__title {
        margin: 0;
        font-size: 1rem;
        color: var(--text-color);
    }

    .projeto-card__meta {
        margin: 0;
        font-size: 0.85rem;
        color: var(--text-color);
    }

    .projeto-card__link {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.85rem;
        color: var(--secondary-color);
        text-decoration: none;
        font-weight: 600;
    }

    .projetos-pagination {
        margin-top: 1.5rem;
    }

    .projetos-empty {
        grid-column: 1 / -1;
        padding: 2rem;
        border: 2px dashed var(--secondary-color);
        border-radius: 16px;
        text-align: center;
        color: var(--text-color);
    }

    @media (max-width: 768px) {
        .projeto-card {
            width: 140px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if (session('success'))
            flashMsg(@json(session('success')), 'success');
        @endif

        @if (session('error'))
            flashMsg(@json(session('error')), 'error');
        @endif
    });
</script>
@endpush
