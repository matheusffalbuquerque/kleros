<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline | {{ $congregacao->nome_curto ?? config('app.name') }}</title>
    <style>
        :root {
            --primary: {{ $congregacao->config->conjunto_cores['primaria'] ?? '#677b96' }};
            --secondary: {{ $congregacao->config->conjunto_cores['secundaria'] ?? '#0a1929' }};
            --bg: {{ $congregacao->config->tema->propriedades['cor-fundo'] ?? '#0a1929' }};
            --text: {{ $congregacao->config->tema->propriedades['cor-texto'] ?? '#e5e7eb' }};
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--text);
            background: radial-gradient(circle at 10% 20%, rgba(255,255,255,0.05), transparent 25%),
                        radial-gradient(circle at 80% 0%, rgba(255,255,255,0.08), transparent 28%),
                        linear-gradient(135deg, var(--bg), #0f172a);
            padding: 24px;
        }
        .card {
            width: min(520px, 100%);
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            text-align: center;
            backdrop-filter: blur(8px);
        }
        .logo {
            width: 82px;
            height: 82px;
            border-radius: 20px;
            object-fit: contain;
            background: rgba(255,255,255,0.05);
            padding: 10px;
            margin-bottom: 14px;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08);
        }
        h1 {
            margin: 0 0 10px;
            font-size: 1.7rem;
            letter-spacing: -0.03em;
        }
        p {
            margin: 0 0 20px;
            color: rgba(255,255,255,0.85);
            line-height: 1.5;
        }
        .actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            border-radius: 14px;
            border: none;
            color: #fff;
            font-weight: 600;
            letter-spacing: 0.02em;
            cursor: pointer;
            text-decoration: none;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            box-shadow: 0 12px 24px rgba(0,0,0,0.25);
        }
        .muted {
            color: rgba(255,255,255,0.65);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <img src="/storage/{{ $congregacao->config->logo_caminho }}" alt="Logo {{ $congregacao->nome_curto }}" class="logo">
        <h1>Você está offline</h1>
        <p>Não foi possível conectar ao {{ $congregacao->nome_curto ?? 'app' }} agora. Verifique sua conexão e tente novamente.</p>
        <div class="actions">
            <button class="btn" onclick="window.location.reload()">Tentar novamente</button>
        </div>
        <p class="muted">Algumas áreas podem funcionar limitadas até a conexão voltar.</p>
    </div>
</body>
</html>
