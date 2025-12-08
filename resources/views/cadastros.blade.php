@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')
@php
    $cad = trans('cadastros');
    $common = $cad['common'];
    $sections = $cad['sections'];
@endphp

<div class="container">
    <h1>{{ $cad['title'] }}</h1>

    {{-- Cultos --}}
    <div class="info" id="cultos">
        <h3>{{ $sections['cults']['title'] }}</h3>
        <b>{{ $sections['cults']['subtitle'] }}</b>
        <div class="card-container">
            @if ($cultos->count())
                @foreach ($cultos as $item)
                    <div class="info_item">
                        <div class="info-edit" onclick="abrirJanelaModal('{{ route('cultos.form_editar', $item->id) }}')"><i class="bi bi-pencil-square"></i></div>
                        <p><i class="bi bi-calendar-event"></i>
                            @php $data = new DateTime($item->data_culto); @endphp
                            {{ $data->format('d/m') }}
                        </p>
                        <p><i class="bi bi-mic"></i> {{ $sections['cults']['labels']['preacher'] }}: {{ $item->preletor ?? $common['no_description'] }}</p>
                        <p>
                            <b>{{ $sections['cults']['labels']['event'] }}</b>:
                            @if ($item->evento_id)
                                {{ $item->evento->titulo }}
                            @else
                                {{ $sections['cults']['labels']['none'] }}
                            @endif
                        </p>
                    </div>
                @endforeach
            @else
                <div class="card">
                    <p><i class="bi bi-exclamation-triangle"></i> {{ $sections['cults']['messages']['no_upcoming'] }}</p>
                </div>
            @endif
        </div>
        <button class="btn" onclick="abrirJanelaModal('{{ route('cultos.form_criar') }}')">
            <i class="bi bi-plus-circle"></i> {{ $sections['cults']['buttons']['schedule'] }}
        </button>
        <a href="{{ url('/cultos/agenda') }}">
            <button class="btn"><i class="bi bi-arrow-right-circle"></i> {{ $sections['cults']['buttons']['agenda'] }}</button>
        </a>
        <a href="{{ route('cultos.complete', 'adicionar') }}">
            <button class="btn"><i class="bi bi-plus-circle-fill"></i> {{ $sections['cults']['buttons']['register'] }}</button>
        </a>
        <a href="{{ url('/cultos/historico') }}">
            <button class="btn"><i class="bi bi-card-list"></i> {{ $sections['cults']['buttons']['history'] }}</button>
        </a>
    </div>

    {{-- Escalas --}}
    <div class="info" id="escalas">
        <h3>{{ $sections['scales']['title'] }}</h3>
        <b>{{ $sections['scales']['subtitle'] }}</b>
        <div class="card-container">
            @if ($tiposEscala->count())
                @foreach ($tiposEscala as $tipo)
                    <div class="list-item">
                        <div class="item-2">
                            <div class="card-title"><i class="bi bi-diagram-3"></i> {{ $tipo->nome }}</div>
                            <div class="card-description hint">
                                {{ $common['status_label'] }} {{ $tipo->ativo ? $common['status']['active'] : $common['status']['inactive'] }}
                            </div>
                        </div>
                        <div class="item-15">
                            <button type="button" class="btn-options" onclick="window.location.href='{{ route('escalas.painel', ['tipo' => $tipo->id]) }}'">
                                <i class="bi bi-list-task"></i> {{ $sections['scales']['buttons']['view'] }}
                            </button>
                            <button type="button" class="btn-options" onclick="abrirJanelaModal('{{ route('escalas.tipos.form_editar', $tipo->id) }}')">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form id="delete-tipo-{{ $tipo->id }}" action="{{ route('escalas.tipos.destroy', $tipo->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-options danger" onclick="handleSubmit(event, this.form, '{{ __('cadastros.confirmations.delete_scale_type') }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card">
                    <p><i class="bi bi-exclamation-triangle"></i> {{ $sections['scales']['messages']['empty'] }}</p>
                </div>
            @endif
        </div>
        <button class="btn mg-top-10" onclick="abrirJanelaModal('{{ route('escalas.tipos.form_criar') }}')">
            <i class="bi bi-plus-circle"></i> {{ $sections['scales']['buttons']['new_type'] }}
        </button>
        <button class="btn mg-top-10" onclick="abrirJanelaModal('{{ route('escalas.form_criar') }}')">
            <i class="bi bi-plus-circle-fill"></i> {{ $sections['scales']['buttons']['generate'] }}
        </button>
        <button id="escalas" class="imprimir btn mg-top-10" data-action="0">
            <i class="bi bi-printer"></i> {{ $sections['scales']['buttons']['print'] }}
        </button>
    </div>

    {{-- Eventos --}}
    <div class="info" id="eventos">
        <h3>{{ $sections['events']['title'] }}</h3>
        <b>{{ $sections['events']['subtitle'] }}</b>
        <div class="card-container">
            @if ($eventos->count())
                @foreach ($eventos as $item)
                    <div class="card">
                        <div class="card-edit" onclick="abrirJanelaModal('{{ route('eventos.form_editar', $item->id) }}')">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <div class="card-date">
                            <i class="bi bi-calendar-event"></i>
                            @php $data = new DateTime($item->data_inicio); @endphp
                            {{ $data->format('d/m') }}
                        </div>
                        <div class="card-title">{{ $item->titulo }}</div>
                        <div class="card-owner">{{ optional($item->grupo)->nome ?? $common['general'] }}</div>
                        <div class="card-description">{{ $item->descricao ?? $common['no_description'] }}</div>
                    </div>
                @endforeach
            @else
                <div class="card">
                    <p><i class="bi bi-exclamation-triangle"></i> {{ $sections['events']['messages']['no_upcoming'] }}</p>
                </div>
            @endif
        </div>
        <button class="btn mg-top-10" onclick="abrirJanelaModal('{{ route('eventos.form_criar') }}')">
            <i class="bi bi-plus-circle"></i> {{ $sections['events']['buttons']['new'] }}</button>
        <a href="{{ url('/eventos/historico') }}">
            <button class="btn mg-top-10"><i class="bi bi-card-list"></i> {{ $sections['events']['buttons']['history'] }}</button>
        </a>
        <a href="{{ url('/eventos/agenda') }}">
            <button class="btn mg-top-10"><i class="bi bi-arrow-right-circle"></i> {{ $sections['events']['buttons']['agenda'] }}</button>
        </a>
    </div>

    {{-- Reuniões --}}
    <div class="info" id="reunioes">
        <h3>{{ $sections['meetings']['title'] }}</h3>
        <b>{{ $sections['meetings']['subtitle'] }}</b>
        <div class="card-container">
            @if ($reunioes->count())
                @foreach ($reunioes as $item)
                    <div class="card">
                        <div class="card-edit" onclick="abrirJanelaModal('{{ route('reunioes.form_editar', $item->id) }}')">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <div class="card-date">
                            <i class="bi bi-calendar-event"></i>
                            @php $data = new DateTime($item->data_inicio); @endphp
                            {{ $data->format('d/m') }} - {{ $data->format('H:i') }} h
                        </div>
                        <div class="card-title">{{ $item->assunto }}</div>
                        <div class="card-description">{{ $item->descricao ?? $common['no_description'] }}</div>
                    </div>
                @endforeach
            @else
                <div class="card">
                    <p><i class="bi bi-exclamation-triangle"></i> {{ $sections['meetings']['messages']['no_upcoming'] }}</p>
                </div>
            @endif
        </div>
        <button class="btn mg-top-10" onclick="abrirJanelaModal('{{ route('reunioes.form_criar') }}')">
            <i class="bi bi-plus-circle"></i> {{ $sections['meetings']['buttons']['new'] }}
        </button>
        <a href="{{ route('reunioes.painel') }}">
            <button class="btn mg-top-10"><i class="bi bi-card-list"></i> {{ $sections['meetings']['buttons']['history'] }}</button>
        </a>
        <a href="{{ url('/eventos/agenda') }}">
            <button class="btn mg-top-10"><i class="bi bi-arrow-right-circle"></i> {{ $sections['meetings']['buttons']['agenda'] }}</button>
        </a>
    </div>

    {{-- Pesquisas --}}
    <div class="info" id="pesquisas">
        <h3>{{ $sections['research']['title'] }}</h3>
        <b>{{ $sections['research']['subtitle'] }}</b>
        <div class="card-container">
            @if ($pesquisas->count())
                @foreach ($pesquisas as $item)
                    <div class="card">
                        <div class="card-edit" onclick="abrirJanelaModal('{{ route('pesquisas.form_editar', $item->id) }}')">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <div class="card-title">{{ $item->titulo }}</div>
                        <div class="card-date">
                            <i class="bi bi-calendar-event"></i>
                            @php
                                $dataInicio = optional($item->data_inicio)->format('d/m/Y');
                                $dataFim = optional($item->data_fim)->format('d/m/Y');
                            @endphp
                            <span>
                                @if ($dataInicio && $dataFim)
                                    {{ $dataInicio }} {{ $common['until'] }} {{ $dataFim }}
                                @elseif ($dataInicio)
                                    Início em {{ $dataInicio }}
                                @elseif ($dataFim)
                                    {{ $common['until'] }} {{ $dataFim }}
                                @else
                                    Sem prazo definido
                                @endif
                            </span>
                        </div>
                        <div class="card-meta"><b>{{ $common['editor'] }}: </b>{{ optional($item->criador)->nome ?? $sections['research']['messages']['no_responsible'] }}</div>
                    </div>
                @endforeach
            @else
                <div class="card">
                    <p><i class="bi bi-exclamation-triangle"></i> {{ $sections['research']['messages']['empty'] }}</p>
                </div>
            @endif
        </div>
        <button class="btn mg-top-10" onclick="abrirJanelaModal('{{ route('pesquisas.form_criar') }}')">
            <i class="bi bi-plus-circle"></i> {{ $sections['research']['buttons']['new'] }}
        </button>
        <a href="{{ route('pesquisas.painel') }}">
            <button class="btn mg-top-10"><i class="bi bi-card-list"></i> {{ $sections['research']['buttons']['panel'] }}</button>
        </a>
    </div>

    {{-- Visitantes --}}
    <div class="info" id="visitantes">
        <h3>{{ $sections['visitors']['title'] }}</h3>
        <div class="card-container">
            @if ($visitantes_total)
                <div class="info_item">
                    <p>{{ $sections['visitors']['labels']['month'] }}</p>
                    <h2>{{ $visitantes_total }}</h2>
                </div>
            @else
                <div class="card">
                    <p><i class="bi bi-exclamation-triangle"></i> {{ $sections['visitors']['labels']['none'] }}</p>
                </div>
            @endif
        </div>
        <a href="{{ url('/visitantes/adicionar') }}">
            <button class="btn mg-top-10"><i class="bi bi-plus-circle"></i> {{ $sections['visitors']['buttons']['new'] }}</button>
        </a>
        <a href="{{ url('/visitantes/historico') }}">
            <button class="btn mg-top-10"><i class="bi bi-card-list"></i> {{ $sections['visitors']['buttons']['history'] }}</button>
        </a>
    </div>

    {{-- Grupos --}}
    <div class="info" id="grupos_da_congregacao">
        <h3>{{ $sections['groups']['title'] }}</h3>
        <div class="card-container">
            @if ($grupos->count())
                @foreach ($grupos as $item)
                    <div class="list-item">
                        <div class="item-15">
                            <div class="card-title">{{ $item->nome }}</div>
                            <div class="card-description">{{ $item->descricao }}</div>
                        </div>
                        <div class="item-2">
                            <div class="card-description">
                                <b>{{ $common['leader'] }}: </b>{{ optional($item->lider)->nome }}@if($item->colider) | {{ optional($item->colider)->nome }}@endif
                            </div>
                        </div>
                        <div class="item-15">
                            <a href="{{ url('/grupos/integrantes/' . $item->id) }}">
                                <button type="button" class="btn-options"><i class="bi bi-eye"></i> {{ $sections['groups']['buttons']['members'] }}</button>
                            </a>
                            <button type="button" class="btn-options" onclick="abrirJanelaModal('{{ route('grupos.form_editar', $item->id) }}')">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form id="delete-grupo-{{ $item->id }}" action="{{ route('grupos.destroy', $item->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-options danger" onclick="handleSubmit(event, this.form, '{{ __('cadastros.confirmations.delete_group') }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card">
                    <p><i class="bi bi-exclamation-triangle"></i> {{ $sections['groups']['messages']['empty'] }}</p>
                </div>
            @endif
        </div>
        <button class="btn mg-top-10" onclick="abrirJanelaModal('{{ route('grupos.form_criar') }}')">
            <i class="bi bi-plus-circle"></i> {{ $sections['groups']['buttons']['new'] }}
        </button>
        <button id="grupos-imprimir" class="imprimir btn mg-top-10" data-action="0" data-reference="grupos">
            <i class="bi bi-printer"></i> {{ $sections['groups']['buttons']['print'] }}
        </button>
    </div>

    {{-- Departamentos --}}
    <div class="info" id="departamentos">
        <h3>{{ $sections['departments']['title'] }}</h3>
        <div class="card-container">
            @if ($departamentos->count())
                @foreach ($departamentos as $item)
                    <div class="list-item">
                        <div class="item-15">
                            <div class="card-title"><i class="bi bi-intersect"></i> {{ $item->nome }}</div>
                            <div class="card-description">{{ $item->descricao }}</div>
                        </div>
                        <div class="item-2">
                            <div class="card-description">
                                <b>{{ $common['leader'] }}: </b>{{ optional($item->lider)->nome }}@if($item->colider) | {{ optional($item->colider)->nome }}@endif
                            </div>
                        </div>
                        <div class="item-15">
                            <a href="{{ route('departamentos.integrantes', $item->id) }}">
                                <button type="button" class="btn-options"><i class="bi bi-eye"></i> {{ $sections['departments']['buttons']['team'] }}</button>
                            </a>
                            <button type="button" class="btn-options" onclick="abrirJanelaModal('{{ route('departamentos.form_editar', $item->id) }}')">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form id="delete-departamento-{{ $item->id }}" action="{{ route('departamentos.destroy', $item->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-options danger" onclick="handleSubmit(event, this.form, '{{ __('cadastros.confirmations.delete_department') }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card">
                    <p><i class="bi bi-exclamation-triangle"></i> {{ $sections['departments']['messages']['empty'] }}</p>
                </div>
            @endif
        </div>
        <button class="btn mg-top-10" onclick="abrirJanelaModal('{{ route('departamentos.form_criar') }}')">
            <i class="bi bi-plus-circle"></i> {{ $sections['departments']['buttons']['new'] }}
        </button>
        <button id="departamentos-imprimir" class="imprimir btn mg-top-10" data-action="0" data-reference="departamentos">
            <i class="bi bi-printer"></i> {{ $sections['departments']['buttons']['print'] }}
        </button>
    </div>

    {{-- Setores --}}
    @if ($congregacao->config->agrupamentos === 'setor')
        <div class="info" id="setores">
            <h3>{{ $sections['sectors']['title'] }}</h3>
            <div class="card-container">
                @if (($setores ?? collect())->count())
                    @foreach ($setores as $setor)
                        <div class="list-item">
                            <div class="item-15">
                                <div class="card-title">{{ $setor->nome }}</div>
                                @if ($setor->lider)
                                    <div class="card-description">
                                        <b>{{ $common['leader'] }}: </b>{{ $setor->lider->nome }}@if($setor->colider) {{ ' / ' . $setor->colider->nome }}@endif
                                    </div>
                                @endif
                            </div>
                            <div class="item-2">
                                <div class="card-description">{{ $setor->descricao ?: $sections['sectors']['messages']['no_description'] }}</div>
                                @php
                                    $relacionados = $setor->departamentos->pluck('nome')
                                        ->merge($setor->grupos->pluck('nome'))
                                        ->filter()
                                        ->unique();
                                @endphp
                                <small class="hint">
                                    <b>{{ $sections['sectors']['labels']['related'] }}:</b>
                                    {{ $relacionados->implode(', ') ?: $common['no_linked'] }}
                                </small>
                            </div>
                            <div class="item-15">
                                <button type="button" class="btn-options" onclick="abrirJanelaModal('{{ route('setores.form_editar', $setor->id) }}')">
                                    <i class="bi bi-pencil-square"></i> {{ $sections['sectors']['buttons']['edit'] }}
                                </button>
                                <form id="delete-setor-{{ $setor->id }}" action="{{ route('setores.destroy', $setor->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-options danger" onclick="handleSubmit(event, this.form, '{{ __('cadastros.confirmations.delete_sector') }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card">
                        <p><i class="bi bi-exclamation-triangle"></i> {{ $sections['sectors']['messages']['empty'] }}</p>
                    </div>
                @endif
            </div>
            <button class="btn mg-top-10" onclick="abrirJanelaModal('{{ route('setores.form_criar') }}')">
                <i class="bi bi-plus-circle"></i> {{ $sections['sectors']['buttons']['new'] }}
            </button>
        </div>
    @endif

    {{-- Controle Financeiro --}}
    <div class="info" id="controle-financeiro">
        <h3>{{ $sections['finance']['title'] }}</h3>
        <div class="card-container">
            @forelse ($caixas as $caixa)
                <div class="alterlist" id="caixa-{{ $caixa->id }}">
                    <div class="item-15">
                        <div class="card-title">{{ $caixa->nome }}</div>
                        <p class="hint">
                            {{ $sections['finance']['labels']['current_balance'] }}: R$ {{ number_format($caixa->saldo_atual, 2, ',', '.') }}<br>
                            {{ $sections['finance']['labels']['entries'] }}:
                            <span class="text-success">R$ {{ number_format($caixa->entradas_total, 2, ',', '.') }}</span>
                            •
                            {{ $sections['finance']['labels']['exits'] }}:
                            <span class="text-danger">R$ {{ number_format($caixa->saidas_total, 2, ',', '.') }}</span>
                        </p>
                        @if ($caixa->descricao)
                            <div class="card-description">{{ $caixa->descricao }}</div>
                        @endif
                    </div>
                    <div class="item-2">
                        <h4>{{ $sections['finance']['labels']['recent'] }}</h4>
                        @php $ultimos = $caixa->lancamentos->take(5); @endphp
                        @if ($ultimos->count())
                            <table class="table">
                                <tbody>
                                    @foreach ($ultimos as $lancamento)
                                        <tr>
                                            <td><small>{{ optional($lancamento->data_lancamento)->format('d/m') }}</small></td>
                                            <td><small>@if($lancamento->tipoLancamento) • {{ $lancamento->tipoLancamento->nome }} @endif</small></td>
                                            <td class="{{ $lancamento->tipo === 'entrada' ? 'text-success' : 'text-danger' }}">
                                                <small>R$ {{ number_format($lancamento->valor, 2, ',', '.') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="card-description">{{ $sections['finance']['messages']['no_entries'] }}</p>
                        @endif
                    </div>
                    <div class="item-15">
                        <div class="form-options">
                            <a href="{{ route('financeiro.painel') }}">
                                <button type="button" class="btn-options"><i class="bi bi-list"></i> {{ $common['manage'] }}</button>
                            </a>
                            <button type="button" class="btn-options" onclick="abrirJanelaModal('{{ route('financeiro.caixas.form_editar', $caixa->id) }}')">
                                <i class="bi bi-pencil-square" title="{{ $common['edit'] }}"></i>
                            </button>
                            <form action="{{ route('financeiro.caixas.destroy', $caixa->id) }}" method="POST" onsubmit="return handleSubmit(event, this, '{{ __('cadastros.confirmations.delete_cashier', ['name' => $caixa->nome]) }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-options danger">
                                    <i class="bi bi-trash" title="{{ $common['delete'] }}"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <p><i class="bi bi-exclamation-triangle"></i> {{ $sections['finance']['messages']['no_cashier'] }}</p>
                </div>
            @endforelse
        </div>
        <div class="form-options">
            <button class="btn" type="button" onclick="abrirJanelaModal('{{ route('financeiro.caixas.form_criar') }}')">
                <i class="bi bi-plus-circle"></i> {{ $sections['finance']['buttons']['new_cashier'] }}
            </button>
            <button class="btn" type="button" onclick="abrirJanelaModal('{{ route('financeiro.tipos.form_criar') }}')">
                <i class="bi bi-plus-circle"></i> {{ $sections['finance']['buttons']['new_type'] }}
            </button>
        </div>
    </div>

    {{-- Cursos --}}
    {{-- @if (module_enabled('cursos'))
        <div class="info" id="cursos">
            <h3>{{ $sections['courses']['title'] }}</h3>
            <div class="card-container">
                @if ($cursos->count())
                    @foreach ($cursos as $item)
                        <div class="alterlist">
                            <div class="item-15">
                                <div class="card-title">
                                    <img style="width: 2em; border-radius: 10px;" src="{{ asset('storage/' . ($item->icone ?? 'images/podcast.png')) }}" alt="">
                                    {{ $item->titulo }}
                                </div>
                            </div>
                            <div class="item-2">
                                <div class="card-description">{{ $item->descricao }}</div>
                            </div>
                            <div class="item-15">
                                <a href="#">
                                    <button type="button" class="btn-options"><i class="bi bi-eye"></i> {{ $common['view'] }}</button>
                                </a>
                                <button type="button" title="{{ $sections['courses']['buttons']['edit'] }}" class="btn-options" onclick="abrirJanelaModal('{{ route('cursos.form_editar', $item->id) }}')">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form id="delete-curso-{{ $item->id }}" action="{{ route('cursos.destroy', $item->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" title="{{ $common['delete'] }}" class="btn-options danger" onclick="handleSubmit(event, this.form, '{{ __('cadastros.confirmations.delete_course') }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card">
                        <p><i class="bi bi-exclamation-triangle"></i> {{ $sections['courses']['messages']['empty'] }}</p>
                    </div>
                @endif
            </div>
            <button class="btn mg-top-10" onclick="abrirJanelaModal('{{ route('cursos.form_criar') }}')">
                <i class="bi bi-plus-circle"></i> {{ $sections['courses']['buttons']['new'] }}
            </button>
            <button id="cursos-imprimir" class="imprimir btn mg-top-10" data-action="0" data-reference="cursos">
                <i class="bi bi-printer"></i> {{ $sections['courses']['buttons']['print'] }}
            </button>
        </div>
    @endif --}}

    {{-- Células --}}
    @if (module_enabled('celulas'))
        <div class="info" id="celulas">
            <h3>{{ $sections['cells']['title'] }}</h3>
            <div class="card-container">
                @if ($celulas->count())
                    @foreach ($celulas as $item)
                        <div class="list-item">
                            <div class="item-15">
                                <div class="card-title"><i class="bi bi-cup-hot"></i> {{ $item->identificacao }}</div>
                                <div class="card-description">
                                    <b>{{ $common['leader'] }}: </b>{{ optional($item->lider)->nome }}@if($item->colider) | {{ optional($item->colider)->nome }}@endif
                                </div>
                            </div>
                            <div class="item-2">
                                <div class="card-description">
                                    <b>{{ $sections['cells']['labels']['meeting'] }}: </b>{{ diaSemana($item->dia_encontro) }} / {{ date('H:i', strtotime($item->hora_encontro)) }} h
                                </div>
                            </div>
                            <div class="item-15">
                                <a href="{{ route('celulas.integrantes', $item->id) }}">
                                    <button type="button" class="btn-options"><i class="bi bi-eye"></i> {{ $sections['cells']['buttons']['view'] }}</button>
                                </a>
                                <button type="button" title="{{ $sections['cells']['buttons']['edit'] }}" class="btn-options" onclick="abrirJanelaModal('{{ route('celulas.form_editar', $item->id) }}')">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form id="delete-celula-{{ $item->id }}" action="{{ route('grupos.destroy', $item->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" title="{{ $common['delete'] }}" class="btn-options danger" onclick="handleSubmit(event, this.form, '{{ __('cadastros.confirmations.delete_cell') }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card">
                        <p><i class="bi bi-exclamation-triangle"></i> {{ $sections['cells']['messages']['empty'] }}</p>
                    </div>
                @endif
            </div>
            @if (Route::has('celulas.form_criar'))
                <button class="btn mg-top-10" onclick="abrirJanelaModal('{{ route('celulas.form_criar') }}')">
                    <i class="bi bi-plus-circle"></i> {{ $sections['cells']['buttons']['new'] }}
                </button>
            @endif
            <button id="celulas-imprimir" class="imprimir btn mg-top-10" data-action="0" data-reference="celulas">
                <i class="bi bi-printer"></i> {{ $sections['cells']['buttons']['print'] }}
            </button>
        </div>
    @endif
    <div class="info">
        @include('noticias.includes.destaques', ['destaques' => $destaques])
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.imprimir').forEach(function (button) {
            button.addEventListener('click', function () {
                const printData = this.getAttribute('data-action');
                const reference = this.dataset.reference;
                window.open(`/${reference}/imprimir/${printData}`, '_blank');
            });
        });
    });
</script>
@endpush
