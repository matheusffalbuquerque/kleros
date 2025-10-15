@extends('layouts.main')

@section('title', ($projeto->nome ?? 'Projeto') . ' | ' . $appName)

@section('content')
<div class="container">
    <h1>{{ $projeto->nome }}</h1>
    <div class="info projeto-header">
        <h3>Detalhes do projeto</h3>
        <div class="projeto-meta">
            <p class="projeto-meta__badge" style="--badge-color: {{ $projeto->cor ?? '#1f2937' }}">{{ $projeto->cor ?? 'Sem cor definida' }}</p>
            <p class="projeto-meta__info">
                <i class="bi bi-people"></i>
                {{ $projeto->para_todos ? 'Visível a todos' : 'Somente equipe designada' }}
            </p>
        </div>

        @if(auth()->check() && auth()->user()->hasAnyRole(['gestor', 'admin', 'kleros']))
        <form action="{{ route('projetos.listas.store', $projeto) }}" method="post" class="projeto-lista-form">
            @csrf
            <label>
                <span>Adicionar lista</span>
                <input type="text" name="titulo" placeholder="Título da lista" required maxlength="255">
            </label>
            <button class="btn" onclick="window.history.back();"><i class="bi bi-arrow-left"></i> Voltar</button>
            <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Criar lista</button>
        </form>
        @endif
    </div>

    <div class="projeto-quadros">
        @forelse ($projeto->listas as $lista)
            <section class="projeto-lista">
                <header class="projeto-lista__header">
                    <h3>{{ $lista->titulo }}</h3>
                    <span class="projeto-lista__badge">{{ $lista->cards->count() }} cards</span>
                </header>

                <div class="projeto-cards">
                    @forelse ($lista->cards as $card)
                        <article class="projeto-card-item">
                            <button type="button" class="projeto-card-item__menu" title="Abrir detalhes" data-card="{{ $card->id }}">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <div class="projeto-card-item__content">
                                <h4>{{ $card->titulo }}</h4>
                                @if($card->descricao)
                                    <p>{{ \Illuminate\Support\Str::limit($card->descricao, 140) }}</p>
                                @endif
                                <div class="projeto-card-item__meta">
                                    @if($card->status)
                                        <span class="status">{{ $card->status->nome }}</span>
                                    @endif
                                    @if($card->data_entrega)
                                        <span class="deadline"><i class="bi bi-calendar-event"></i> {{ $card->data_entrega->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="projeto-cards__empty">Nenhum card nesta lista.</div>
                    @endforelse
                </div>

                @if(auth()->check() && auth()->user()->hasAnyRole(['gestor', 'admin', 'kleros']))
                    <div class="projeto-lista__footer">
                        <button type="button" class="btn btn-light btn-add-card" data-list="{{ $lista->id }}">
                            <i class="bi bi-plus-lg"></i> Adicionar card
                        </button>
                    </div>
                    <form action="{{ route('projetos.cards.store', [$projeto, $lista]) }}" method="post" class="projeto-card-form" id="card-form-{{ $lista->id }}">
                        @csrf
                        <textarea name="titulo" placeholder="Título do card" required></textarea>
                        <textarea name="descricao" placeholder="Descrição (opcional)"></textarea>
                        <div class="projeto-card-form__actions">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg"></i> Salvar</button>
                            <button type="button" class="btn btn-light btn-sm projeto-card-form__cancel" data-list="{{ $lista->id }}"><i class="bi bi-x-lg"></i> Cancelar</button>
                        </div>
                    </form>
                @endif
            </section>
        @empty
            <div class="projeto-empty">
                <p>Nenhuma lista cadastrada. Utilize o botão acima para criar a primeira lista.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('styles')
<style>
    .projeto-header {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .projeto-meta {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .projeto-meta__badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        background: var(--badge-color, #1f2937);
        color: #fff;
        font-size: 0.85rem;
        width: fit-content;
    }

    .projeto-meta__info {
        color: rgba(15, 23, 42, 0.65);
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .projeto-lista-form {
        display: flex;
        align-items: flex-end;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .projeto-lista-form label {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .projeto-lista-form input[type="text"] {
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        border: 1px solid rgba(15, 23, 42, 0.15);
        min-width: 220px;
    }

    .projeto-quadros {
        margin-top: 2rem;
        display: flex;
        gap: 1.2rem;
        overflow-x: auto;
        padding-bottom: 1rem;
    }

    .projeto-lista {
        background: #fff;
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: 0 12px 32px -26px rgba(15, 23, 42, 0.45);
        min-width: 260px;
        max-width: 320px;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .projeto-lista__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .projeto-lista__badge {
        font-size: 0.8rem;
        color: rgba(15, 23, 42, 0.6);
    }

    .projeto-cards {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    .projeto-card-item {
        position: relative;
        border-radius: 12px;
        border: 1px solid rgba(15, 23, 42, 0.1);
        padding: 1rem 1rem 1rem 1rem;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .projeto-card-item__content {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .projeto-card-item__menu {
        position: absolute;
        top: 0.45rem;
        right: 0.45rem;
        border: none;
        background: transparent;
        color: rgba(15, 23, 42, 0.45);
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 6px;
    }

    .projeto-card-item__menu:hover {
        background: rgba(15, 23, 42, 0.1);
        color: rgba(15, 23, 42, 0.7);
    }

    .projeto-card-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 26px -26px rgba(15, 23, 42, 0.55);
    }

    .projeto-card-item__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        font-size: 0.8rem;
        color: rgba(15, 23, 42, 0.65);
    }

    .projeto-card-item__meta .status {
        font-weight: 600;
    }

    .projeto-cards__empty,
    .projeto-empty {
        padding: 1rem;
        border-radius: 12px;
        border: 2px dashed rgba(15, 23, 42, 0.15);
        text-align: center;
        color: rgba(15, 23, 42, 0.6);
    }

    .projeto-lista__footer {
        margin-top: auto;
        display: flex;
        justify-content: center;
    }

    .btn-add-card {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.85rem;
        border-radius: 999px;
        padding: 0.45rem 1rem;
    }

    .projeto-card-form {
        display: none;
        margin-top: 0.75rem;
        padding: 0.75rem;
        border-radius: 12px;
        border: 1px solid rgba(15, 23, 42, 0.12);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
        flex-direction: column;
        gap: 0.6rem;
    }

    .projeto-card-form.is-visible {
        display: flex;
    }

    .projeto-card-form textarea {
        width: 100%;
        border: 1px solid rgba(15, 23, 42, 0.15);
        border-radius: 8px;
        padding: 0.5rem 0.65rem;
        font-size: 0.9rem;
        resize: vertical;
        min-height: 60px;
    }

    .projeto-card-form textarea:focus {
        outline: none;
        border-color: rgba(15, 23, 42, 0.35);
        box-shadow: 0 0 0 2px rgba(15, 23, 42, 0.08);
    }

    .projeto-card-form__actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-sm {
        font-size: 0.8rem;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
    }

    @media (max-width: 768px) {
        .projeto-lista-form {
            flex-direction: column;
            align-items: flex-start;
        }

        .projeto-lista {
            min-width: 220px;
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

        document.querySelectorAll('.btn-add-card').forEach(function (button) {
            button.addEventListener('click', function () {
                const listId = this.dataset.list;
                const form = document.getElementById(`card-form-${listId}`);
                if (!form) {
                    return;
                }
                form.classList.add('is-visible');
                const titleField = form.querySelector('textarea[name="titulo"]');
                if (titleField) {
                    titleField.focus();
                }
            });
        });

        document.querySelectorAll('.projeto-card-form__cancel').forEach(function (button) {
            button.addEventListener('click', function () {
                const listId = this.dataset.list;
                const form = document.getElementById(`card-form-${listId}`);
                if (!form) {
                    return;
                }
                form.classList.remove('is-visible');
                form.reset();
            });
        });

        document.querySelectorAll('.projeto-card-form textarea').forEach(function (textarea) {
            textarea.addEventListener('focus', function () {
                this.closest('.projeto-card-form').classList.add('is-editing');
            });
            textarea.addEventListener('blur', function () {
                const form = this.closest('.projeto-card-form');
                setTimeout(function () {
                    if (!form.contains(document.activeElement)) {
                        form.classList.remove('is-editing');
                    }
                }, 120);
            });
        });

        document.querySelectorAll('.projeto-card-item__menu').forEach(function (button) {
            button.addEventListener('click', function () {
                alert('Detalhamento do card em desenvolvimento.');
            });
        });
    });
</script>
@endpush
