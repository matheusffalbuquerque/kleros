@php
    $tiposEscala = isset($tiposEscala) ? $tiposEscala : collect();
    $membros = isset($membros) ? $membros : collect();
    $cultosAgendados = isset($cultosAgendados) ? $cultosAgendados : collect();
    $cultoId = optional($culto ?? null)->id;
    $cultoSelecionado = old('culto_id', $cultoId);
    $cultoSelecionadoObj = $cultoSelecionado ? $cultosAgendados->firstWhere('id', $cultoSelecionado) : null;

    if (!$cultoSelecionadoObj && ($culto ?? null)) {
        $cultoSelecionadoObj = $culto;
    }

    $cultoDataFormatada = $cultoSelecionadoObj ? optional($cultoSelecionadoObj->data_culto)->format('d/m/Y H:i') : null;
    $cultoCategoriaNome = $cultoSelecionadoObj ? optional($cultoSelecionadoObj->categoria)->nome : null;
    $itens = old('itens', [
        ['funcao' => '', 'membro_id' => null, 'responsavel_externo' => ''],
    ]);
@endphp

<h1>Nova Escala</h1>
<form action="{{ route('escalas.store') }}" method="post" data-escala-form>
    @csrf
    <div class="tabs">
        <ul class="tab-menu">
            <li class="active" data-tab="escala-dados"><i class="bi bi-journal-text"></i> Dados gerais</li>
            <li data-tab="escala-itens"><i class="bi bi-people"></i> Itens</li>
        </ul>

        <div class="tab-content card">
            <div id="escala-dados" class="tab-pane form-control active">
                <div class="form-item">
                    <label for="tipo_escala_id">Tipo de escala:</label>
                    <select name="tipo_escala_id" id="tipo_escala_id" class="select2" data-placeholder="Selecione o tipo de escala" required>
                        <option value="">Selecione o tipo</option>
                        @foreach($tiposEscala as $tipo)
                            <option value="{{ $tipo->id }}" @selected(old('tipo_escala_id') == $tipo->id)>{{ $tipo->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-item">
                    <label for="culto_id">Vincular culto:</label>
                    @if($cultoSelecionadoObj)
                        <input type="hidden" name="culto_id" value="{{ $cultoSelecionadoObj->id }}">
                        <select id="culto_id" disabled>
                            <option value="{{ $cultoSelecionadoObj->id }}">
                                {{ $cultoDataFormatada ?? 'Data não informada' }}@if($cultoCategoriaNome) - {{ $cultoCategoriaNome }}@endif
                            </option>
                        </select>
                    @elseif($cultosAgendados->isEmpty())
                        <p class="hint">Nenhum culto agendado para vincular.</p>
                    @else
                        <select name="culto_id" id="culto_id">
                            <option value="">Selecione um culto agendado</option>
                            @foreach($cultosAgendados as $cultoOp)
                                @php
                                    $dataCulto = $cultoOp->data_culto ? \Illuminate\Support\Carbon::parse($cultoOp->data_culto)->format('d/m/Y H:i') : null;
                                    $categoriaNome = optional($cultoOp->categoria)->nome;
                                @endphp
                                <option value="{{ $cultoOp->id }}" @selected($cultoSelecionado == $cultoOp->id)>
                                    {{ $dataCulto ?? 'Data não informada' }}@if($categoriaNome) - {{ $categoriaNome }}@endif
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                @unless($cultoSelecionadoObj)
                    <div class="form-item" data-escala-datahora>
                        <label for="data_hora">Data e hora:</label>
                        <input type="datetime-local" name="data_hora" id="data_hora" value="{{ old('data_hora') }}">
                    </div>
                @endunless

                <div class="form-item">
                    <label for="local">Local:</label>
                    <input type="text" name="local" id="local" value="{{ old('local') }}" placeholder="Ex: Auditório principal">
                </div>

                <div class="form-item">
                    <label for="observacoes">Observações:</label>
                    <textarea name="observacoes" id="observacoes" rows="3" placeholder="Detalhes adicionais">{{ old('observacoes') }}</textarea>
                </div>
            </div>

            <div id="escala-itens" class="tab-pane form-control">
                <div class="form-item">
                    <label>Itens da escala:</label>
                    <div class="escala-items">
                        <button type="button" class="btn" id="btn-adicionar-item" data-escala-add><i class="bi bi-plus-circle"></i> Adicionar função</button>
                        <div class="escala-items-list" data-escala-items>
                            @foreach($itens as $index => $item)
                                @include('escalas.includes.partials.item', ['index' => $index, 'item' => $item, 'membros' => $membros])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-options center">
            <button class="btn" type="submit"><i class="bi bi-check-circle"></i> Salvar Escala</button>
            <button type="button" class="btn" onclick="fecharJanelaModal()"><i class="bi bi-x-circle"></i> Cancelar</button>
        </div>
    </div>

    <template id="escala-item-template">
        @include('escalas.includes.partials.item-template', ['membros' => $membros])
    </template>
</form>
