@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')
<div class="container">
    <div class="nao-imprimir">
        <h1>Aniversariantes</h1>
        <div class="info">
            <h3>Selecione o mês</h3>
            <form method="GET" action="{{ route('membros.aniversariantes') }}" class="search-panel">
                <div class="search-panel-item">
                    <label for="mes">Mês:</label>
                    <select id="mes" name="mes">
                        @foreach ($meses as $numero => $nomeMes)
                            <option value="{{ $numero }}" @selected($mesSelecionado === $numero)>{{ $nomeMes }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="search-panel-item">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" placeholder="Buscar por nome" value="{{ $nomeFiltro ?? '' }}">
                </div>
                <div class="search-panel-item">
                    <button type="submit"><i class="bi bi-search"></i> Filtrar</button>
                    <button type="button" onclick="abrirJanelaModal('{{ route('membros.aniversariantes.config') }}')">
                        <i class="bi bi-gear"></i> Configurar mensagem
                    </button>
                    <a href="{{ route('membros.painel') }}">
                        <button type="button"><i class="bi bi-arrow-left-circle"></i> Voltar</button>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="list">
        <div class="list-title">
            <div class="item-2"><b>Nome</b></div>
            <div class="item-1"><b>Data</b></div>
            <div class="item-1"><b>Contato</b></div>
            <div class="item-1"><b>Idade</b></div>
        </div>
        <div id="content">
            @forelse ($membros as $membro)
                <div class="list-item">
                    <div class="item item-2">
                        <p style="display:flex; align-items:center; gap:.5em">
                            <img src="{{ $membro->foto ? asset('storage/' . $membro->foto) : asset('storage/images/newuser.png') }}" class="avatar" alt="Avatar">
                            {{ $membro->nome }}
                        </p>
                    </div>
                    <div class="item item-1">
                        <p>{{ optional($membro->data_nascimento)->format('d/m') }}</p>
                    </div>
                    <div class="item item-1">
                        <p>{{ $membro->telefone ?? 'Não informado' }}</p>
                    </div>
                    <div class="item item-1">
                        @php
                            $idade = optional($membro->data_nascimento)?->age;
                        @endphp
                        <p class="tag">{{ $idade ? $idade . ' anos' : '—' }}</p>
                    </div>
                </div>
            @empty
                <div class="card">
                    <p>Não há aniversariantes para este mês.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
