@php
    $dataCulto = optional($culto->data_culto)->format('Y-m-d');
    $horaCulto = optional($culto->data_culto)->format('H:i');
@endphp

<h1>Editar Culto</h1>
<div class="info">
    <form action="{{ route('cultos.update', $culto->id) }}" method="post">
        @csrf
        @method('PUT')

        <div class="tabs">
            <ul class="tab-menu">
                <li class="active" data-tab="culto-registro"><i class="bi bi-journal-text"></i> Registro</li>
                <li data-tab="culto-detalhes"><i class="bi bi-file-earmark-plus"></i> Detalhes</li>
                 <li data-tab="culto-escalas"><i class="bi bi-diagram-3"></i> Escalas</li>
            </ul>

            <div class="tab-content card">
                <div id="culto-registro" class="tab-pane form-control active">
                    <div class="form-item">
                        <label for="data_culto">Data do culto: </label>
                        <input type="date" name="data_culto" id="data_culto" value="{{ old('data_culto', $dataCulto) }}" required>
                    </div>
                    <div class="form-item">
                        <label for="horario_inicio">Horário de início: </label>
                        <input type="time" name="horario_inicio" id="horario_inicio" value="{{ old('horario_inicio', $horaCulto) }}">
                    </div>
                    <div class="form-item">
                        <label for="culto_categoria">Categoria: </label>
                        <select name="culto_categoria" id="culto_categoria">
                            <option value="">Regular</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->nome }}" @selected(old('culto_categoria', optional($culto->categoria)->nome) == $categoria->nome)>{{ $categoria->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-item">
                        <label for="evento_id">Evento: </label>
                        <select name="evento_id" id="evento_id">
                            <option value="">Selecione um evento cadastrado</option>
                            <option value="">Nenhum</option>
                            @if($eventos)
                                @foreach ($eventos as $item)
                                    <option value="{{ $item->id }}" @selected(old('evento_id', $culto->evento_id) == $item->id)>{{ $item->titulo }}</option>
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

                <div id="culto-detalhes" class="tab-pane form-control">
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
                                <option value="{{ $membro->id }}" @selected(old('preletor_id', $culto->preletor_id) == $membro->id)>
                                    {{ $membro->nome }}@if($ministerioNome) <small style="color:#666;"> ({{ $ministerioNome }})</small>@endif
                                </option>
                            @endforeach
                        </select>
                        <input type="text" name="preletor_externo" id="preletor_externo" value="{{ old('preletor_externo', $culto->preletor_externo) }}" placeholder="Nome do preletor externo" data-preletor-external-input style="display: none;" disabled>
                    </div>
                    <div class="form-item">
                        <label for="tema_sermao">Tema do sermão</label>
                        <input type="text" placeholder="Tema central do sermão" name="tema_sermao" id="tema_sermao" value="{{ old('tema_sermao', $culto->tema_sermao) }}">
                    </div>
                    <div class="form-item">
                        <label for="texto_base">Texto-base</label>
                        <input type="text" placeholder="Texto-base do sermão" name="texto_base" id="texto_base" value="{{ old('texto_base', $culto->texto_base) }}">
                    </div>
                    <div class="form-item">
                        <label for="quantidade_pessoas">Quantidade de pessoas</label>
                        <div class="form-square">
                            <div>
                                <label for="quantidade_adultos">Adultos</label>
                                <input type="number" min="0" placeholder="0" name="quantidade_adultos" id="quantidade_adultos" value="{{ old('quantidade_adultos', $culto->quant_adultos) }}">
                            </div>
                            <div>
                                <label for="quantidade_criancas">Crianças</label>
                                <input type="number" min="0" placeholder="0" name="quantidade_criancas" id="quantidade_criancas" value="{{ old('quantidade_criancas', $culto->quant_criancas) }}">
                            </div>
                            <div>
                                <label for="quantidade_visitantes">Visitantes</label>
                                <input type="number" min="0" placeholder="0" name="quantidade_visitantes" id="quantidade_visitantes" value="{{ old('quantidade_visitantes', $culto->quant_visitantes) }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-item">
                        <label for="observacoes">Observações</label>
                        <textarea name="observacoes" id="observacoes" cols="30" rows="3" placeholder="Observações gerais sobre o culto">{{ old('observacoes', $culto->observacoes) }}</textarea>
                    </div>
                </div>

                <div id="culto-escalas" class="tab-pane form-control">
                    <div class="form-item">
                        <button type="button" class="btn" onclick="abrirJanelaModal('{{ route('escalas.form_criar', ['culto' => $culto->id]) }}')"><i class="bi bi-plus-circle"></i> Nova escala</button>
                    </div>

                    <div data-escalas-lista data-culto-id="{{ $culto->id }}">
                        @if($culto->escalas->isEmpty())
                            <div class="card">
                                <p><i class="bi bi-info-circle"></i> Nenhuma escala cadastrada para este culto.</p>
                            </div>
                        @else
                            <div class="list">
                                <div class="list-title">
                                    <div class="item-1"><b>Tipo</b></div>
                                    <div class="item-2"><b>Detalhes</b></div>
                                    <div class="item-1"><b>Ações</b></div>
                                </div>
                                @foreach($culto->escalas as $escala)
                                    <div class="list-item">
                                        <div class="item item-1">
                                            <p>{{ optional($escala->tipo)->nome ?? 'Sem tipo' }}</p>
                                            <small class="hint">
                                                @if($escala->data_hora)
                                                    {{ $escala->data_hora->format('d/m/Y H:i') }}
                                                @else
                                                    Data não definida
                                                @endif
                                                @if($escala->local)
                                                    • {{ $escala->local }}
                                                @endif
                                            </small>
                                        </div>
                                        <div class="item item-2">
                                            @if($escala->observacoes)
                                                <p>{{ $escala->observacoes }}</p>
                                            @endif
                                            <ul class="hint">
                                                @foreach($escala->itens as $item)
                                                    <li>
                                                        <strong>{{ $item->funcao }}</strong>
                                                        @if($item->membro)
                                                            — {{ $item->membro->nome }}
                                                        @elseif($item->responsavel_externo)
                                                            — {{ $item->responsavel_externo }}
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="item item-1">
                                            <div class="form-options column">
                                                <button type="button" class="btn" onclick="abrirJanelaModal('{{ route('escalas.form_editar', $escala->id) }}')"><i class="bi bi-pencil-square"></i> Editar</button>
                                                <form action="{{ route('escalas.destroy', $escala->id) }}" method="post" onsubmit="return handleSubmit(event, this, 'Deseja excluir esta escala?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn danger"><i class="bi bi-trash"></i> Excluir</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-options center">
                <button class="btn" type="submit"><i class="bi bi-arrow-clockwise"></i> Atualizar Culto</button>
                <button type="button" class="btn danger" onclick="handleSubmit(event, document.getElementById('delete-culto-{{ $culto->id }}'), 'Deseja realmente excluir este culto?')"><i class="bi bi-trash"></i> Excluir</button>
                <button type="button" class="btn" onclick="fecharJanelaModal()"><i class="bi bi-arrow-return-left"></i> Voltar</button>
            </div>
        </div>
    </form>
</div>


<form id="delete-culto-{{ $culto->id }}" action="{{ route('cultos.destroy', $culto->id) }}" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
(function() {
    'use strict';
    
    console.log('Script de atualização de eventos carregado no formulário de edição de cultos');
    
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
        console.log('Evento eventoCreated recebido:', e.detail);
        
        const { eventoId, eventoTitulo } = e.detail;
        
        if (!eventoId || !eventoTitulo) {
            console.warn('Evento criado mas sem dados completos:', e.detail);
            return;
        }
        
        console.log('Processando evento criado:', eventoId, eventoTitulo);
        
        // Aguarda um pouco para garantir que voltou ao modal anterior
        setTimeout(() => {
            adicionarEventoAoSelect(eventoId, eventoTitulo);
        }, 300);
    });

    // Escuta criação de escalas vindas de modais aninhados
    const escalasListaContainer = document.querySelector('[data-escalas-lista]');
})();
</script>
