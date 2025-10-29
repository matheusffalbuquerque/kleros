<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestor de Imagens</title>
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- CSS Estilo Geral-->
    @vite(['resources/css/app.scss'])
    
    <style>
        :root {
            --primary-color: {{$congregacao->config->conjunto_cores['primaria']}};
            --secondary-color: {{$congregacao->config->conjunto_cores['secundaria']}};
            --tertiary-color: {{$congregacao->config->conjunto_cores['terciaria']}};
            --background-color: {{$congregacao->config->tema->propriedades['cor-fundo']}};
            --text-color: {{$congregacao->config->tema->propriedades['cor-texto']}};
        }
        body {
            margin: 0;
            padding: 0;
            font-family: var(--font-family, 'Roboto', sans-serif);
            background: var(--background-color);
            color: var(--text-color);
        }
    </style>
    
    @livewireStyles
</head>
<body>
    @livewire('gestor-imagens')
    
    @livewireScripts
</body>
</html>
