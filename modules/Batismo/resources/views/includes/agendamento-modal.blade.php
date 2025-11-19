@php
    $selectedMembros = collect($selectedMembros ?? [])
        ->map(fn ($value) => (int) $value)
        ->filter()
        ->values()
        ->all();
    $dataBatismoValue = $dataBatismo ?: now()->format('Y-m-d');
@endphp

<h1><i class="bi bi-calendar-plus"></i> Agendar Batismo</h1>
<div class="info">
    <form method="POST" action="{{ route('batismo.agendar') }}">
        @csrf
        <input type="hidden" name="status" value="{{ $statusFilter }}">
        <input type="hidden" name="_source" value="batismo_agendar">

        <div class="form-control">
            <div class="form-item">
                <label for="membro_id">Membros:</label>
                <select class="select2" name="membros[]" id="membro_id" multiple data-placeholder="Selecione os membros" data-search-placeholder="Pesquise por membros" required>
                    @foreach ($membrosSemBatismo as $membro)
                        <option value="{{ $membro->id }}" @selected(in_array($membro->id, $selectedMembros, true))>{{ $membro->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-item">
                <label for="data_batismo">Data do batismo:</label>
                <input type="date" name="data_batismo" id="data_batismo" value="{{ $dataBatismoValue }}" required>
            </div>

            <div class="form-options center">
                <button class="btn" type="submit"><i class="bi bi-check-lg"></i> Salvar agendamento</button>
                <button type="button" class="btn btn-light" onclick="fecharJanelaModal()"><i class="bi bi-x-circle"></i> Cancelar</button>
            </div>
        </div>
    </form>
</div>
