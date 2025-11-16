@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')
<div class="container">
    <h1>Bíblia Sagrada - NVI</h1>
    <div class="biblia-container">
        
        
        <h2><span><i class="bi bi-book"></i> {{ $book->name }} - Capítulo {{ $chapter }}</span>
            <span class="chapter-navigation-top">
                @if($chapter > 1)
                    <a href="{{ route('biblia.verses', [$book->id, $chapter - 1]) }}" 
                    class="nav-chapter-btn" 
                    title="Capítulo anterior">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                @else
                    <span class="nav-chapter-btn disabled">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                @endif
                
                @if($chapter < $maxChapter)
                    <a href="{{ route('biblia.verses', [$book->id, $chapter + 1]) }}" 
                    class="nav-chapter-btn" 
                    title="Próximo capítulo">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @else
                    <span class="nav-chapter-btn disabled">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                @endif
            </span>
        </h2>

        <div class="verses-wrapper">
            @foreach ($versiculos as $v)
                <p>
                    <strong class="verse-number">{{ $v->verse }}</strong>
                    <span
                        class="each-verse"
                        data-verse-id="{{ $v->id }}"
                        data-verse-number="{{ $v->verse }}"
                    >{{ $v->text }}</span>
                </p>
            @endforeach
        </div>

        <!-- Botão flutuante para comentar seleção -->
        <button id="comment-selection-btn" class="copy-selection-btn">
            <i class="bi bi-chat-left-text"></i>
            <span>Comentar <span class="count">0</span> versículos</span>
        </button>

        <div id="verse-comments-modal" class="verse-modal" hidden>
            <div class="verse-modal__dialog" role="dialog" aria-modal="true">
                <button type="button" class="verse-modal__close" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="verse-modal__header">
                    <h3 id="verse-modal-title">Comentários</h3>
                </div>
                <div class="verse-modal__content">
                    @livewire('biblia-verse-comments')
                </div>
            </div>
        </div>

        <br>
        <a href="{{ route('biblia.chapters', $book->id) }}" class="btn-voltar">⬅ Voltar para capítulos</a>
    </div>
</div>
@endsection

@push('styles')
    <style>
        .biblia-container h2 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .chapter-navigation-top {
            display: flex;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        .nav-chapter-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: var(--text-color);
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .nav-chapter-btn:hover:not(.disabled) {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .nav-chapter-btn.disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        .verses-wrapper p {
            margin: 0 0 0.8rem;
            display: flex;
            gap: 0.6rem;
            align-items: flex-start;
        }

        .verse-number {
            font-family: var(--text-font, inherit);
            font-weight: 600;
            color: var(--secondary-color);
            min-width: 1.8rem;
            text-align: right;
            line-height: 1.6;
            font-size: 0.9rem;
        }

        .each-verse {
            display: inline-block;
            flex: 1;
            padding: 0.35rem 0.5rem;
            border-radius: var(--border-style, 8px);
            transition: all 0.2s ease;
            cursor: pointer;
            line-height: 1.6;
        }

        .each-verse:hover {
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.1);
        }

        .each-verse.is-selected {
            background: rgba(255, 255, 255, 0.12);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.2);
        }

        .each-verse.is-multi-selected {
            background: rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.2);
            box-shadow: inset 0 0 0 2px rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.4);
        }

        /* Botão de copiar seleção */
        .copy-selection-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 0.85rem 1.5rem;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            z-index: 1000;
            opacity: 0;
            transform: translateY(20px);
            pointer-events: none;
        }

        .copy-selection-btn.visible {
            opacity: 1;
            transform: translateY(0);
            pointer-events: all;
        }

        .copy-selection-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3);
        }

        .copy-selection-btn i {
            font-size: 1.1rem;
        }

        .copy-selection-btn .count {
            background: rgba(255, 255, 255, 0.25);
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 700;
        }

        /* Modal */
        .verse-modal {
            position: fixed;
            inset: 0;
            background: rgba(10, 25, 41, 0.6);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1.5rem;
            z-index: 1200;
        }

        .verse-modal[hidden] {
            display: none;
        }

        .verse-modal__dialog {
            position: relative;
            width: min(700px, 100%);
            max-height: 90vh;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.98));
            color: #1e293b;
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.25);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .verse-modal__header {
            padding: 2rem 2rem 1.5rem;
            border-bottom: 2px solid #e2e8f0;
            flex-shrink: 0;
        }

        .verse-modal__content {
            padding: 0 2rem 2rem;
            overflow-y: auto;
            flex: 1;
        }

        .verse-modal__content::-webkit-scrollbar {
            width: 10px;
        }

        .verse-modal__content::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .verse-modal__content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .verse-modal__content::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .verse-modal__close {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
            border: none;
            background: rgba(0, 0, 0, 0.08);
            font-size: 1.2rem;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .verse-modal__close:hover {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }

        #verse-modal-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        /* Estilos do componente Livewire */
        .verse-modal .verse-comments-wrapper {
            display: flex !important;
            flex-direction: column !important;
            gap: 1.75rem !important;
        }

        .verse-modal .verse-comments-label {
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.1em !important;
            font-weight: 700 !important;
            color: #64748b !important;
            margin-bottom: 0.5rem !important;
        }

        .verse-modal .verse-comments-highlight {
            background: #ffffff !important;
            border: 2px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 1.5rem !important;
            display: flex !important;
            gap: 1.25rem !important;
            align-items: flex-start !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06) !important;
            max-height: 300px !important;
            overflow-y: auto !important;
        }

        .verse-modal .verse-comments-highlight::-webkit-scrollbar {
            width: 8px !important;
        }

        .verse-modal .verse-comments-highlight::-webkit-scrollbar-track {
            background: #f1f5f9 !important;
            border-radius: 10px !important;
        }

        .verse-modal .verse-comments-highlight::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
            border-radius: 10px !important;
        }

        .verse-modal .verse-comments-highlight::-webkit-scrollbar-thumb:hover {
            background: #94a3b8 !important;
        }

        .verse-modal .verse-comments-highlight.is-empty {
            border-style: dashed !important;
            opacity: 0.7 !important;
        }

        .verse-modal .verse-comments-number {
            font-weight: 800 !important;
            color: #ffffff !important;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
            font-size: 1.1rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            border-radius: 10px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        .verse-modal .verse-comments-highlight p {
            color: #1e293b !important;
            line-height: 1.7 !important;
            font-size: 1rem !important;
            margin: 0 !important;
        }

        .verse-modal .btn-verse-comment {
            align-self: flex-start !important;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
            color: #ffffff !important;
            padding: 0.7rem 1.5rem !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            border: none !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }

        .verse-modal .btn-verse-comment:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2) !important;
        }

        .verse-modal .verse-comment-form {
            padding: 1.5rem !important;
            border-radius: 12px !important;
            background: #ffffff !important;
            border: 2px solid #e2e8f0 !important;
            gap: 1.25rem !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06) !important;
        }

        .verse-modal .verse-comment-form label {
            font-weight: 700 !important;
            color: #1e293b !important;
            font-size: 0.95rem !important;
            display: block !important;
            margin-bottom: 0.5rem !important;
        }

        .verse-modal .verse-comment-form textarea {
            width: 100% !important;
            border-radius: 8px !important;
            border: 2px solid #cbd5e1 !important;
            padding: 1rem !important;
            background: #f8fafc !important;
            color: #1e293b !important;
            font-size: 0.95rem !important;
            line-height: 1.6 !important;
            resize: vertical !important;
            min-height: 120px !important;
            font-family: inherit !important;
        }

        .verse-modal .verse-comment-form textarea:focus {
            border-color: var(--primary-color) !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
            outline: none !important;
        }

        .verse-modal .btn-save-comment {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
            color: #ffffff !important;
            padding: 0.7rem 1.5rem !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            border: none !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }

        .verse-modal .btn-save-comment:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2) !important;
        }

        .verse-modal .btn-save-comment[disabled] {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
            transform: none !important;
        }

        .verse-modal .comment-item {
            padding: 1.5rem !important;
            border-radius: 12px !important;
            background: #ffffff !important;
            border: 2px solid #e2e8f0 !important;
            transition: all 0.2s ease !important;
            margin-bottom: 1rem !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06) !important;
        }

        .verse-modal .comment-item:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        }

        .verse-modal .comment-item header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            gap: 1rem !important;
            margin-bottom: 0.85rem !important;
            padding-bottom: 0.85rem !important;
            border-bottom: 2px solid #f1f5f9 !important;
        }

        .verse-modal .comment-item header strong {
            color: var(--primary-color) !important;
            font-weight: 700 !important;
            font-size: 0.95rem !important;
        }

        .verse-modal .comment-item header span {
            color: #64748b !important;
            font-size: 0.85rem !important;
            font-weight: 500 !important;
        }

        .verse-modal .comment-item p {
            color: #334155 !important;
            line-height: 1.7 !important;
            font-size: 0.95rem !important;
            margin: 0 !important;
        }

        .verse-modal .no-comments {
            text-align: center !important;
            padding: 3rem 1rem !important;
            color: #64748b !important;
        }

        .verse-modal .no-comments i {
            font-size: 3rem !important;
            color: #cbd5e1 !important;
            margin-bottom: 1rem !important;
            display: block !important;
        }

        .verse-modal .form-error {
            color: #ef4444 !important;
            font-size: 0.85rem !important;
            font-weight: 500 !important;
        }

        .verse-modal .saving-indicator {
            color: var(--text-color) !important;
            opacity: 0.6 !important;
            font-size: 0.85rem !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('[Verses] DOM carregado');
            
            const modal = document.getElementById('verse-comments-modal');
            const commentBtn = document.getElementById('comment-selection-btn');
            const countSpan = commentBtn?.querySelector('.count');
            
            if (!modal) {
                console.error('[Verses] Modal não encontrado!');
                return;
            }

            const verseElements = document.querySelectorAll('.each-verse');
            console.log('[Verses] Versículos encontrados:', verseElements.length);

            if (!verseElements.length) {
                console.warn('[Verses] Nenhum versículo encontrado na página');
                return;
            }

            // Gerenciamento de seleção múltipla
            let selectedVerses = new Set();
            let lastSelectedIndex = -1;
            let currentSelected = null;

            const updateCommentButton = () => {
                if (selectedVerses.size > 0) {
                    commentBtn.classList.add('visible');
                    countSpan.textContent = selectedVerses.size;
                } else {
                    commentBtn.classList.remove('visible');
                }
            };

            const toggleVerseSelection = (element, index, isCtrl, isShift) => {
                if (isShift && lastSelectedIndex !== -1) {
                    // Seleção de intervalo
                    const start = Math.min(lastSelectedIndex, index);
                    const end = Math.max(lastSelectedIndex, index);
                    
                    for (let i = start; i <= end; i++) {
                        verseElements[i].classList.add('is-multi-selected');
                        selectedVerses.add(i);
                    }
                } else if (isCtrl) {
                    // Adiciona/remove da seleção
                    if (selectedVerses.has(index)) {
                        selectedVerses.delete(index);
                        element.classList.remove('is-multi-selected');
                    } else {
                        selectedVerses.add(index);
                        element.classList.add('is-multi-selected');
                    }
                    lastSelectedIndex = index;
                } else {
                    // Seleção única
                    verseElements.forEach(v => v.classList.remove('is-multi-selected'));
                    selectedVerses.clear();
                    selectedVerses.add(index);
                    element.classList.add('is-multi-selected');
                    lastSelectedIndex = index;
                }

                updateCommentButton();
            };

            const openModalForSelection = () => {
                if (selectedVerses.size === 0) return;

                const sortedIndexes = Array.from(selectedVerses).sort((a, b) => a - b);
                
                // Pega dados de todos os versículos selecionados
                const verses = sortedIndexes.map(index => {
                    const verse = verseElements[index];
                    return {
                        id: verse.dataset.verseId,
                        number: verse.dataset.verseNumber,
                        text: verse.textContent.trim()
                    };
                });

                // Marca visualmente
                verseElements.forEach(v => v.classList.remove('is-selected'));
                sortedIndexes.forEach(index => {
                    verseElements[index].classList.add('is-selected');
                });

                // Monta o texto concatenado
                const verseNumbers = verses.map(v => v.number).join(', ');
                const fullText = verses.map(v => `${v.number} ${v.text}`).join(' ');

                console.log('[Verses] Abrindo modal para versículos:', verseNumbers);

                // Abre modal
                modal.hidden = false;
                modal.setAttribute('data-open', 'true');

                // Envia para Livewire
                setTimeout(() => {
                    try {
                        const payload = {
                            verseId: parseInt(verses[0].id, 10), // Usa o ID do primeiro
                            verseNumber: verseNumbers,
                            verseText: fullText
                        };

                        const livewireComponent = document.querySelector('[wire\\:id]');
                        if (livewireComponent && window.Livewire) {
                            const wireId = livewireComponent.getAttribute('wire:id');
                            const component = window.Livewire.find(wireId);
                            if (component) {
                                component.call('loadVerse', payload);
                            }
                        }
                    } catch (error) {
                        console.error('[Verses] Erro ao comunicar com Livewire:', error);
                    }
                }, 100);
            };

            const closeModal = () => {
                console.log('[Verses] Fechando modal');
                modal.hidden = true;
                modal.removeAttribute('data-open');
                verseElements.forEach(v => v.classList.remove('is-selected'));
            };

            const openModalSingle = (element) => {
                const verseId = element.dataset.verseId;
                const verseNumber = element.dataset.verseNumber;
                const verseText = element.textContent.replace(/\s+/g, ' ').trim();

                console.log('[Verses] Abrindo modal para versículo:', verseNumber);

                // Remove seleção anterior
                verseElements.forEach(v => v.classList.remove('is-selected'));
                element.classList.add('is-selected');
                currentSelected = element;

                // Abre o modal
                modal.hidden = false;
                modal.setAttribute('data-open', 'true');

                // Envia para Livewire
                setTimeout(() => {
                    try {
                        const payload = {
                            verseId: parseInt(verseId, 10),
                            verseNumber: verseNumber,
                            verseText: verseText
                        };

                        const livewireComponent = document.querySelector('[wire\\:id]');
                        if (livewireComponent && window.Livewire) {
                            const wireId = livewireComponent.getAttribute('wire:id');
                            const component = window.Livewire.find(wireId);
                            if (component) {
                                component.call('loadVerse', payload);
                            }
                        }
                    } catch (error) {
                        console.error('[Verses] Erro ao comunicar com Livewire:', error);
                    }
                }, 100);
            };

            // Event listeners nos versículos
            verseElements.forEach((element, index) => {
                element.addEventListener('click', function(e) {
                    const isCtrl = e.ctrlKey || e.metaKey;
                    const isShift = e.shiftKey;

                    // Se Ctrl ou Shift está pressionado, é seleção múltipla
                    if (isCtrl || isShift) {
                        e.preventDefault();
                        toggleVerseSelection(element, index, isCtrl, isShift);
                    } else {
                        // Click simples - abre modal individual
                        e.preventDefault();
                        openModalSingle(element);
                    }
                });
            });

            // Botão de comentar seleção
            if (commentBtn) {
                commentBtn.addEventListener('click', openModalForSelection);
            }

            // Botão de fechar modal
            const closeBtn = modal.querySelector('.verse-modal__close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeModal();
                });
            }

            // Fechar ao clicar no backdrop
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            // Atalhos de teclado
            document.addEventListener('keydown', function(event) {
                // ESC - fecha modal ou limpa seleção
                if (event.key === 'Escape') {
                    if (modal.getAttribute('data-open') === 'true') {
                        closeModal();
                    } else if (selectedVerses.size > 0) {
                        verseElements.forEach(v => v.classList.remove('is-multi-selected'));
                        selectedVerses.clear();
                        updateCommentButton();
                    }
                }
                
                // Ctrl+A - seleciona todos os versículos
                if ((event.ctrlKey || event.metaKey) && event.key === 'a' && document.activeElement.tagName !== 'TEXTAREA') {
                    event.preventDefault();
                    verseElements.forEach((v, i) => {
                        v.classList.add('is-multi-selected');
                        selectedVerses.add(i);
                    });
                    lastSelectedIndex = verseElements.length - 1;
                    updateCommentButton();
                }
                
                // Enter - abre modal com seleção
                if (event.key === 'Enter' && selectedVerses.size > 0 && modal.getAttribute('data-open') !== 'true') {
                    event.preventDefault();
                    openModalForSelection();
                }
            });

            console.log('[Verses] Setup completo com seleção múltipla para comentários!');
        });
    </script>
@endpush
