@php
    $appName = $congregacao->nome_curto ?? config('app.name', 'Kleros');
    $baseUrl = url('/');
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appName }} | Web App</title>
    <link rel="shortcut icon" href="/storage/{{ $congregacao->config->logo_caminho }}" type="image/x-icon">
    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    @vite(['resources/css/app.scss'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Teko&family=Roboto&family=Source+Sans+Pro&family=Oswald&family=Saira&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: {{ $congregacao->config->conjunto_cores['primaria'] ?? '#677b96' }};
            --secondary-color: {{ $congregacao->config->conjunto_cores['secundaria'] ?? '#0a1929' }};
            --terciary-color: {{ $congregacao->config->conjunto_cores['terciaria'] ?? '#f44916' }};
            --primary-contrast: {{ getContrastTextColor($congregacao->config->conjunto_cores['primaria'])}};
            --secondary-contrast: {{ getContrastTextColor($congregacao->config->conjunto_cores['secundaria'])}};
            --terciary-contrast: {{ getContrastTextColor($congregacao->config->conjunto_cores['terciaria'])}};
            --text-font: {{ $congregacao->config->font_family }};
            --background-color: {{ $congregacao->config->tema->propriedades['cor-fundo'] }};
            --text-color: {{ $congregacao->config->tema->propriedades['cor-texto'] }};
            --border-style: {{ $congregacao->config->tema->propriedades['borda'] }};
        }
        body#login {
            background-image: url(/storage/{{ $congregacao->config->banner_caminho }});
            background-size: cover;
            background-position: center;
            margin: 0;
        }
        .login-container {
            text-align: center;
            background: rgba(0,0,0,0.55);
            padding: 28px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.45);
            color: #3f3f3f;
            max-width: 420px;
            width: 100%;
        }
        .login-container h2 {
            margin: 0 0 12px;
            font-size: 1.6rem;
        }
        .login-container p {
            margin: 0 0 12px;
            color: #3f3f3f;
        }
        .login-container .logo {
            max-width: 120px;
            margin-bottom: 12px;
        }
        .cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--primary-color);
            color: var(--primary-contrast);
            padding: 12px 18px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            margin: 10px 0 6px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.28);
        }
        .login-footer {
            text-align: center;
            color: #3f3f3f;
            font-size: 0.95rem;
            margin-top: 18px;
            text-shadow: 0 1px 4px rgba(0,0,0,0.6);
        }
        .steps {
            list-style: decimal;
            text-align: left;
            margin: 14px 0 4px;
            padding-left: 18px;
            color: #3f3f3f;
            line-height: 1.5;
        }
        .muted {
            color: #5a5a5a;
            font-size: 0.9rem;
            margin-top: 6px;
        }
    </style>
</head>

<body id="login">
    <div style="min-height: 100vh; display:flex; align-items:center; justify-content:center; padding: 24px;">
        <div class="login-container">
            <img src="/storage/{{ $congregacao->config->logo_caminho }}" alt="{{ $congregacao->denominacao->nome }}" class="logo">
            <h2>Instale o app web</h2>
            <p>Use como aplicativo no seu celular ou desktop com nosso PWA.</p>
            <a class="cta-btn" href="{{ route('webapp.start') }}"><i class="bi bi-download"></i> Baixar app</a>
            <ol class="steps">
                <li>Abra o link acima no navegador do seu dispositivo.</li>
                <li>Procure “Adicionar à tela inicial” ou “Instalar aplicativo”.</li>
                <li>Confirme e use o atalho criado na sua tela inicial.</li>
            </ol>
            <div class="muted">Se já estiver instalado, use o atalho ou menu do navegador para abrir. Em caso de dúvidas, fale com a equipe do {{ $appName }}.</div>
        </div>
    </div>
    
    <div class="login-footer">
        Ecossistema Kleros - Tecnologia a serviço do Reino
    </div>
</body>
</html>
