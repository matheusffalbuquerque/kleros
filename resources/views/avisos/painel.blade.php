@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')

<div class="container avisos-container">
    <div class="avisos-header center nao-imprimir">
        <h1>Caixa de Mensagens</h1>
        <p>Acompanhe e gerencie as mensagens recebidas</p>
    </div>

    <div class="avisos-pane nao-imprimir">
        <aside class="avisos-list" id="avisoList">
            @if(auth()->check() && auth()->user()->hasAnyRole(['gestor', 'admin', 'kleros']))
                <div class="aviso-list-actions">
                    <button type="button" onclick="abrirJanelaModal('{{ route('avisos.form_criar') }}')">
                        <i class="bi bi-plus-circle"></i> Nova Mensagem
                    </button>
                    <button type="button" onclick="window.history.back()">
                        <i class="bi bi-arrow-return-left"></i> Voltar
                    </button>
                </div>
            @endif
            @forelse ($avisos as $item)
                @php
                    $enviadoPor = optional(optional($item->criador)->membro);
                    $ministerio = optional($enviadoPor->ministerio)->sigla;
                    $autor = trim(collect([
                        $ministerio,
                        $enviadoPor && $enviadoPor->nome ? primeiroEUltimoNome($enviadoPor->nome) : null,
                    ])->filter()->implode(' '));
                @endphp
                <button
                    id="aviso-{{ $item->id }}"
                    class="aviso-list-item {{ $item->is_lido ? 'is-lido' : 'is-nao-lido' }} {{ $loop->first ? 'is-ativo' : '' }}"
                    data-aviso-id="{{ $item->id }}"
                    data-aviso-url="{{ route('avisos.show', $item->id) }}"
                >
                    <div class="aviso-list-item__header">
                        <span class="aviso-list-item__title">{{ $item->titulo }}</span>
                        <time datetime="{{ optional($item->created_at)->toIso8601String() }}">{{ optional($item->created_at)->format('d/m H:i') }}</time>
                    </div>
                    <div class="aviso-list-item__meta">
                        <span>{{ $autor }}</span>
                        <span class="aviso-list-item__badge aviso-{{ $item->prioridade }}">{{ ucfirst($item->prioridade) }}</span>
                    </div>
                    <p class="aviso-list-item__preview">{{ \Illuminate\Support\Str::limit(strip_tags($item->mensagem), 120) }}</p>
                </button>
            @empty
                <div class="aviso-list-empty">
                    <i class="bi bi-inbox"></i>
                    <p>Nenhum aviso disponível no momento.</p>
                </div>
            @endforelse
        </aside>

        <section class="aviso-reader" id="avisoDetail">
            @include('avisos.includes.read', ['aviso' => null])
        </section>
    </div>

    @include('noticias.includes.destaques')
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const listContainer = document.getElementById('avisoList');
        const readerContent = document.getElementById('avisoReaderContent');

        if (!listContainer || !readerContent) {
            return;
        }

        const buttons = Array.from(listContainer.querySelectorAll('.aviso-list-item'));

        const getButtonFromHash = () => {
            const hash = window.location.hash ? decodeURIComponent(window.location.hash.substring(1)) : '';
            if (!hash) {
                return null;
            }
            return buttons.find(button => button.id === hash) || null;
        };

        const formatter = new Intl.DateTimeFormat('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        const escapeHtml = (value = '') => {
            const span = document.createElement('span');
            span.textContent = value ?? '';
            return span.innerHTML;
        };

        const capitalize = (value = '') => {
            if (!value) {
                return '';
            }
            return value.charAt(0).toUpperCase() + value.slice(1);
        };

        const renderMensagem = (texto = '') => {
            const escaped = escapeHtml(texto);
            return escaped.replace(/\n/g, '<br>');
        };

        const renderAviso = (data) => {
            if (!data) {
                readerContent.classList.add('is-empty');
                readerContent.innerHTML = `
                    <div class="aviso-reader__placeholder">
                        <i class="bi bi-envelope-open"></i>
                        <p>Selecione um aviso para visualizar o conteúdo.</p>
                    </div>
                `;
                return;
            }

            readerContent.classList.remove('is-empty');

            const createdAt = data.criado_em ? new Date(data.criado_em) : null;
            const datetimeIso = createdAt ? createdAt.toISOString() : '';
            const formattedDate = createdAt ? formatter.format(createdAt) : '';

            readerContent.innerHTML = `
                <header class="aviso-reader__header">
                    <div>
                        <h2>${escapeHtml(data.titulo)}</h2>
                        <div class="aviso-reader__meta">
                            <span>${escapeHtml(data.enviado_por || 'Equipe')}</span>
                            <span>&bull;</span>
                            <time datetime="${datetimeIso}">${formattedDate}</time>
                        </div>
                    </div>
                    <span class="aviso-pill aviso-${escapeHtml(data.prioridade)}">${capitalize(data.prioridade)}</span>
                </header>
                <article class="aviso-reader__body">${renderMensagem(data.mensagem)}</article>
            `;
        };

        const setActiveButton = (selected) => {
            buttons.forEach(button => button.classList.toggle('is-ativo', button === selected));
        };

        const loadAviso = async (button) => {
            const { avisoUrl } = button.dataset;
            if (!avisoUrl) {
                return;
            }

            setActiveButton(button);
            readerContent.innerHTML = `
                <div class="aviso-reader__placeholder">
                    <i class="bi bi-arrow-repeat"></i>
                    <p>Carregando aviso...</p>
                </div>
            `;
            readerContent.classList.add('is-empty');

            try {
                const response = await fetch(avisoUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Não foi possível carregar o aviso.');
                }

                const data = await response.json();
                renderAviso(data);
                button.classList.remove('is-nao-lido');
                button.classList.add('is-lido');
            } catch (error) {
                console.error(error);
                readerContent.innerHTML = `
                    <div class="aviso-reader__placeholder">
                        <i class="bi bi-exclamation-octagon"></i>
                        <p>${escapeHtml(error.message)}</p>
                    </div>
                `;
                readerContent.classList.add('is-empty');
            }
        };

        buttons.forEach(button => {
            button.addEventListener('click', () => loadAviso(button));
        });

        const initialButton = getButtonFromHash() || buttons[0];
        if (initialButton) {
            loadAviso(initialButton);
        } else {
            renderAviso(null);
        }

        window.addEventListener('hashchange', () => {
            const target = getButtonFromHash();
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                loadAviso(target);
            }
        });
    });
</script>
@endpush
