@extends('layouts.main')

@section('title', 'Futcristão | ' . $appName)

@section('content')
@php
    $isManager = $usuario && $usuario->hasAnyRole(['gestor', 'admin', 'kleros']);
@endphp
<div class="container">
    <header class="">
        <div>
            <p class="eyebrow">{{ $congregacao->nome_curto ?? $congregacao->identificacao }}</p>
            <h1>Futcristão</h1>
            <p class="subtitle">Coordene treinos, amistosos e a escala dos seus jogadores em um só lugar.</p>
        </div>
        <div class="hero-actions">
            @if($isManager)
                <button type="button" class="btn btn-primary" onclick="abrirJanelaModal('{{ route('futcristao.dias.create') }}')">
                    <i class="bi bi-calendar2-plus"></i> Novo dia
                </button>
                <button type="button" class="btn btn-light" onclick="abrirJanelaModal('{{ route('futcristao.grupos.create') }}')">
                    <i class="bi bi-people"></i> Novo grupo
                </button>
                <button type="button" class="btn btn-ghost" onclick="abrirJanelaModal('{{ route('futcristao.config.edit') }}')">
                    <i class="bi bi-sliders"></i> Configurações
                </button>
            @endif
        </div>
    </header>

    <section class="futebol-stats">
        <article class="stat-card">
            <span class="label">Grupos ativos</span>
            <strong>{{ $stats['grupos'] }}</strong>
            <small>Total cadastrado</small>
        </article>
        <article class="stat-card">
            <span class="label">Jogadores vinculados</span>
            <strong>{{ $stats['jogadores'] }}</strong>
            <small>Pivot de membros</small>
        </article>
        <article class="stat-card">
            <span class="label">Convidados</span>
            <strong>{{ $stats['convidados'] }}</strong>
            <small>Participantes externos</small>
        </article>
        <article class="stat-card highlight">
            <span class="label">Escala média</span>
            <strong>{{ number_format($stats['mediaTime'], 1, ',', '.') }}</strong>
            <small>Jogadores por time</small>
        </article>
        <article class="stat-card">
            <span class="label">Jogos nesta semana</span>
            <strong>{{ $stats['semana'] }}</strong>
            <small>{{ now()->startOfWeek()->format('d/m') }} — {{ now()->endOfWeek()->format('d/m') }}</small>
        </article>
        <article class="stat-card">
            <span class="label">Próximo encontro</span>
            @if($stats['proximoDia'])
                <strong>{{ optional($stats['proximoDia']->data_jogo)->translatedFormat('d M') }}</strong>
                <small>{{ $stats['proximoDia']->grupo->nome ?? 'Grupo não definido' }}</small>
            @else
                <strong>—</strong>
                <small>Sem datas futuras</small>
            @endif
        </article>
    </section>

    <div class="futebol-grid">
        <section class="card">
            <div class="section-header">
                <div>
                    <h2>Próximos dias</h2>
                    <p>Organize a próxima escala e confirme presença dos atletas.</p>
                </div>
                @if($isManager && count($grupos))
                    <button type="button" class="btn btn-small" onclick="abrirJanelaModal('{{ route('futcristao.dias.create') }}')">
                        <i class="bi bi-plus-circle"></i> Agendar
                    </button>
                @endif
            </div>
            <div class="day-grid">
                @forelse($proximosDias as $dia)
                    @php
                        $horaFormatada = $dia->hora_jogo ? substr($dia->hora_jogo, 0, 5) : null;
                    @endphp
                    <article class="day-card status-{{ $dia->status }}">
                        <header>
                            <div>
                                <span class="day-week">{{ optional($dia->data_jogo)->translatedFormat('l') }}</span>
                                <strong class="day-date">{{ optional($dia->data_jogo)->format('d/m') }}</strong>
                            </div>
                            <span class="status">{{ $dia->status_label }}</span>
                        </header>
                        <div class="day-body">
                            <h3>{{ $dia->grupo->nome ?? 'Grupo removido' }}</h3>
                            <p>
                                <i class="bi bi-geo-alt"></i> {{ $dia->local ?? 'Local a definir' }}<br>
                                <i class="bi bi-clock"></i> {{ $horaFormatada ?? 'Horário livre' }}
                            </p>
                            <div class="scoreboard">
                                <span>{{ $dia->placar_time_a }}</span>
                                <small>x</small>
                                <span>{{ $dia->placar_time_b }}</span>
                            </div>
                        </div>
                        @if($isManager)
                            <footer>
                                <button type="button" class="btn-link" onclick="abrirJanelaModal('{{ route('futcristao.dias.edit', $dia) }}')">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                                <form action="{{ route('futcristao.dias.destroy', $dia) }}" method="POST" onsubmit="return confirm('Deseja remover este dia?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-link danger"><i class="bi bi-trash"></i> Remover</button>
                                </form>
                            </footer>
                        @endif
                    </article>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-emoji-smile"></i>
                        <p>Nenhum dia agendado. Crie um novo para montar as equipes.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="card">
            <div class="section-header">
                <div>
                    <h2>Histórico recente</h2>
                    <p>Use os resultados para extrair estatísticas rápidas.</p>
                </div>
            </div>
            <ul class="history-list">
                @forelse($historicoDias as $dia)
                    @php
                        $horaHistorico = $dia->hora_jogo ? substr($dia->hora_jogo, 0, 5) : null;
                    @endphp
                    <li>
                        <div>
                            <h4>{{ $dia->grupo->nome ?? 'Grupo' }}</h4>
                            <small>{{ optional($dia->data_jogo)->translatedFormat('d \d\e F') }}{{ $horaHistorico ? ' • ' . $horaHistorico : '' }}</small>
                        </div>
                        <div class="history-score">
                            <span>{{ $dia->placar_time_a }}</span>
                            <small>x</small>
                            <span>{{ $dia->placar_time_b }}</span>
                        </div>
                    </li>
                @empty
                    <li class="empty-state">
                        <i class="bi bi-clipboard-data"></i> Nenhum registro concluído.
                    </li>
                @endforelse
            </ul>
        </section>
    </div>

    <section class="card regras">
        <div class="section-header">
            <div>
                <h2>Regras gerais</h2>
                <p>Definidas em configurações do módulo.</p>
            </div>
            @if($isManager)
                <button class="btn btn-small btn-ghost" onclick="abrirJanelaModal('{{ route('futcristao.config.edit') }}')">
                    <i class="bi bi-pencil"></i> Ajustar
                </button>
            @endif
        </div>
        <div class="regras-content">
            @if(filled($config->regras_gerais))
                {!! nl2br(e($config->regras_gerais)) !!}
            @else
                <p class="text-muted">Use as configurações para registrar regulamentos, escala de uniformes e combinados gerais.</p>
            @endif
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    :root {
        --futebol-surface: color-mix(in srgb, var(--background-color, #f8fafc) 80%, white);
        --futebol-border: color-mix(in srgb, var(--text-color, #0f172a) 15%, transparent);
        --futebol-muted: color-mix(in srgb, var(--text-color, #0f172a) 45%, white);
        --futebol-soft-muted: color-mix(in srgb, var(--text-color, #0f172a) 25%, white);
        --futebol-card-shadow: 0 20px 45px rgba(15, 23, 42, 0.12);
        --futebol-highlight-shadow: 0 30px 60px rgba(15, 23, 42, 0.35);
    }

    .futebol-dashboard {
        display: flex;
        flex-direction: column;
        gap: 1.75rem;
        padding-bottom: 2rem;
    }

    .futebol-dashboard .card {
        border-radius: 22px;
        padding: 1.75rem;
        background: var(--futebol-surface);
        border: 1px solid var(--futebol-border);
        box-shadow: var(--futebol-card-shadow);
        backdrop-filter: blur(12px);
    }

    .futebol-hero {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 1.25rem;
        color: var(--secondary-contrast);
        background-image: linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.35)), linear-gradient(135deg, var(--secondary-color), var(--terciary-color));
        border: none;
        box-shadow: var(--futebol-highlight-shadow);
    }

    .futebol-hero .eyebrow {
        text-transform: uppercase;
        letter-spacing: 0.12em;
        font-size: 0.75rem;
        opacity: 0.8;
        margin-bottom: 0.35rem;
    }

    .futebol-hero h1 {
        margin: 0;
        font-size: clamp(2rem, 4vw, 3rem);
    }

    .futebol-hero .subtitle {
        margin: 0.35rem 0 0;
        font-size: 1rem;
        opacity: 0.9;
    }

    .futebol-hero .hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .futebol-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.15rem;
    }

    .stat-card {
        border-radius: 18px;
        padding: 1.25rem;
        background: linear-gradient(135deg, var(--futebol-surface), color-mix(in srgb, var(--futebol-surface) 70%, white));
        border: 1px solid var(--futebol-border);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    }

    .stat-card .label {
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.08em;
        color: var(--futebol-muted);
        margin-bottom: 0.4rem;
        display: block;
    }

    .stat-card strong {
        display: block;
        font-size: 2.15rem;
        margin: 0;
        color: var(--text-color);
        line-height: 1.2;
    }

    .stat-card small {
        color: var(--futebol-muted);
        display: block;
        margin-top: 0.35rem;
    }

    .stat-card.highlight {
        background: linear-gradient(135deg, var(--primary-color), var(--terciary-color));
        color: var(--primary-contrast);
        border: none;
        box-shadow: 0 18px 35px rgba(15, 23, 42, 0.2);
    }

    .stat-card.highlight .label,
    .stat-card.highlight small {
        color: rgba(255, 255, 255, 0.85);
    }

    .futebol-grid {
        margin-top: 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .day-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.1rem;
    }

    .day-card {
        border-radius: 18px;
        padding: 1.15rem;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        border: 1px solid var(--futebol-border);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45);
    }

    .day-card header,
    .day-card footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.5rem;
    }

    .day-card .status {
        font-size: 0.8rem;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.08);
    }

    .day-card.status-confirmado .status {
        background: color-mix(in srgb, var(--secondary-color) 20%, white);
        color: color-mix(in srgb, var(--secondary-color) 60%, black);
    }

    .day-card.status-encerrado .status {
        background: color-mix(in srgb, var(--primary-color) 25%, white);
        color: color-mix(in srgb, var(--primary-color) 65%, black);
    }

    .day-card.status-cancelado .status {
        background: rgba(248, 113, 113, 0.18);
        color: #991b1b;
    }

    .day-card .day-week {
        text-transform: capitalize;
        font-size: 0.85rem;
        color: var(--futebol-soft-muted);
    }

    .day-card .day-date {
        font-size: 1.6rem;
        line-height: 1;
    }

    .day-body h3 {
        margin: 0 0 0.25rem;
        color: var(--text-color);
    }

    .day-body p {
        margin: 0;
        font-size: 0.92rem;
        color: var(--futebol-muted);
        line-height: 1.45;
    }

    .scoreboard,
    .history-score {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-color);
    }

    .history-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
    }

    .history-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.85rem 1.15rem;
        border-radius: 14px;
        border: 1px solid var(--futebol-border);
        background: color-mix(in srgb, var(--futebol-surface) 85%, white);
    }

    .history-list small {
        color: var(--futebol-muted);
    }

    .empty-state {
        border: 1px dashed var(--futebol-border);
        border-radius: 14px;
        padding: 1.5rem;
        text-align: center;
        color: var(--futebol-muted);
    }

    .btn-link {
        background: none;
        border: none;
        padding: 0;
        color: var(--primary-color);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .btn-link.danger {
        color: #dc2626;
    }

    .btn-small {
        padding: 0.45rem 1rem;
        border-radius: 999px;
        background: var(--secondary-color);
        color: var(--secondary-contrast);
        font-size: 0.9rem;
        border: none;
        box-shadow: 0 10px 18px rgba(15, 23, 42, 0.15);
    }

    .btn-ghost {
        background: rgba(255, 255, 255, 0.15);
        color: inherit;
        border: 1px solid rgba(255, 255, 255, 0.45);
    }

    .regras-content {
        white-space: pre-wrap;
        line-height: 1.6;
        color: var(--text-color);
    }

    @media (max-width: 1024px) {
        .futebol-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .futebol-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .day-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 520px) {
        .stat-card {
            padding: 1rem;
        }

        .futebol-hero .hero-actions {
            width: 100%;
        }

        .futebol-hero .hero-actions .btn {
            flex: 1;
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        @if(session('success'))
            flashMsg(@json(session('success')));
        @endif
        @if($errors->any())
            flashMsg(@json($errors->first()), 'error');
        @endif
    });
</script>
@endpush
