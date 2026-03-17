@extends('layouts.main')

@section('title', ($congregacao->nome_curto ?? 'Congregação') . ' | ' . $appName)

@section('content')
@php
    use Illuminate\Support\Str;
    $hasProgramacoes = $programacoes->count() > 0;
@endphp

<div class="container programacao-container">
    <div class="page-heading nao-imprimir">
        <h1>Programações da comunidade</h1>
        <p class="page-heading__subtitle">
            Fique por dentro dos próximos eventos e cultos da sua congregação.
        </p>
    </div>

    <div class="info">
        @if ($hasProgramacoes)
            <div class="programacao-table">
                @foreach ($programacoes as $item)
                    @php
                        /** @var \Illuminate\Support\Carbon $dataReferencia */
                        $dataReferencia = $item['inicio'];
                        $weekDay = Str::upper($dataReferencia->translatedFormat('D'));
                        $dayNumber = $dataReferencia->format('d');
                        $timeLabel = $dataReferencia->format('H:i') === '00:00'
                            ? 'Dia todo'
                            : str_replace(':', 'h', $dataReferencia->format('H:i'));
                        $descricao = Str::limit(strip_tags($item['descricao'] ?? ''), 140);
                    @endphp
                    <button type="button"
                        class="programacao-row"
                        onclick="abrirJanelaModal('{{ $item['modal_url'] }}')"
                        aria-label="Ver detalhes de {{ $item['titulo'] }}">
                        <div class="programacao-row__date">
                            <span class="programacao-row__weekday">{{ $weekDay }}</span>
                            <span class="programacao-row__day">{{ $dayNumber }}</span>
                            <span class="programacao-row__time">{{ $timeLabel }}</span>
                        </div>
                        <div class="programacao-row__content">
                            <div class="programacao-row__header">
                                <span class="programacao-row__type programacao-row__type--{{ $item['tipo'] }}">
                                    {{ $item['tipo'] === 'evento' ? 'Evento' : 'Culto' }}
                                </span>
                                <h3 class="programacao-row__title">{{ $item['titulo'] }}</h3>
                            </div>
                            @if (!empty($descricao))
                                <p class="programacao-row__description">{{ $descricao }}</p>
                            @endif
                            <ul class="programacao-row__meta">
                                @if ($item['tipo'] === 'evento')
                                    @if (!empty($item['local']))
                                        <li><i class="bi bi-geo-alt"></i> {{ $item['local'] }}</li>
                                    @endif
                                    @if ($item['fim'] && $item['fim']->greaterThan($item['inicio']))
                                        <li><i class="bi bi-calendar-range"></i> Até {{ $item['fim']->translatedFormat('d/m') }}</li>
                                    @endif
                                    <li><i class="bi bi-people"></i>
                                        {{ $item['requer_inscricao'] ? 'Necessita inscrição' : 'Aberto ao público' }}
                                    </li>
                                @else
                                    @if (!empty($item['preletor']))
                                        <li><i class="bi bi-mic"></i> {{ $item['preletor'] }}</li>
                                    @endif
                                    @if (!empty($item['texto_base']))
                                        <li><i class="bi bi-book"></i> {{ $item['texto_base'] }}</li>
                                    @endif
                                @endif
                            </ul>
                        </div>
                        <div class="programacao-row__action">
                            <span class="programacao-row__cta">
                                <i class="bi bi-eye-fill"></i>
                            </span>
                        </div>
                    </button>
                @endforeach
            </div>

            <div class="programacao-pagination">
                {{ $programacoes->links('vendor.pagination.simple-default') }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state__icon">
                    <i class="bi bi-calendar4-week"></i>
                </div>
                <h3 class="empty-state__title">Ainda não há programações previstas</h3>
                <p class="empty-state__subtitle">
                    Quando novos eventos ou cultos forem agendados, eles aparecerão aqui automaticamente.
                </p>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .programacao-container {
        width: 100%;
    }

    .page-heading {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 22px;
    }
    .page-heading__badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: linear-gradient(135deg, var(--terciary-color), var(--primary-color));
        color: var(--terciary-contrast);
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.12em;
    }
    .page-heading__title {
        font-size: clamp(1.8rem, 2vw + 1rem, 2.4rem);
        color: var(--text-color);
        font-weight: 600;
    }
    .page-heading__subtitle {
        color: var(--text-color);
        font-size: 0.95rem;
        max-width: 540px;
    }

    .programacao-table {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .programacao-row {
        display: grid;
        grid-template-columns: 110px minmax(0, 1fr) 56px;
        align-items: stretch;
        padding: 20px;
        background: var(--background-color);
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 18px;
        gap: 20px;
        text-align: left;
        color: inherit;
        cursor: pointer;
        transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
    }
    .programacao-row:hover {
        transform: translateY(-2px);
        border-color: rgba(255, 255, 255, 0.18);
        box-shadow: 0 12px 28px -12px rgba(0, 0, 0, 0.45);
    }
    .programacao-row:focus {
        outline: 2px solid var(--terciary-color);
        outline-offset: 3px;
    }

    .programacao-row__date {
        display: grid;
        grid-template-rows: auto 1fr auto;
        justify-items: center;
        gap: 4px;
        background: var(--primary-color);
        border-radius: 14px;
        padding: 12px 10px;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.08em;
    }
    .programacao-row__weekday {
        font-size: 0.75rem;
        color: var(--text-color);
    }
    .programacao-row__day {
        font-size: 1.9rem;
        color: var(--text-color);
        line-height: 1;
    }
    .programacao-row__time {
        font-size: 0.85rem;
        color: var(--text-color);
    }

    .programacao-row__content {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .programacao-row__header {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .programacao-row__type {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        padding: 6px 12px;
        border-radius: 999px;
        width: fit-content;
    }
    .programacao-row__type--evento {
        background: rgba(100, 73, 162, 0.25);
        color: var(--text-color);
    }
    .programacao-row__type--culto {
        background: rgba(203, 182, 255, 0.2);
        color: var(--text-color);
    }
    .programacao-row__title {
        margin: 0;
        font-size: 1.25rem;
        color: var(--text-color);
    }
    .programacao-row__description {
        margin: 0;
        font-size: 0.95rem;
        color: var(--text-color);
    }
    .programacao-row__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px 18px;
        padding: 0;
        margin: 0;
        list-style: none;
        font-size: 0.85rem;
        color: var(--text-color);
    }
    .programacao-row__meta i {
        margin-right: 6px;
        opacity: 0.75;
    }

    .programacao-row__action {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .programacao-row__cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        color: var(--text-color);
        transition: background 0.18s ease, color 0.18s ease;
    }
    .programacao-row:hover .programacao-row__cta {
        background: rgba(255, 255, 255, 0.46);
        color: var(--text-color);
    }

    .programacao-pagination {
        margin-top: 24px;
    }
    .programacao-pagination nav {
        display: flex;
        justify-content: center;
    }
    .programacao-pagination .pagination {
        display: flex;
        align-items: center;
        gap: 8px;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    /* Botões anterior/próximo */
    .programacao-pagination .pagination li a,
    .programacao-pagination .pagination li span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: var(--text-color);
        font-size: 0 !important;
        text-decoration: none;
        transition: background 0.18s ease, border-color 0.18s ease;
        cursor: pointer;
    }

    .programacao-pagination .pagination li a:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.25);
    }

    /* Ícone Anterior */
    .programacao-pagination .pagination li:first-child a::before,
    .programacao-pagination .pagination li:first-child span::before {
        content: "\F284";
        font-family: "bootstrap-icons";
        font-size: 1rem;
    }

    /* Ícone Próximo */
    .programacao-pagination .pagination li:last-child a::after,
    .programacao-pagination .pagination li:last-child span::after {
        content: "\F285";
        font-family: "bootstrap-icons";
        font-size: 1rem;
    }

    /* Desabilitado */
    .programacao-pagination .pagination li.disabled span {
        opacity: 0.35;
        cursor: not-allowed;
    }

    .empty-state {
        text-align: center;
        padding: 48px 32px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 18px;
        border: 1px dashed rgba(255, 255, 255, 0.1);
    }
    .empty-state__icon {
        font-size: 2.2rem;
        color: var(--terciary-color);
        margin-bottom: 12px;
    }
    .empty-state__title {
        margin: 0 0 8px;
        color: var(--text-color);
        font-size: 1.2rem;
    }
    .empty-state__subtitle {
        margin: 0;
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.95rem;
    }

    @media (max-width: 720px) {
        .programacao-row {
            grid-template-columns: 90px 1fr;
            grid-template-rows: auto auto;
        }
        .programacao-row__action {
            grid-column: span 2;
            justify-content: flex-end;
            padding-top: 8px;
        }
    }
</style>
@endpush
@endsection
