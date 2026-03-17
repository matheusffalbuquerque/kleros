<h1>Editar Evento</h1>
<div class="info">
    <form action="{{route('eventos.update', $evento->id)}}" method="post">
        @csrf
        @method('PUT')
        <div class="tabs">
            <ul class="tab-menu">
                <li class="active" data-tab="evento-descricao"><i class="bi bi-card-text"></i> Descrição</li>
                <li data-tab="evento-detalhes"><i class="bi bi-info-circle"></i> Detalhes</li>
                <li data-tab="evento-cronograma"><i class="bi bi-calendar-week"></i> Cronograma</li>
            </ul>

            <div class="tab-content card">
                <div id="evento-descricao" class="tab-pane form-control active">
                    <div class="form-item">
                        <label for="titulo">Título: </label>
                        <input type="text" name="titulo" id="titulo" placeholder="Título do evento" value="{{ $evento->titulo }}">
                    </div>
                    <div class="form-item">
                        <label for="grupo_id">Grupo responsável: </label>
                        <select name="grupo_id" id="grupo_id">
                            <option value="">Grupo responsável</option>
                            @foreach ($grupos as $item)
                            <option value="{{$item->id}}" {{ $evento->agrupamento_id == $item->id ? 'selected' : '' }}>{{$item->nome}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-item">
                        <label for="descricao">Descrição: </label>
                        <textarea name="descricao" placeholder="Descrição do evento">{{ $evento->descricao }}</textarea>
                    </div>
                </div>

                <div id="evento-detalhes" class="tab-pane form-control">
                    <div class="form-item">
                        <label for="requer_inscricao">Tipo de Acesso: </label>
                        <div class="form-square">
                            <div>
                                <input type="radio" id="publico" name="requer_inscricao" value="0" {{ !$evento->requer_inscricao ? 'checked' : '' }}>
                                <label for="publico">Público - Livre</label>
                            </div>
                            <div>
                                <input type="radio" id="privado" name="requer_inscricao" value="1" {{ $evento->requer_inscricao ? 'checked' : '' }}>
                                <label for="privado">Privado - Requer confirmação</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="evento-cronograma" class="tab-pane form-control">
                    @php
                        $cronograma = $evento->ocorrencias ?? collect();
                        $emptyOcorrencias = $cronograma->isEmpty();
                    @endphp
                    
                    <!--Pode ser necessário, caso seja um evento recorrente-->
                    @if ($emptyOcorrencias)
                        <div class="card">
                            <p><i class="bi bi-info-circle"></i> Não há ocorrências cadastradas.</p>
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table cronograma-table">
                            <thead>
                                <tr>
                                    <th>Dia</th>
                                    <th>Horário de início (opcional)</th>
                                    <th class="col-gerar-culto">Gerar culto</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            @if (!$emptyOcorrencias)
                                <!--Posteriormente pode ser introduzida uma descrição e local-->
                                <tbody id="cronograma-body-editar">
                                    @foreach ($cronograma as $index => $oc)
                                        <tr data-ocorrencia-row>
                                            <td data-label="Dia">
                                                <input type="hidden" name="ocorrencias[{{ $index }}][id]" value="{{ $oc->id }}">
                                                <input type="date" name="ocorrencias[{{ $index }}][data_ocorrencia]" value="{{ $oc->data_ocorrencia ? \Illuminate\Support\Carbon::parse($oc->data_ocorrencia)->format('Y-m-d') : '' }}" required>
                                            </td>
                                            <td data-label="Horário"><input type="time" name="ocorrencias[{{ $index }}][horario_inicio]" value="{{ $oc->horario_inicio }}"></td>
                                            <td data-label="Gerar culto" class="gerar-culto-cell">
                                                <label class="toggle-pill">
                                                    <input type="hidden" name="ocorrencias[{{ $index }}][gerar_culto]" value="0">
                                                    <input type="checkbox" name="ocorrencias[{{ $index }}][gerar_culto]" value="1" checked>
                                                    <span class="pill" aria-hidden="true"></span>
                                                    <span class="toggle-text" data-on="Sim" data-off="Não"></span>
                                                </label>
                                            </td>
                                            <td data-label="Ações" class="cronograma-acoes">
                                                <button type="button" class="btn-icon btn-add-ocorrencia" title="Duplicar ocorrência">
                                                    <i class="bi bi-plus-circle"></i>
                                                </button>
                                                <button type="button" class="btn-icon btn-remove-ocorrencia" title="Remover ocorrência">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            @else
                                <tbody id="cronograma-body-editar">
                                    <!-- Primeira ocorrência será adicionada automaticamente -->
                                </tbody>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <div class="form-options center">
                <button class="btn" type="submit"><i class="bi bi-arrow-clockwise"></i> Atualizar Evento</button>
                <button type="button" class="btn danger" onclick="handleSubmit(event, document.getElementById('delete-evento-{{ $evento->id }}'), 'Deseja realmente excluir este evento?')"><i class="bi bi-trash"></i> Excluir</button>
            </div>
        </div>
    </form>
    
    <!-- Formulário de exclusão -->
    <form id="delete-evento-{{ $evento->id }}" action="{{ route('eventos.destroy', $evento->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')
    
<script>
    (function() {
        function toggleGeracaoCultos() {
            const isRecorrente = $('input[name="evento_recorrente"]:checked').val() === '1';
            const $geracaoField = $('.geracao_cultos');
            const $gerarCells = $('.gerar-culto-cell');

            if ($geracaoField.length) {
                $geracaoField.toggle(!isRecorrente);
                if (isRecorrente) {
                    $('input[name="geracao_cultos"][value="0"]').prop('checked', true);
                }
            }

            if ($gerarCells.length) {
                $gerarCells.toggle(!isRecorrente);
                $gerarCells.find('input[type="checkbox"]').prop('checked', !isRecorrente);
            }
        }

        $('input[name="evento_recorrente"]').on('change', toggleGeracaoCultos);
        toggleGeracaoCultos(); // Executa ao carregar para definir o estado inicial
    })();
</script>

@endpush
