@extends('layouts.main')

@section('title', 'Painel Pastoral | ' . config('app.name'))

@section('content')
<div class="container">
    <h1>Painel Pastoral</h1>

    <div class="info">
        <h3>Gerenciar conteúdos</h3>
        <form method="get" action="{{ route('areapastoral.painel') }}" class="search-panel nao-imprimir">
            <div class="search-panel-item">
                <label for="filtro-tipo"><i class="bi bi-layers"></i> Tipo</label>
                <select id="filtro-tipo" name="tipo">
                    <option value="">Todos</option>
                    @foreach (['texto' => 'Texto', 'link' => 'Link externo', 'ebook' => 'E-book', 'apostila' => 'Apostila', 'imagem' => 'Imagem', 'video' => 'Vídeo', 'geral' => 'Geral'] as $valor => $label)
                        <option value="{{ $valor }}" @selected(($filtros['tipo'] ?? '') === $valor)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="search-panel-item">
                <label for="filtro-status"><i class="bi bi-flag"></i> Status</label>
                <select id="filtro-status" name="status">
                    <option value="">Todos</option>
                    <option value="publicado" @selected(($filtros['status'] ?? '') === 'publicado')>Publicados</option>
                    <option value="rascunho" @selected(($filtros['status'] ?? '') === 'rascunho')>Rascunhos</option>
                </select>
            </div>
            <div class="search-panel-item">
                <button type="submit" class="btn"><i class="bi bi-search"></i> Filtrar</button>
            </div>
            <div class="search-panel-item">
                <button type="button" class="btn" onclick="abrirJanelaModal('{{ route('areapastoral.form_criar') }}')">
                    <i class="bi bi-plus-circle"></i> Novo conteúdo
                </button>
            </div>
        </form>

        <section class="pastoral-painel-cards">
            @forelse ($posts as $post)
                <article class="pastoral-painel-card">
                    <header>
                        <span class="badge-tipo badge-{{ $post->tipo_conteudo }}">{{ ucfirst($post->tipo_conteudo) }}</span>
                        <span class="badge-status {{ $post->status === 'publicado' ? 'publicado' : 'rascunho' }}">
                            <i class="bi {{ $post->status === 'publicado' ? 'bi-rocket-takeoff' : 'bi-pencil' }}"></i>
                            {{ ucfirst($post->status) }}
                        </span>
                    </header>
                    <div class="pastoral-painel-card-body">
                        <h4>{{ $post->titulo }}</h4>
                        <p>{{ $post->resumo ?? \Illuminate\Support\Str::limit(strip_tags($post->conteudo), 140) }}</p>
                    </div>
                    <footer>
                        <div class="pastoral-painel-meta">
                            <span><i class="bi bi-calendar3"></i> {{ optional($post->publicado_em)->format('d/m/Y') ?? $post->created_at->format('d/m/Y') }}</span>
                            <span><i class="bi bi-person"></i> {{ optional($post->autor)->nome ?? 'Equipe Pastoral' }}</span>
                        </div>
                        <div class="pastoral-painel-acoes nao-imprimir">
                            <button type="button" class="btn btn-outline" onclick="abrirJanelaModal('{{ route('areapastoral.form_criar') }}?post={{ $post->id }}')">
                                <i class="bi bi-pencil-square"></i> Editar
                            </button>
                        </div>
                    </footer>
                </article>
            @empty
                <div class="card">
                    <p><i class="bi bi-info-circle"></i> Nenhum conteúdo cadastrado ainda. Utilize o botão “Novo conteúdo”.</p>
                </div>
            @endforelse
        </section>

        @if ($posts->hasPages())
            <div class="pagination pagination-compact nao-imprimir">
                {{ $posts->links('pagination::default') }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .pastoral-painel-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 18px;
        margin-top: 24px;
    }

    .pastoral-painel-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        box-shadow: 0 12px 28px rgba(17, 24, 39, 0.18);
    }

    .pastoral-painel-card header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .badge-tipo {
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        background: rgba(76, 106, 255, 0.18);
        text-transform: uppercase;
    }

    .badge-status {
        display: inline-flex;
        gap: 6px;
        align-items: center;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        text-transform: uppercase;
    }

    .badge-status.publicado {
        background: rgba(46, 204, 113, 0.18);
        color: #2ecc71;
    }

    .badge-status.rascunho {
        background: rgba(241, 196, 15, 0.18);
        color: #f1c40f;
    }

    .pastoral-painel-card-body h4 {
        margin-bottom: 8px;
    }

    .pastoral-painel-card-body p {
        margin: 0;
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.75);
    }

    .pastoral-painel-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 0.85rem;
        opacity: 0.8;
    }

    .pastoral-painel-acoes {
        margin-top: 12px;
    }

    .pastoral-painel-acoes .btn {
        min-width: auto;
    }
</style>
@endpush
