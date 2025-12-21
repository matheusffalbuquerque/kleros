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
        }
        body {
            margin: 0;
            font-family: var(--text-font, 'Inter'), system-ui, -apple-system, sans-serif;
            background: var(--background-color, #0f172a);
            color: var(--text-color, #e2e8f0);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background-image: url(/storage/{{ $congregacao->config->banner_caminho }});
            background-size: cover;
            background-position: center;
        }
        .panel {
            max-width: 520px;
            width: 100%;
            background: rgba(0,0,0,0.85);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 16px 50px rgba(0,0,0,0.45);
            color: #e2e8f0;
            text-align: center;
        }
        .panel img {
            max-width: 120px;
            margin-bottom: 12px;
        }
        .panel h1 {
            margin: 0 0 10px;
            font-size: 1.8rem;
        }
        .panel p {
            margin: 0 0 10px;
        }
        .panel a {
            color: white;
        }
        .hint {
            margin-top: 14px;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="panel">
        <img src="/storage/{{ $congregacao->config->logo_caminho }}" alt="{{ $appName }}">
        <h1>{{ $appName }}</h1>
        <p>Abra este link no seu dispositivo e use “Adicionar à tela inicial” para instalar.</p>
        <button id="installBtn" style="display:none; margin: 12px auto; padding: 12px 16px; border: none; border-radius: 12px; background: var(--primary-color); color: var(--primary-contrast); font-weight: 700; cursor: pointer;">
            Instalar agora
        </button>
        <p class="hint">Se o prompt aparecer, aceite para instalar o app.</p>
        <p class="hint"><a href="{{ $baseUrl }}">Voltar ao site</a></p>
    </div>

    <script>
        (() => {
            let deferredPrompt = null;
            const btn = document.getElementById('installBtn');

            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                if (btn) {
                    btn.style.display = 'inline-flex';
                }
            });

            if (btn) {
                btn.addEventListener('click', async () => {
                    if (!deferredPrompt) return;
                    deferredPrompt.prompt();
                    const result = await deferredPrompt.userChoice;
                    deferredPrompt = null;
                    btn.style.display = 'none';
                });
            }
        })();
    </script>
</body>
</html>
