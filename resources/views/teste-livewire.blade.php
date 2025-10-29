@extends('layouts.main')

@section('title', 'Teste Livewire | ' . $appName)

@section('content')
<div class="container">
    <h1>🚀 Teste do Livewire</h1>
    <p>Esta página demonstra que o Livewire está funcionando corretamente no seu sistema!</p>
    
    @livewire('counter')
    
    <div class="card" style="margin-top: 30px; padding: 20px;">
        <h3>✅ Livewire Instalado com Sucesso!</h3>
        <p>Agora você pode criar componentes Livewire para:</p>
        <ul>
            <li>Formulários reativos sem JavaScript</li>
            <li>Atualizações em tempo real</li>
            <li>Interfaces dinâmicas</li>
            <li>Paginação automática</li>
            <li>Validação em tempo real</li>
            <li>E muito mais!</li>
        </ul>
        
        <div style="margin-top: 20px;">
            <strong>Como criar um novo componente:</strong><br>
            <code style="background: #f1f1f1; padding: 5px;">php artisan make:livewire MeuComponente</code>
        </div>
        
        <div style="margin-top: 15px;">
            <strong>Como usar em uma view:</strong><br>
            <code style="background: #f1f1f1; padding: 5px;">{{ '@' }}livewire('meu-componente')</code>
        </div>
    </div>
</div>
@endsection