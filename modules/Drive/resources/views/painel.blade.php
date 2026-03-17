@extends('layouts.main')

@section('title', 'Drive | ' . $appName)

@section('content')
<div class="container">
    <h1>{{$congregacao->nome_curto}} Drive</h1>
    <div class="info drive-wrapper">
        <h3>Arquivos e pastas</h3>
        <div class="drive-header">
            <p>Gerencie os arquivos e pastas da congregação, organizando em Documentos e Imagens.</p>
            
            <div class="info"></div>
            <div class="drive-header__actions">
                <form method="get" action="{{ route('drive.painel') }}" class="drive-view-toggle">
                    <input type="hidden" name="path" value="{{ $currentPath }}">
                    <button type="submit" name="view" value="grid" class="drive-view-toggle__btn {{ $viewMode === 'grid' ? 'is-active' : '' }}" title="Visualizar em ícones">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </button>
                    <button type="submit" name="view" value="list" class="drive-view-toggle__btn {{ $viewMode === 'list' ? 'is-active' : '' }}" title="Visualizar em lista">
                        <i class="bi bi-view-list"></i>
                    </button>
                </form>
            </div>
        </div>
        <nav class="drive-breadcrumbs">
            @foreach ($breadcrumbs as $index => $crumb)
                @if ($index + 1 < count($breadcrumbs))
                    <a href="{{ route('drive.painel', array_filter(['path' => $crumb['path'] ?: null, 'view' => $viewMode !== 'grid' ? $viewMode : null])) }}">{{ $crumb['label'] }}</a>
                    <span class="divider">/</span>
                @else
                    <span class="current">{{ $crumb['label'] }}</span>
                @endif
            @endforeach
        </nav>

        <div class="drive-toolbar">
            <form action="{{ route('drive.pastas.store') }}" method="post" class="drive-toolbar__form">
                @csrf
                <input type="hidden" name="current_path" value="{{ $currentPath }}">
                <input type="hidden" name="view" value="{{ $viewMode }}">
                <label for="folder_name" class="sr-only">Nome da pasta</label>
                <input type="text" name="folder_name" id="folder_name" maxlength="50" placeholder="Nova pasta" required>
                <button type="submit" class="btn btn-secondary"><i class="bi bi-folder-plus"></i> Criar pasta</button>
            </form>
            <form action="{{ route('drive.upload') }}" method="post" enctype="multipart/form-data" class="drive-toolbar__form drive-toolbar__form--upload">
                @csrf
                <input type="hidden" name="current_path" value="{{ $currentPath }}">
                <input type="hidden" name="view" value="{{ $viewMode }}">
                <label for="drive-file" class="btn btn-secondary"><i class="bi bi-upload"></i> Selecionar arquivo</label>
                <input type="file" name="file" id="drive-file" required>
                <button type="submit" class="btn btn-primary"><i class="bi bi-cloud-arrow-up"></i> Enviar</button>
                <span id="drive-selected-file" class="drive-toolbar__filename">Nenhum arquivo selecionado</span>
            </form>
        </div>

        <div class="drive-nav">
            @if ($parentPath)
                <a class="btn btn-light" href="{{ route('drive.painel', array_filter(['path' => $parentPath, 'view' => $viewMode !== 'grid' ? $viewMode : null])) }}"><i class="bi bi-arrow-90deg-up"></i> Voltar</a>
            @else
                <span class="drive-nav__root">Início</span>
            @endif
            <span class="drive-nav__location">{{ $currentPath !== '' ? $currentPath : 'Documentos e Imagens' }}</span>
        </div>

        <div class="drive-content drive-content--{{ $viewMode }}">
            @forelse ($items as $item)
                <div class="drive-item drive-item--{{ $item['type'] }}">
                    <div class="drive-item__main">
                        @if ($item['type'] === 'directory')
                            <a href="{{ route('drive.painel', array_filter(['path' => $item['relative_path'], 'view' => $viewMode !== 'grid' ? $viewMode : null])) }}" class="drive-item__link" title="Abrir {{ $item['name'] }}">
                                <i class="drive-item__icon {{ $item['icon'] }}"></i>
                                <span class="drive-item__name" title="{{ $item['name'] }}">{{ $item['display_name'] }}</span>
                            </a>
                            <span class="drive-item__meta">Pasta</span>
                       @else
                           <a href="{{ $item['url'] }}" target="_blank" rel="noopener" class="drive-item__link" title="Abrir {{ $item['name'] }}">
                               <i class="drive-item__icon {{ $item['icon'] }}"></i>
                               <span class="drive-item__name" title="{{ $item['name'] }}">{{ $item['display_name'] }}</span>
                           </a>
                            <span class="drive-item__meta">{{ strtoupper($item['extension'] ?? '') }} • {{ $item['size_label'] ?? '---' }} • {{ $item['updated_label'] ?? '---' }}</span>
                        @endif
                    </div>
                    <div class="drive-item__actions">
                        @if ($item['type'] === 'file')
                            <a href="{{ $item['url'] }}" target="_blank" rel="noopener" class="drive-item__action" title="Visualizar">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        @else
                            <a href="{{ route('drive.painel', array_filter(['path' => $item['relative_path'], 'view' => $viewMode !== 'grid' ? $viewMode : null])) }}" class="drive-item__action" title="Abrir">
                                <i class="bi bi-folder2-open"></i>
                            </a>
                        @endif
                        @if($item['type'] === 'file' || ($item['type'] === 'directory' && ($item['deletable'] ?? true)))
                            <form action="{{ route('drive.remover') }}" method="post" class="drive-item__delete" onsubmit="return handleSubmit(event, this, '{{ $item['type'] === 'directory' ? 'Deseja excluir a pasta e todos os arquivos contidos?' : 'Deseja excluir este arquivo?' }}');">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="target" value="{{ $item['delete_target'] ?? $item['relative_path'] }}">
                                <input type="hidden" name="type" value="{{ $item['type'] }}">
                                <input type="hidden" name="origin" value="{{ $item['origin'] ?? 'storage' }}">
                                @isset($item['arquivo_id'])
                                    <input type="hidden" name="arquivo_id" value="{{ $item['arquivo_id'] }}">
                                @endisset
                                <input type="hidden" name="current_path" value="{{ $currentPath }}">
                                <input type="hidden" name="view" value="{{ $viewMode }}">
                                <button type="submit" class="drive-item__action drive-item__action--danger" title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="drive-empty">
                    <i class="bi bi-archive"></i>
                    <h3>Nenhum item encontrado</h3>
                    <p>Crie pastas ou envie arquivos para começar a organizar o material da congregação.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@once
@push('styles')
<style>
    .drive-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        width: 100%;
        min-width: 0; /* evita empurrar o menu lateral em telas menores */
    }

    .drive-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1.5rem;
    }

    .drive-header h1 {
        margin: 0;
    }

    .drive-header__actions {
        display: flex;
        gap: 0.5rem;
    }

    .alert {
        padding: 0.75rem 1rem;
        border-radius: 10px;
        border: 1px solid transparent;
    }

    .alert-success {
        background: rgba(34, 197, 94, 0.12);
        border-color: rgba(34, 197, 94, 0.35);
        color: #166534;
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.12);
        border-color: rgba(239, 68, 68, 0.35);
        color: #7f1d1d;
    }

    .drive-view-toggle {
        display: inline-flex;
        gap: 0.5rem;
    }

    .drive-view-toggle__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 8px;
        border: 1px solid rgba(10, 25, 41, 0.15);
        background: rgba(255, 255, 255, 0.9);
        color: rgba(10, 25, 41, 0.85);
        transition: all 0.2s ease;
    }

    .drive-view-toggle__btn.is-active,
    .drive-view-toggle__btn:hover {
        background: var(--primary-color);
        color: var(--primary-contrast);
        border-color: transparent;
    }

    .drive-breadcrumbs {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.35rem;
        font-size: 0.95rem;
    }

    .drive-breadcrumbs a {
        color: var(--secondary-color);
        text-decoration: none;
    }

    .drive-breadcrumbs .current {
        font-weight: 600;
    }

    .drive-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
        min-width: 0;
    }

    .drive-toolbar__form {
        display: flex;
        gap: 0.65rem;
        align-items: center;
        flex-wrap: wrap;
        min-width: 0;
    }

    .drive-toolbar__filename {
        font-size: 0.9rem;
        color: var(--secondary-color);
        max-width: 20ch;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .drive-toolbar__form input[type="text"] {
        min-width: 12rem;
        min-height: 44px;
        padding: 10px 14px;
        border-radius: 14px;
        border: 1px solid rgba(24, 24, 24, 0.12);
        background: rgba(255, 255, 255, 0.08);
        color: var(--text-color);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .drive-toolbar__form input[type="text"]:focus {
        border-color: var(--secondary-color);
        background: rgba(255, 255, 255, 0.12);
        box-shadow: 0 0 0 3px rgba(100, 73, 162, 0.23);
        outline: none;
    }

    .drive-toolbar__form--upload input[type="file"] {
        display: none;
    }

    .drive-nav {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.95rem;
        color: var(--secondary-color);
        flex-wrap: wrap;
        min-width: 0;
    }

    .drive-content {
        display: grid;
        gap: 1rem;
        background: var(--background-color);
        box-shadow: 0 2px 8px rgba(10, 25, 41, 0.5);
        border-radius: 14px;
        padding: 1.5rem;
        width: 100%;
        min-width: 0;
        overflow-x: auto; /* evita empurrar o layout se houver itens largos */
    }

    .drive-content--grid {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    .drive-content--list {
        grid-template-columns: 1fr;
    }

    .drive-item {
        display: flex;
        flex-wrap: nowrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
        border-radius: 12px;
        background: var(--background-color);
        
        box-shadow: 0 0 0 rgba(10, 25, 41, 0.4);
        border: 1px solid rgba(173, 173, 173, 0.08);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .drive-item:hover {
        border-color: rgba(10, 25, 41, 0.08);
        box-shadow: 0 6px 24px -16px rgba(10, 25, 41, 0.4);
    }

    .drive-item__main {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        flex: 1;
        min-width: 0;
    }

    .drive-item__link {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        color: var(--text-color);
        text-decoration: none;
        font-weight: 600;
    }

    .drive-item__icon {
        font-size: 1.75rem;
        color: var(--secondary-color);
    }

    .drive-item__name {
        max-width: 18ch;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .drive-item__meta {
        font-size: 0.85rem;
        color: var(--secondary-color);
    }

    .drive-item__actions {
        display: inline-flex;
        flex-wrap: nowrap;
        gap: 0.4rem;
        row-gap: 0.35rem;
        justify-content: flex-end;
        max-width: none;
        white-space: nowrap;
        flex-shrink: 0;
        margin-left: auto;
    }

    .drive-item__action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 8px;
        border: 1px solid rgba(10, 25, 41, 0.14);
        background: var(--primary-color);
        color: var(--primary-contrast);
        transition: all 0.2s ease;
    }

    .drive-item__action:hover {
        background: var(--secondary-color);
        color: var(--secondary-contrast);
        border-color: transparent;
    }

    .drive-item__action--danger:hover {
        background: #ef4444;
        color: #fff;
    }

    .drive-empty {
        grid-column: 1 / -1;
        text-align: center;
        padding: 3rem 1rem;
        border: 2px dashed rgba(10, 25, 41, 0.15);
        border-radius: 16px;
        color: rgba(10, 25, 41, 0.6);
    }

    .drive-empty i {
        font-size: 2.5rem;
        display: block;
        margin-bottom: 1rem;
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        border: 0;
    }

    @media (max-width: 768px) {
        .drive-header {
            flex-direction: column;
        }

        .drive-toolbar {
            flex-direction: column;
            align-items: flex-start;
        }

        .drive-item__name {
            max-width: 14ch;
        }
    }
</style>
@endpush
@endonce

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('drive-file');
        const label = document.getElementById('drive-selected-file');

        if (input && label) {
            input.addEventListener('change', function () {
                const file = this.files && this.files.length ? this.files[0].name : 'Nenhum arquivo selecionado';
                label.textContent = file;
                label.setAttribute('title', file);
            });
        }

        @if (session('success'))
            flashMsg(@json(session('success')), 'success');
        @endif

        @if (session('error'))
            flashMsg(@json(session('error')), 'error');
        @endif
    });
</script>
@endpush
