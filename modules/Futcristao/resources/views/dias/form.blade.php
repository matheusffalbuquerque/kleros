@php
    $isEdit = ($mode ?? 'create') === 'edit';
@endphp
<div class="futebol-form card">
    <header>
        <h1>{{ $isEdit ? 'Editar dia de futebol' : 'Agendar dia de futebol' }}</h1>
        <p>Associe o dia a um grupo e registre placares ou observações logo após o jogo.</p>
    </header>

    <form action="{{ $isEdit ? route('futcristao.dias.update', $dia) : route('futcristao.dias.store') }}" method="POST" class="form-grid">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif
        <label>
            <span>Grupo</span>
            <select name="futebol_grupo_id" required>
                <option value="">Selecione</option>
                @foreach($grupos as $grupo)
                    <option value="{{ $grupo->id }}" @selected(old('futebol_grupo_id', $dia->futebol_grupo_id) == $grupo->id)>{{ $grupo->nome }}</option>
                @endforeach
            </select>
        </label>
        <div class="grid-2">
            <label>
                <span>Data</span>
                <input type="date" name="data_jogo" required value="{{ old('data_jogo', optional($dia->data_jogo)->format('Y-m-d')) }}">
            </label>
            <label>
                <span>Horário</span>
                <input type="time" name="hora_jogo" value="{{ old('hora_jogo', $dia->hora_jogo) }}">
            </label>
        </div>
        <label>
            <span>Local</span>
            <input type="text" name="local" value="{{ old('local', $dia->local) }}" maxlength="255">
        </label>
        <label>
            <span>Status</span>
            <select name="status" required>
                @foreach(Modules\Futcristao\Models\FutebolDia::STATUS as $status)
                    <option value="{{ $status }}" @selected(old('status', $dia->status) === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </label>
        <div class="grid-2">
            <label>
                <span>Placar time A</span>
                <input type="number" name="placar_time_a" min="0" max="99" value="{{ old('placar_time_a', $dia->placar_time_a) }}">
            </label>
            <label>
                <span>Placar time B</span>
                <input type="number" name="placar_time_b" min="0" max="99" value="{{ old('placar_time_b', $dia->placar_time_b) }}">
            </label>
        </div>
        <label>
            <span>Observações</span>
            <textarea name="observacoes" rows="4" maxlength="2000">{{ old('observacoes', $dia->observacoes) }}</textarea>
        </label>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check2"></i> Salvar</button>
            <button type="button" class="btn btn-light" onclick="fecharJanelaModal()">Cancelar</button>
        </div>
    </form>
</div>

<style>
    .futebol-form.card {
        background: color-mix(in srgb, var(--background-color, #fff) 80%, white);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 18px 38px rgba(15,23,42,0.18);
        border: 1px solid color-mix(in srgb, var(--text-color, #0f172a) 15%, transparent);
        max-width: 880px;
        margin: 0 auto;
    }
    .futebol-form header h1 {
        margin: 0;
        color: var(--text-color);
    }
    .futebol-form header p {
        color: color-mix(in srgb, var(--text-color) 55%, white);
        margin-top: .35rem;
    }
    .form-grid {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-top: 1.5rem;
    }
    .grid-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1rem;
    }
    .form-grid label {
        display: flex;
        flex-direction: column;
        gap: .35rem;
        font-weight: 600;
        color: var(--text-color);
    }
    .form-grid input,
    .form-grid select,
    .form-grid textarea {
        border: 1px solid color-mix(in srgb, var(--text-color) 20%, transparent);
        border-radius: 10px;
        padding: .75rem 1rem;
        font-size: 1rem;
        background: rgba(255,255,255,0.9);
        color: var(--text-color);
    }
    .form-grid textarea {
        resize: vertical;
    }
    .form-actions {
        display: flex;
        gap: .75rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        @if($errors->any())
            flashMsg(@json($errors->first()), 'error');
        @endif
    });
</script>
