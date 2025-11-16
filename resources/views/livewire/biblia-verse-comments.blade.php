@pushOnce('styles:verse-comments')
    <style>
        .verse-comments-wrapper {
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
        }

        .verse-comments-header {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .verse-comments-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            color: #64748b;
        }

        .verse-comments-highlight {
            border: 2px solid rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.2);
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            display: flex;
            gap: 1.25rem;
            align-items: flex-start;
            box-shadow: 
                0 4px 12px rgba(0, 0, 0, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .verse-comments-highlight:hover {
            border-color: rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.35);
            box-shadow: 
                0 6px 20px rgba(0, 0, 0, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        .verse-comments-highlight.is-empty {
            border-style: dashed;
            border-color: #cbd5e1;
            color: #64748b;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .verse-comments-number {
            font-weight: 800;
            font-family: var(--text-font, inherit);
            color: #fff;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            font-size: 1.1rem;
            min-width: 2.75rem;
            height: 2.75rem;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                0 8px 16px -8px rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
            flex-shrink: 0;
        }

        .verse-comments-highlight p {
            margin: 0;
            color: #1e293b;
            line-height: 1.7;
            font-size: 1rem;
        }

        .btn-verse-comment {
            align-self: flex-start;
            border: none;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: #fff;
            padding: 0.65rem 1.25rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.3);
        }

        .btn-verse-comment:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.4);
        }

        .btn-verse-comment:active {
            transform: translateY(0);
        }

        .verse-comment-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 1.5rem;
            border-radius: 16px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 2px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .verse-comment-form label {
            font-weight: 700;
            font-size: 0.95rem;
            color: #1e293b;
        }

        .verse-comment-form textarea {
            width: 100%;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 1rem;
            font-size: 0.95rem;
            resize: vertical;
            background: #fff;
            color: #1e293b;
            transition: all 0.2s ease;
            font-family: inherit;
            line-height: 1.6;
        }

        .verse-comment-form textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.1);
        }

        .form-error {
            font-size: 0.85rem;
            color: #ef4444;
            font-weight: 500;
        }

        .verse-comment-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-save-comment {
            border: none;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: #fff;
            padding: 0.7rem 1.5rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.3);
        }

        .btn-save-comment:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.4);
        }

        .btn-save-comment:active {
            transform: translateY(0);
        }

        .btn-save-comment[disabled] {
            cursor: not-allowed;
            opacity: 0.6;
            transform: none;
        }

        .saving-indicator {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
        }

        .verse-comments {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .comment-item {
            padding: 1.25rem 1.5rem;
            border-radius: 16px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 2px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: all 0.2s ease;
        }

        .comment-item:hover {
            border-color: rgba(var(--kleros-primary-rgb, 16, 96, 165), 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateX(2px);
        }

        .comment-item header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .comment-item header strong {
            color: var(--primary-color);
            font-size: 0.95rem;
            font-weight: 700;
        }

        .comment-item header span {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
        }

        .comment-item p {
            margin: 0;
            color: #334155;
            white-space: pre-line;
            line-height: 1.7;
            font-size: 0.95rem;
        }

        .no-comments {
            text-align: center;
            padding: 3rem 1rem;
            color: #64748b;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .no-comments i {
            font-size: 3rem;
            color: #cbd5e1;
        }

        .no-comments p {
            margin: 0;
            font-size: 0.95rem;
        }

        @media (max-width: 640px) {
            .verse-modal__dialog {
                padding: 1.5rem;
            }

            .verse-comments-highlight {
                padding: 1rem;
                gap: 1rem;
            }

            .verse-comments-number {
                min-width: 2.25rem;
                height: 2.25rem;
                font-size: 1rem;
            }

            .comment-item {
                padding: 1rem;
            }
        }
    </style>
@endpushOnce

<div class="verse-comments-wrapper">
    <div class="verse-comments-header">
        <small class="verse-comments-label">Versículo selecionado</small>

        @if ($verseId)
            <div class="verse-comments-highlight">
                <span class="verse-comments-number">{{ $verseNumber }}</span>
                <p>{{ $verseText }}</p>
            </div>
        @else
            <div class="verse-comments-highlight is-empty">
                <span class="verse-comments-number">–</span>
                <p>Escolha um versículo para visualizar e compartilhar comentários.</p>
            </div>
        @endif

        @if ($canComment && $verseId)
            <button
                type="button"
                class="btn-verse-comment"
                wire:click="toggleForm"
            >
                <i class="bi bi-pencil-square"></i>
                {{ $showForm ? 'Cancelar' : 'Adicionar comentário' }}
            </button>
        @endif
    </div>

    @if ($showForm && $canComment && $verseId)
        <form class="verse-comment-form" wire:submit.prevent="saveComment">
            <label for="novo-comentario">Escreva sua reflexão</label>
            <textarea
                id="novo-comentario"
                rows="4"
                wire:model.defer="newComment"
                placeholder="Compartilhe como este versículo falou com você..."
            ></textarea>
            @error('newComment')
                <span class="form-error">{{ $message }}</span>
            @enderror
            <div class="verse-comment-actions">
                <button type="submit" class="btn-save-comment" wire:loading.attr="disabled">
                    <i class="bi bi-save"></i> Salvar comentário
                </button>
                <span class="saving-indicator" wire:loading>Enviando...</span>
            </div>
        </form>
    @endif

    @if (! $verseId)
        <div class="no-comments">
            <i class="bi bi-info-circle"></i> Selecione um versículo para consultar os comentários da liderança.
        </div>
    @elseif (empty($comments))
        <div class="no-comments">
            <i class="bi bi-chat-dots"></i> Ainda não há comentários para este versículo.
        </div>
    @else
        <div class="verse-comments">
            @foreach ($comments as $comment)
                <article class="comment-item" wire:key="comment-{{ $comment['id'] }}">
                    <header>
                        <strong>{{ $comment['autor'] ?? 'Líder' }}</strong>
                        @if (!empty($comment['criado_em']))
                            <span>{{ $comment['criado_em'] }}</span>
                        @endif
                    </header>
                    <p>{{ $comment['comentario'] }}</p>
                </article>
            @endforeach
        </div>
    @endif
</div>
