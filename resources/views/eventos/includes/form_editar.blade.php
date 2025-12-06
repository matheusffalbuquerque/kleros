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
                        <label for="evento_recorrente">Natureza do evento: </label>
                        <div class="form-square">
                            <div>
                                <input type="radio" id="especifico" name="evento_recorrente" value="0" {{ !$evento->recorrente ? 'checked' : '' }}>
                                <label for="especifico">Específico (cadastro individual)</label>
                            </div>
                            <div>
                                <input type="radio" id="recorrente" name="evento_recorrente" value="1" {{ $evento->recorrente ? 'checked' : '' }}>
                                <label for="recorrente">Recorrente (cadastro único)</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-item">
                        <label for="data_inicio">Data de início: </label>
                        <input type="date" name="data_inicio" id="data_inicio" value="{{ \Carbon\Carbon::parse($evento->data_inicio)->format('Y-m-d') }}">
                    </div>
                    <div class="form-item">
                        <label for="data_encerramento">Data de encerramento: </label>
                        <input type="date" name="data_encerramento" id="data_encerramento" value="{{ \Carbon\Carbon::parse($evento->data_encerramento)->format('Y-m-d') }}">
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
                        $hasOcorrencias = $cronograma->isNotEmpty();
                    @endphp
                    <div class="card">
                        @if ($hasOcorrencias)
                            <p><i class="bi bi-info-circle"></i> Ajuste as ocorrências existentes. Inclusões e exclusões devem ser feitas no cadastro inicial.</p>
                        @else
                            <p><i class="bi bi-info-circle"></i> Não há ocorrências cadastradas. As datas serão sugeridas pelo intervalo de início e encerramento para você ajustar.</p>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="table cronograma-table">
                            <thead>
                                <tr>
                                    <th>Dia</th>
                                    <th>Horário de início (opcional)</th>
                                    <th>Descrição (opcional)</th>
                                    <th>Local (opcional)</th>
                                </tr>
                            </thead>
                            @if ($hasOcorrencias)
                                <tbody id="cronograma-body-editar">
                                    @foreach ($cronograma as $index => $oc)
                                        <tr>
                                            <td>
                                                <input type="hidden" name="ocorrencias[{{ $index }}][id]" value="{{ $oc->id }}">
                                                <input type="date" name="ocorrencias[{{ $index }}][data_ocorrencia]" value="{{ $oc->data_ocorrencia ? \Illuminate\Support\Carbon::parse($oc->data_ocorrencia)->format('Y-m-d') : '' }}">
                                            </td>
                                            <td><input type="time" name="ocorrencias[{{ $index }}][horario_inicio]" value="{{ $oc->horario_inicio }}"></td>
                                            <td><input type="text" name="ocorrencias[{{ $index }}][descricao]" value="{{ $oc->descricao }}" placeholder="Descrição (opcional)"></td>
                                            <td><input type="text" name="ocorrencias[{{ $index }}][local]" value="{{ $oc->local }}" placeholder="Local (opcional)"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            @else
                                <tbody
                                    id="cronograma-body-editar"
                                    data-cronograma-body
                                    data-cronograma-start="#data_inicio"
                                    data-cronograma-end="#data_encerramento"
                                    data-cronograma-prefix="ocorrencias">
                                </tbody>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <div class="form-options center">
                <button class="btn" type="submit"><i class="bi bi-arrow-clockwise"></i> Atualizar Evento</button>
                <button type="button" class="btn danger" onclick="handleSubmit(event, document.getElementById('delete-evento-{{ $evento->id }}'), 'Deseja realmente excluir este evento?')"><i class="bi bi-trash"></i> Excluir</button>
                <button type="button" class="btn" onclick="fecharJanelaModal()"><i class="bi bi-arrow-return-left"></i> Voltar</button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
    
<script>
    (function() {
        function toggleGeracaoCultos() {
            // Se "Recorrente" (valor 1) estiver selecionado
            if ($('input[name="evento_recorrente"]:checked').val() === '1') {
                $('.geracao_cultos').hide();
                // Força a seleção de "Manual" (valor 0) para geracao_cultos
                $('input[name="geracao_cultos"][value="0"]').prop('checked', true);
            } else {
                // Caso contrário, exibe a opção
                $('.geracao_cultos').show();
            }
        }

        $('input[name="evento_recorrente"]').on('change', toggleGeracaoCultos);
        toggleGeracaoCultos(); // Executa ao carregar para definir o estado inicial
    })();
</script>

@endpush
