@extends('layouts.main')

@section('title', 'Debug Livewire | ' . $appName)

@section('content')
<div class="container">
    <h1>🔍 Debug Livewire</h1>
    
    <div class="card" style="margin: 20px 0; padding: 20px;">
        <h3>Componentes Disponíveis:</h3>
        <ul>
            <li>✅ <strong>counter</strong> - Contador simples</li>
            <li>✅ <strong>gestor-imagens</strong> - Gestor de imagens</li>
        </ul>
    </div>

    <div class="card" style="margin: 20px 0; padding: 20px;">
        <h3>Teste do Counter:</h3>
        @livewire('counter')
    </div>

    <div class="card" style="margin: 20px 0; padding: 20px;">
        <h3>Teste do GestorImagens:</h3>
        @livewire('gestor-imagens')
    </div>

    <div class="card" style="margin: 20px 0; padding: 20px;">
        <h3>⚠️ Erros Comuns:</h3>
        <ul>
            <li><code>{{ '@' }}livewire('nome-errado')</code> ❌ Componente não existe</li>
            <li><code>{{ '@' }}livewire('counter')</code> ✅ Funciona</li>
            <li><code>{{ '@' }}livewire('gestor-imagens')</code> ✅ Funciona</li>
        </ul>
    </div>
</div>
@endsection