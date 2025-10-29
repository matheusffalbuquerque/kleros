@php
    $visitors = trans('visitors');
    $common = $visitors['common'];
    $editTexts = $visitors['edit'];
@endphp

<form action="{{ route('visitantes.update', $visitante->id) }}" method="post"
    class="painel-visitante-editar-form"
    data-visitante-id="{{ $visitante->id }}">
    @csrf
    @method('PUT')
    <input type="hidden" name="return_to" value="{{ old('return_to', $returnTo ?? request('return_to')) }}">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-control">
        <div class="form-block">
            <h3>{{ $editTexts['section'] }}</h3>
            <div class="form-item">
                <label for="visitante_nome">{{ $common['fields']['name'] }}</label>
                <input type="text" id="visitante_nome" name="nome" value="{{ old('nome', $visitante->nome) }}" placeholder="{{ $common['placeholders']['name'] }}" required>
            </div>
            <div class="form-item">
                <label for="visitante_data">{{ $common['fields']['visit_date'] }}</label>
                <input type="date" id="visitante_data" name="data_visita" value="{{ old('data_visita', $visitante->data_visita) }}" required>
            </div>
            <div class="form-item">
                <label for="visitante_telefone">{{ $common['fields']['phone'] }}</label>
                <input type="text" id="visitante_telefone" name="telefone" value="{{ old('telefone', $visitante->telefone) }}" placeholder="{{ $common['placeholders']['phone'] }}" required>
            </div>
            <div class="form-item">
                <label for="visitante_situacao">{{ $common['fields']['status'] }}</label>
                <select name="sit_visitante" id="visitante_situacao" required>
                    @foreach ($situacao_visitante as $item)
                        <option value="{{ $item->id }}" @selected(old('sit_visitante', $visitante->sit_visitante_id) == $item->id)>{{ $item->titulo }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-item">
                <label for="visitante_observacoes">{{ $common['fields']['notes'] }}</label>
                <textarea id="visitante_observacoes" name="observacoes" rows="5" placeholder="{{ $common['placeholders']['notes'] }}">{{ old('observacoes', $visitante->observacoes) }}</textarea>
            </div>
        </div>
        <div class="form-options">
            <button class="btn" type="submit"><i class="bi bi-arrow-clockwise"></i> {{ $common['buttons']['update'] }}</button>
            <button class="btn" form="form-membrar"><i class="bi bi-person-add"></i> {{ $common['buttons']['convert_member'] }}</button>
            <button type="button" class="btn" onclick="window.history.back()"><i class="bi bi-x-circle"></i> {{ $common['buttons']['cancel'] }}</button>
        </div>
    </div>
</form>

<form id="form-membrar" action="{{ route('visitantes.membrar') }}" method="POST">
    @csrf
    <input type="hidden" name="nome" value="{{ old('nome', $visitante->nome) }}">
    <input type="hidden" name="telefone" value="{{ old('telefone', $visitante->telefone) }}">
</form>
