@php
    $isEdit = ($mode ?? 'create') === 'edit';
@endphp
<div class="futebol-form card">
    <header>
        <h1>{{ $isEdit ? 'Editar grupo' : 'Novo grupo de futebol' }}</h1>
        <p>Defina os dados principais do grupo para organizar escalações e convites.</p>
    </header>

    <form action="{{ $isEdit ? route('futcristao.grupos.update', $grupo) : route('futcristao.grupos.store') }}" method="POST" class="form-grid">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif
        <label>
            <span>Nome</span>
            <input type="text" name="nome" required value="{{ old('nome', $grupo->nome) }}" maxlength="255">
        </label>
        <label>
            <span>Descrição</span>
            <textarea name="descricao" rows="4" maxlength="2000">{{ old('descricao', $grupo->descricao) }}</textarea>
        </label>
        <label class="checkbox">
            <input type="checkbox" name="ativo" value="1" {{ old('ativo', $grupo->ativo) ? 'checked' : '' }}>
            <span>Grupo ativo para agendamentos</span>
        </label>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check2"></i> Salvar</button>
            <button type="button" class="btn btn-light" onclick="fecharJanelaModal()">Cancelar</button>
        </div>
    </form>
</div>

<style>
    .futebol-form.card {
        background: color-mix(in srgb, var(--background-color, #fff) 85%, white);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.18);
        border: 1px solid color-mix(in srgb, var(--text-color, #0f172a) 15%, transparent);
        max-width: 720px;
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
    .form-grid label {
        display: flex;
        flex-direction: column;
        gap: .35rem;
        font-weight: 600;
        color: var(--text-color);
    }
    .form-grid input[type="text"],
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
    .form-grid .checkbox {
        flex-direction: row;
        align-items: center;
        gap: .5rem;
        font-weight: 500;
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
        @if ($errors->any())
            flashMsg(@json($errors->first()), 'error');
        @endif
    });
</script>
