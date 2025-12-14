<h1>Agendar Culto</h1>
<div class="info">
    <form action="{{ route('cultos.store') }}" method="post">
        @csrf

        <div class="tabs">
            <ul class="tab-menu">
                <li class="active" data-tab="culto-registro"><i class="bi bi-journal-text"></i> Registro</li>
                <li data-tab="culto-escalas"><i class="bi bi-diagram-3"></i> Escalas</li>
            </ul>

            <div class="tab-content card">
                <div id="culto-registro" class="tab-pane form-control active">
                    <div class="form-item">
                        <label for="data_culto">Data do culto: </label>
                        <input type="date" name="data_culto" id="data_culto" value="{{ old('data_culto') }}" required>
                    </div>

                    <div class="form-item">
                        <label for="culto_categoria">Categoria: </label>
                        <select name="culto_categoria" id="culto_categoria">
                            <option value="">Regular</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->nome }}" @selected(old('culto_categoria') == $categoria->nome)>{{ $categoria->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-item" data-preletor-container>
                        <label for="preletor_id">
                            Preletor:
                            <button type="button" class="btn-small" data-preletor-toggle>Inserir externo</button>
                        </label>
                        <select name="preletor_id" id="preletor_id" class="select2" data-placeholder="Selecione um preletor" data-preletor-select>
                            <option value="">Selecione um preletor</option>
                            @foreach($membros as $membro)
                                @php
                                    $ministerioNome = optional($membro->ministerio)->nome;
                                @endphp
                                <option value="{{ $membro->id }}" @selected(old('preletor_id') == $membro->id)>
                                    {{ $membro->nome }}@if($ministerioNome) <small style="color:#666;"> ({{ $ministerioNome }})</small>@endif
                                </option>
                            @endforeach
                        </select>
                        <input type="text" name="preletor_externo" id="preletor_externo" value="{{ old('preletor_externo') }}" placeholder="Nome do preletor externo" data-preletor-external-input style="display: none;" disabled>
                    </div>

                    <div class="form-item">
                        <label for="evento_id">Evento: </label>
                        <select name="evento_id" id="evento_id">
                            <option value="">Selecione um evento cadastrado</option>
                            <option value="">Nenhum</option>
                            @if($eventos)
                                @foreach ($eventos as $item)
                                    <option value="{{ $item->id }}" @selected(old('evento_id') == $item->id)>{{ $item->titulo }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="form-item">
                        <div class="card">
                            <p>Não encontrou o evento? <a onclick="abrirJanelaModal('{{ route('eventos.form_criar') }}')" class="link-standard">Cadastrar aqui</a></p>
                        </div>
                    </div>
                </div>

                <div id="culto-escalas" class="tab-pane form-control">
                    <div class="card">
                        <p><i class="bi bi-info-circle"></i> Salve o culto para adicionar escalas específicas.</p>
                    </div>
                </div>
            </div>

            <div class="form-options center">
                <button class="btn" type="submit"><i class="bi bi-plus-circle"></i> Registrar Culto</button>
                <button type="button" class="btn" onclick="window.history.back()"><i class="bi bi-arrow-return-left"></i> Voltar</button>
            </div>
        </div>
    </form>
</div>

<script>
(function() {
    'use strict';
    
    console.log('Script de atualização de eventos carregado no formulário de criação de cultos');
    
    // Função para adicionar evento ao select
    function adicionarEventoAoSelect(eventoId, eventoTitulo) {
        const selectEvento = document.getElementById('evento_id');
        
        if (!selectEvento) {
            console.error('Select de eventos não encontrado!');
            return false;
        }
        
        console.log('Select de eventos encontrado, adicionando opção...', eventoId, eventoTitulo);
        
        // Verifica se a opção já existe
        const optionExists = Array.from(selectEvento.options).some(opt => opt.value == eventoId);
        
        if (!optionExists) {
            // Cria nova opção
            const newOption = new Option(eventoTitulo, eventoId, true, true);
            selectEvento.add(newOption);
            
            // Feedback visual
            selectEvento.style.backgroundColor = 'color-mix(in srgb, var(--success-color, #22c55e) 20%, transparent)';
            setTimeout(() => {
                selectEvento.style.backgroundColor = '';
            }, 1500);
            
            console.log('✅ Evento adicionado ao select:', eventoTitulo);
            return true;
        } else {
            // Se já existe, apenas seleciona
            selectEvento.value = eventoId;
            console.log('ℹ️ Evento já existe no select, apenas selecionado');
            return true;
        }
    }
    
    // Verifica se há um evento recente criado ao carregar a página
    console.log('Verificando window.eventoRecenteCriado:', window.eventoRecenteCriado);
    if (window.eventoRecenteCriado) {
        console.log('Encontrado evento recente criado, adicionando ao select...');
        setTimeout(() => {
            adicionarEventoAoSelect(
                window.eventoRecenteCriado.id, 
                window.eventoRecenteCriado.titulo
            );
            // Limpa após usar
            window.eventoRecenteCriado = null;
        }, 200);
    }
    
    // Escuta quando o modal é restaurado
    window.addEventListener('modalRestaurado', function(e) {
        console.log('Modal restaurado! Verificando evento recente...', window.eventoRecenteCriado);
        if (window.eventoRecenteCriado) {
            console.log('Encontrado evento após restauração:', window.eventoRecenteCriado);
            adicionarEventoAoSelect(
                window.eventoRecenteCriado.id, 
                window.eventoRecenteCriado.titulo
            );
            // Limpa após usar
            window.eventoRecenteCriado = null;
        }
    });
    
    // Escuta o evento de criação de evento
window.addEventListener('eventoCreated', function(e) {
        console.log('Evento eventoCreated recebido no form criar cultos:', e.detail);
        
        const { eventoId, eventoTitulo } = e.detail;
        
        if (!eventoId || !eventoTitulo) {
            console.warn('Dados incompletos:', e.detail);
            return;
        }
        
        console.log('Processando evento criado:', eventoId, eventoTitulo);
        
        setTimeout(() => {
            adicionarEventoAoSelect(eventoId, eventoTitulo);
        }, 300);
    });
})();

</script>
