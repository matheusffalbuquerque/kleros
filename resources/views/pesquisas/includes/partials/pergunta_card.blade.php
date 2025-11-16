@php
    $tipoLabels = [
        'texto' => 'Texto livre',
        'radio' => 'Escolha única',
        'checkbox' => 'Múltipla escolha',
    ];

    $tipoAnterior = $tipoAnterior ?? $pergunta->tipo;
    $textoAnterior = $textoAnterior ?? $pergunta->texto;
    $optionsAnterior = $optionsAnterior ?? $pergunta->opcoes->pluck('texto')->implode("\n");
    $tipoDisplay = $tipoLabels[$tipoAnterior] ?? ucfirst($tipoAnterior);
    $isCurrent = $isCurrent ?? false;
    $bodyId = 'pergunta-body-' . $pergunta->id;
    $showErrors = $showErrors ?? false;
@endphp

<div class="pergunta-card pergunta-accordion {{ $isCurrent ? 'open' : '' }}" data-accordion data-open="{{ $isCurrent ? 'true' : 'false' }}">
    <button type="button" class="pergunta-toggle" data-accordion-toggle aria-expanded="{{ $isCurrent ? 'true' : 'false' }}" aria-controls="{{ $bodyId }}">
        <div class="pergunta-header">
            <span class="pergunta-title">{{ $pergunta->texto }}</span>
            <small class="pergunta-meta">
                <i class="bi bi-chat-left-dots"></i> {{ $tipoDisplay }}
                @if(in_array($tipoAnterior, ['radio','checkbox']))
                    <span class="divider">•</span> {{ $pergunta->opcoes->count() }} Opções
                @endif
            </small>
        </div>
        <i class="bi bi-chevron-down"></i>
    </button>
    <div class="pergunta-body" id="{{ $bodyId }}" data-accordion-body>
        <form action="{{ route('pesquisas.perguntas.update', [$pesquisa->id, $pergunta->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="pergunta_id" value="{{ $pergunta->id }}">
            <div class="form-control">
                <div class="form-item">
                    <label for="texto-{{ $pergunta->id }}">Enunciado</label>
                    <textarea name="texto" id="texto-{{ $pergunta->id }}" rows="3" required>{{ $textoAnterior }}</textarea>
                    @if($showErrors)
                        @error('texto')
                            <small class="hint text-error">{{ $message }}</small>
                        @enderror
                    @endif
                </div>
                <div class="form-item">
                    <label for="tipo-{{ $pergunta->id }}">Tipo de resposta</label>
                    <select name="tipo" id="tipo-{{ $pergunta->id }}" data-toggle-options="#options-{{ $pergunta->id }}">
                        <option value="texto" @selected($tipoAnterior === 'texto')>Texto livre</option>
                        <option value="radio" @selected($tipoAnterior === 'radio')>Escolha única</option>
                        <option value="checkbox" @selected($tipoAnterior === 'checkbox')>Múltipla escolha</option>
                    </select>
                </div>
                <div class="form-item options-box" id="options-{{ $pergunta->id }}" style="display: {{ in_array($tipoAnterior, ['radio','checkbox']) ? 'block' : 'none' }};">
                    <label for="options">Opções (uma por linha) <br> <small class="">As respostas serão criadas conforme as opções listadas.</small></label>
                    <textarea name="options" id="options-{{ $pergunta->id }}-textarea">{{ $optionsAnterior }}</textarea>
                    @if($showErrors)
                        @error('options')
                            <small class="hint text-error">{{ $message }}</small>
                        @enderror
                    @endif
                </div>
                <div class="form-options">
                    <button type="submit" class="btn"><i class="bi bi-floppy"></i> Salvar pergunta</button>
                    <button type="button" class="btn danger" onclick="handleSubmit(event, document.getElementById('delete-pergunta-{{ $pergunta->id }}'), 'Deseja realmente excluir esta pergunta?')"><i class="bi bi-trash"></i> Excluir</button>
                </div>
            </div>
        </form>
        <form id="delete-pergunta-{{ $pergunta->id }}" action="{{ route('pesquisas.perguntas.destroy', [$pesquisa->id, $pergunta->id]) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
