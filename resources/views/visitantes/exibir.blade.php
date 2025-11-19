@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')
@php
    use Illuminate\Support\Carbon;

    $visitors = trans('visitors');
    $common = $visitors['common'];
    $viewTexts = $visitors['view'];
    $formatDate = fn ($value) => $value ? Carbon::parse($value)->format('d/m/Y') : '-';
@endphp

<div class="container">
    <h1>{{ $viewTexts['title'] }}</h1>

    <form action="{{ route('visitantes.destroy', $visitante->id) }}" method="post" onsubmit="return handleSubmit(event, this, '{{ $common['messages']['confirm_delete'] ?? $common['buttons']['remove'] }}')">
        @csrf
        @method('DELETE')
        <div class="data-view">
            <div class="section">
                <h3>{{ $viewTexts['section'] }}</h3>
                <div class="section-grid w100">
                    <div class="field full-width horizontal">
                        <div class="field-content">
                            <label for="nome">{{ $common['fields']['name'] }}:</label>
                            <div class="card-title">{{ $visitante->nome }}</div>
                        </div>
                    </div>
                    <div class="field">
                        <label for="visitas_realizadas">Visitas realizadas:</label>
                        <div class="card-title">
                            <span>{{ $visitante->totalVisitas() }} visita(s)</span>
                        </div>
                    </div>
                    <div class="field">
                        <label for="data_visita">{{ $common['fields']['visit_date'] }}:</label>
                        <div class="card-title">{{ $formatDate($visitante->data_visita) }}</div>
                    </div>
                    <div class="field">
                        <label for="telefone">{{ $common['fields']['phone'] }}:</label>
                        <div class="card-title">{{ $visitante->telefone ?? '-' }}</div>
                    </div>
                    <div class="field">
                        <label for="status">{{ $common['fields']['status'] }}:</label>
                        <div class="card-title">{{ optional($visitante->sit_visitante)->titulo ?? $common['statuses']['not_informed'] }}</div>
                    </div>
                    <div class="field">
                        <label for="observacoes">{{ $common['fields']['notes'] }}:</label>
                        <div class="card-title">{{ $visitante->observacoes ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="form-options nao-imprimir limit-80">
                <button class="btn" onclick="abrirJanelaModal('{{ route('visitantes.form_editar', $visitante->id) }}')" type="button"><i class="bi bi-pencil-square"></i> {{ $common['buttons']['edit'] }}</button>
                <button class="btn imprimir" type="button"><i class="bi bi-printer"></i> {{ $common['buttons']['print'] }}</button>
                <button class="btn" type="submit"><i class="bi bi-trash"></i> {{ $common['buttons']['remove'] }}</button>
                <button type="button" onclick="window.history.back()" class="btn"><i class="bi bi-arrow-return-left"></i> {{ $common['buttons']['back'] }}</button>
            </div>
        </div>
    </form>
</div>

<style>
.badge-secondary {
    display: inline-block;
    padding: 0.25em 0.5em;
    margin-left: 0.5em;
    font-size: 0.88em;
    font-weight: 600;
    line-height: 1;
    color: var(--secondary-color);
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
}
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.imprimir').forEach((button) => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                window.print();
            });
        });
    });
</script>
@endpush
