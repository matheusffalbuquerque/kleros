@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | Área Pastoral')

@section('content')
<div class="container area-pastoral-container">
    <h1>Área Pastoral</h1>
    <div class="info">
        <h3>Conteúdos recentes</h3>

        <section class="pastoral-galeria nao-imprimir" aria-label="Postagens pastorais">
            <div class="pastoral-cards-wrapper" id="pastoral-cards">
                @forelse ($posts as $post)
                    <article
                        class="pastoral-card"
                        data-post-id="{{ $post->id }}"
                        data-title="{{ $post->titulo }}"
                        data-tipo="{{ ucfirst($post->tipo_conteudo) }}"
                        data-data="{{ optional($post->publicado_em)->format('d/m/Y') ?? $post->created_at->format('d/m/Y') }}"
                        data-conteudo="{{ base64_encode($post->conteudo ?? '') }}"
                        data-resumo="{{ $post->resumo }}"
                        data-link="{{ $post->link_externo }}"
                        data-video="{{ $post->video_url }}"
                        data-arquivo="{{ $post->arquivo_principal }}"
                    >
                        <header>
                            <span class="pastoral-card-tipo">{{ ucfirst($post->tipo_conteudo) }}</span>
                            <time datetime="{{ optional($post->publicado_em)->toDateString() ?? $post->created_at->toDateString() }}">
                                {{ optional($post->publicado_em)->translatedFormat('d M Y') ?? $post->created_at->translatedFormat('d M Y') }}
                            </time>
                        </header>
                        <h4>{{ $post->titulo }}</h4>
                        <p>{{ $post->resumo ?? \Illuminate\Support\Str::limit(strip_tags($post->conteudo), 120) }}</p>
                        <footer>
                            <span class="pastoral-card-autor">
                                <i class="bi bi-person"></i> {{ optional($post->autor)->nome ?? 'Equipe Pastoral' }}
                            </span>
                        </footer>
                    </article>
                @empty
                    <div class="pastoral-card vazio">
                        <p><i class="bi bi-info-circle"></i> Nenhum conteúdo pastoral disponível ainda.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="pastoral-conteudo" id="pastoral-conteudo" aria-live="polite">
            @if ($postSelecionado)
                <article>
                    <header class="pastoral-conteudo-header">
                        <span class="pastoral-conteudo-tipo">{{ ucfirst($postSelecionado->tipo_conteudo) }}</span>
                        <h2 id="pastoral-conteudo-titulo">{{ $postSelecionado->titulo }}</h2>
                        <div class="pastoral-conteudo-meta">
                            <time datetime="{{ optional($postSelecionado->publicado_em)->toDateString() ?? $postSelecionado->created_at->toDateString() }}">
                                <i class="bi bi-calendar"></i>
                                {{ optional($postSelecionado->publicado_em)->translatedFormat('d \\d\\e F \\d\\e Y') ?? $postSelecionado->created_at->translatedFormat('d \\d\\e F \\d\\e Y') }}
                            </time>
                            <span>
                                <i class="bi bi-person"></i>
                                {{ optional($postSelecionado->autor)->nome ?? 'Equipe Pastoral' }}
                            </span>
                        </div>
                    </header>

                    <div class="pastoral-conteudo-body" id="pastoral-conteudo-body">
                        {!! $postSelecionado->conteudo !!}
                    </div>

                    <div class="pastoral-conteudo-links" id="pastoral-conteudo-links">
                        @if ($postSelecionado->link_externo)
                            <a href="{{ $postSelecionado->link_externo }}" target="_blank" rel="noopener" class="btn btn-outline">
                                <i class="bi bi-box-arrow-up-right"></i> Acessar link relacionado
                            </a>
                        @endif
                        @if ($postSelecionado->arquivo_principal)
                            <a href="{{ $postSelecionado->arquivo_principal }}" target="_blank" rel="noopener" class="btn">
                                <i class="bi bi-download"></i> Baixar recurso
                            </a>
                        @endif
                    </div>
                </article>
            @else
                <div class="card">
                    <p><i class="bi bi-journal-text"></i> Escolha um conteúdo para visualizar.</p>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection

@push('styles')
<style>
    .area-pastoral-container .pastoral-galeria {
        margin-bottom: 30px;
    }

    .pastoral-cards-wrapper {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 18px;
    }

    .pastoral-card {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 18px;
        padding: 18px 20px;
        box-shadow: 0 12px 28px rgba(17, 24, 39, 0.16);
        display: flex;
        flex-direction: column;
        gap: 12px;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .pastoral-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 34px rgba(17, 24, 39, 0.22);
        border-color: var(--secondary-color);
    }

    .pastoral-card header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        opacity: 0.8;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .pastoral-card h4 {
        margin: 0;
        font-size: 1.15rem;
    }

    .pastoral-card p {
        margin: 0;
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.78);
    }

    .pastoral-card footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        opacity: 0.85;
    }

    .pastoral-card-tipo {
        background: rgba(76, 106, 255, 0.15);
        border-radius: 999px;
        padding: 4px 9px;
        font-size: 0.75rem;
        letter-spacing: 1px;
    }

    .pastoral-card.vazio {
        cursor: default;
        text-align: center;
    }

    .pastoral-conteudo {
        border-radius: 22px;
        padding: 24px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 14px 32px rgba(17, 24, 39, 0.2);
        min-height: 260px;
    }

    .pastoral-conteudo-header {
        margin-bottom: 16px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .pastoral-conteudo-tipo {
        align-self: flex-start;
        background: rgba(76, 106, 255, 0.18);
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    .pastoral-conteudo-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 0.9rem;
        opacity: 0.8;
    }

    .pastoral-conteudo-body {
        line-height: 1.7;
        font-size: 1rem;
        color: rgba(255, 255, 255, 0.9);
    }

    .pastoral-conteudo-body p {
        margin-bottom: 1rem;
    }

    .pastoral-conteudo-links {
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    @media (max-width: 960px) {
        .pastoral-cards-wrapper {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }
    }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        const cards = document.querySelectorAll('.pastoral-card[data-post-id]');
        const contentTitle = document.getElementById('pastoral-conteudo-titulo');
        const contentBody = document.getElementById('pastoral-conteudo-body');
        const contentLinks = document.getElementById('pastoral-conteudo-links');
        const contentTypeBadge = document.querySelector('.pastoral-conteudo-tipo');

        if (!cards.length || !contentTitle || !contentBody || !contentLinks || !contentTypeBadge) {
            return;
        }

        const decodeHtml = (encoded) => {
            if (!encoded) {
                return '';
            }
            try {
                return atob(encoded);
            } catch (error) {
                return encoded;
            }
        };

        cards.forEach((card) => {
            card.addEventListener('click', () => {
                const titulo = card.dataset.title || 'Conteúdo pastoral';
                const tipo = card.dataset.tipo || '';
                const conteudo = decodeHtml(card.dataset.conteudo || '');
                const link = card.dataset.link;
                const arquivo = card.dataset.arquivo;

                contentTitle.textContent = titulo;
                contentTypeBadge.textContent = tipo;
                contentBody.innerHTML = conteudo || '<p>Este conteúdo ainda não possui detalhes adicionados.</p>';

                contentLinks.innerHTML = '';

                if (link) {
                    const linkEl = document.createElement('a');
                    linkEl.href = link;
                    linkEl.target = '_blank';
                    linkEl.rel = 'noopener';
                    linkEl.className = 'btn btn-outline';
                    linkEl.innerHTML = '<i class="bi bi-box-arrow-up-right"></i> Acessar link relacionado';
                    contentLinks.appendChild(linkEl);
                }

                if (arquivo) {
                    const downloadEl = document.createElement('a');
                    downloadEl.href = arquivo;
                    downloadEl.target = '_blank';
                    downloadEl.rel = 'noopener';
                    downloadEl.className = 'btn';
                    downloadEl.innerHTML = '<i class="bi bi-download"></i> Baixar recurso';
                    contentLinks.appendChild(downloadEl);
                }
            });
        });
    })();
</script>
@endpush
