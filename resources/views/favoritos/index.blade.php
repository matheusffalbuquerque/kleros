@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')
@php
    $itensFavoritos = [
        [
            'titulo' => 'Estudo bíblico de domingo',
            'descricao' => 'Resumo do encontro e materiais para compartilhar com o grupo.',
            'tipo' => 'Documento',
            'data' => '12/05',
            'icone' => 'bi-journal-text',
        ],
        [
            'titulo' => 'Devocional da semana',
            'descricao' => 'Playlist com mensagens curtas para enviar para amigos.',
            'tipo' => 'Áudio',
            'data' => '10/05',
            'icone' => 'bi-music-note-beamed',
        ],
        [
            'titulo' => 'Evento jovem - sábado',
            'descricao' => 'Detalhes do evento e convites rápidos para compartilhar.',
            'tipo' => 'Evento',
            'data' => '08/05',
            'icone' => 'bi-calendar-event',
        ],
    ];
@endphp

<div class="container favoritos-page">
    <header class="favoritos-hero">
        <div>
            <p class="section-kicker">Favoritos</p>
            <h1>Conteúdos salvos para acessar e compartilhar</h1>
            <p>Organize materiais, eventos e links importantes em um só lugar para revisitá-los rapidamente.</p>
        </div>
        <div class="favoritos-status">
            <span class="status-pill"><i class="bi bi-tools"></i> Em desenvolvimento</span>
            <p>Estamos finalizando esta área. Em breve você poderá abrir e compartilhar diretamente os itens favoritados.</p>
        </div>
    </header>

    <section class="favoritos-grid">
        @foreach ($itensFavoritos as $item)
            <article class="favorito-card">
                <div class="favorito-card__icon">
                    <i class="bi {{ $item['icone'] }}"></i>
                </div>
                <div class="favorito-card__body">
                    <div class="favorito-card__meta">
                        <span><i class="bi bi-link-45deg"></i> {{ $item['tipo'] }}</span>
                        <span><i class="bi bi-calendar-event"></i> {{ $item['data'] }}</span>
                    </div>
                    <h3>{{ $item['titulo'] }}</h3>
                    <p>{{ $item['descricao'] }}</p>
                    <div class="favorito-card__actions">
                        <button class="btn" disabled title="Disponível em breve">
                            <i class="bi bi-box-arrow-up-right"></i> Acessar
                        </button>
                        <button class="btn btn-primary" disabled title="Disponível em breve">
                            <i class="bi bi-share-fill"></i> Compartilhar
                        </button>
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    <div class="favoritos-placeholder">
        <i class="bi bi-stars"></i>
        <div>
            <h4>Seus favoritos chegarão aqui</h4>
            <p>Assim que liberarmos, você poderá adicionar itens e acessá-los em qualquer dispositivo.</p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .favoritos-page {
        display: grid;
        gap: 2rem;
        padding: 1rem 0 2rem;
    }
    .favoritos-hero {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1.5rem;
        background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
        color: var(--primary-contrast);
        border-radius: 1rem;
        padding: 1.75rem;
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
    }
    .favoritos-hero h1 {
        margin: 0 0 .5rem;
        font-size: clamp(1.6rem, 2vw + 1rem, 2.2rem);
    }
    .favoritos-hero p {
        margin: 0;
        color: var(--primary-contrast);
        opacity: .85;
    }
    .section-kicker {
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 700;
        font-size: .85rem;
        margin: 0 0 .35rem;
        color: var(--primary-contrast);
        opacity: .8;
    }
    .favoritos-status {
        max-width: 360px;
        background: rgba(0,0,0,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: .85rem;
        padding: 1rem 1.2rem;
        color: var(--primary-contrast);
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .4rem .75rem;
        border-radius: 999px;
        font-weight: 700;
        background: rgba(0,0,0,0.3);
    }
    .favoritos-status p {
        margin: .6rem 0 0;
        color: var(--primary-contrast);
        opacity: .8;
    }
    .favoritos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1rem;
    }
    .favorito-card {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 1rem;
        padding: 1rem;
        border-radius: 1rem;
        border: 1px solid rgba(255,255,255,0.08);
        background: rgba(255,255,255,0.02);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
    .favorito-card__icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        background: linear-gradient(135deg, var(--primary-color), var(--terciary-color));
        color: var(--primary-contrast);
        font-size: 1.4rem;
    }
    .favorito-card__body h3 {
        margin: .25rem 0;
        font-size: 1.1rem;
        color: var(--text-color);
    }
    .favorito-card__body p {
        margin: 0 0 .75rem;
        color: var(--text-color);
        opacity: .8;
    }
    .favorito-card__meta {
        display: flex;
        gap: .75rem;
        color: var(--text-color);
        opacity: .65;
        font-size: .9rem;
    }
    .favorito-card__actions {
        display: flex;
        gap: .5rem;
        flex-wrap: wrap;
    }
    .favorito-card .btn {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        border-radius: .6rem;
        padding: .6rem 1rem;
        border: 1px solid var(--primary-color);
        background: transparent;
        color: var(--text-color);
        cursor: not-allowed;
    }
    .favorito-card .btn.btn-primary {
        background: var(--primary-color);
        color: var(--primary-contrast);
    }
    .favoritos-placeholder {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 1rem;
        border: 1px dashed rgba(255,255,255,0.25);
        color: var(--text-color);
        opacity: .75;
        background: rgba(255,255,255,0.03);
    }
    .favoritos-placeholder i {
        font-size: 1.6rem;
        color: var(--terciary-color);
    }
    @media (max-width: 720px) {
        .favoritos-hero {
            padding: 1.25rem;
        }
    }
</style>
@endpush
