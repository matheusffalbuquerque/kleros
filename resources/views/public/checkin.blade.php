@php
    $config = $congregacao->config ?? null;
    $theme = $config->tema ?? null;

    $appName = $congregacao->nome_curto ?? config('app.name', 'Kleros');
    $identificacao = $congregacao->identificacao ?? $appName;
    $primary = data_get($config, 'conjunto_cores.primaria', '#c37c5a');
    $secondary = data_get($config, 'conjunto_cores.secundaria', '#56362e');
    $tertiary = data_get($config, 'conjunto_cores.terciaria', '#f2d9c3');
    $background = data_get($theme, 'propriedades.cor-fundo', '#f4ede4');
    $text = data_get($theme, 'propriedades.cor-texto', '#1f2937');
    $font = $config->font_family ?? 'Inter, system-ui, -apple-system, sans-serif';
    $logo = $config && $config->logo_caminho ? '/storage/'.$config->logo_caminho : null;
    $banner = $config && $config->banner_caminho ? '/storage/'.$config->banner_caminho : null;

    $rawPhone = preg_replace('/\D+/', '', $congregacao->telefone ?? '');
    $phoneLink = $rawPhone ? 'https://wa.me/'.$rawPhone : null;
    $emailLink = $congregacao->email ? 'mailto:'.$congregacao->email : null;
    $contactLink = $phoneLink ?? $emailLink ?? '#';

    $instagram = data_get($config, 'social.instagram') ?? data_get($config, 'links.instagram');
    $instagramLink = $instagram
        ? (preg_match('/^https?:\\/\\//', $instagram) ? $instagram : 'https://instagram.com/'.$instagram)
        : '#';

    $memberLinks = [
        [
            'icon' => '📲',
            'title' => 'Baixar o app do membro',
            'description' => 'Instale o webapp e leve a comunidade no bolso.',
            'href' => route('webapp'),
        ],
        [
            'icon' => '🪪',
            'title' => 'Entrar pelo navegador',
            'description' => 'Acesse com seu login para participar e servir.',
            'href' => route('login'),
        ],
        [
            'icon' => '💝',
            'title' => 'Contribuir financeiramente',
            'description' => 'Combine ofertas, dízimos ou PIX com nossa tesouraria.',
            'href' => $contactLink,
        ],
    ];

    $visitorLinks = [
        [
            'icon' => '🤝',
            'title' => 'Sou visitante',
            'description' => 'Deixe seu contato para recebermos você com carinho.',
            'href' => $contactLink,
        ],
        [
            'icon' => '🗓️',
            'title' => 'Programação básica',
            'description' => 'Veja rapidamente nossos encontros principais.',
            'href' => '#programacao',
        ],
        [
            'icon' => '🌿',
            'title' => 'Conheça a igreja',
            'description' => 'Visão, valores e como caminhamos juntos.',
            'href' => '#sobre',
        ],
    ];

    $programas = [
        ['titulo' => 'Celebração de domingo', 'descricao' => 'Culto principal com toda a igreja reunida.'],
        ['titulo' => 'Grupos na semana', 'descricao' => 'Conecte-se em grupos menores para discipulado e cuidado.'],
        ['titulo' => 'Intercessão e oração', 'descricao' => 'Momentos dedicados à oração pela igreja e pela cidade.'],
    ];
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in | {{ $appName }}</title>
    <style>
        :root {
            --primary: {{ $primary }};
            --secondary: {{ $secondary }};
            --tertiary: {{ $tertiary }};
            --background: {{ $background }};
            --text: {{ $text }};
            --muted: rgba(0,0,0,0.58);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: {{ $font }};
            background: radial-gradient(circle at 10% 20%, rgba(255,255,255,0.6), transparent 28%),
                        radial-gradient(circle at 90% 10%, rgba(0,0,0,0.05), transparent 25%),
                        linear-gradient(135deg, var(--background), #f8f0e7);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .canvas {
            width: min(540px, 100%);
            background: rgba(255,255,255,0.9);
            border-radius: 22px;
            padding: 20px 18px 26px;
            box-shadow: 0 14px 32px rgba(0,0,0,0.12);
            position: relative;
            overflow: hidden;
        }
        .backdrop {
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, rgba(0,0,0,0.04), rgba(0,0,0,0.02));
            @if($banner)
                background-image:
                    linear-gradient(160deg, rgba(0,0,0,0.35), rgba(0,0,0,0.1)),
                    url('{{ $banner }}');
                background-size: cover;
                background-position: center;
                filter: saturate(1.1);
                opacity: 0.32;
            @endif
        }
        .inner {
            position: relative;
            z-index: 1;
        }
        .hero {
            text-align: center;
            padding: 10px 12px 22px;
        }
        .logo-wrap {
            width: 110px;
            height: 110px;
            border-radius: 26px;
            background: rgba(255,255,255,0.8);
            margin: 0 auto 14px;
            display: grid;
            place-items: center;
            box-shadow: inset 0 0 0 1px rgba(0,0,0,0.04), 0 14px 26px rgba(0,0,0,0.08);
        }
        .logo-wrap img {
            max-width: 90px;
            max-height: 90px;
            object-fit: contain;
        }
        .hero h1 {
            margin: 0;
            font-size: 1.7rem;
            letter-spacing: -0.02em;
        }
        .hero p {
            margin: 6px 0 0;
            color: var(--muted);
            line-height: 1.4;
        }
        .section {
            margin-top: 18px;
        }
        .section-title {
            font-weight: 700;
            margin: 0 0 10px;
            color: var(--secondary);
            letter-spacing: 0.02em;
            font-size: 0.95rem;
        }
        .link-list {
            display: grid;
            gap: 10px;
        }
        .link-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 12px 12px 14px;
            background: rgba(255,255,255,0.92);
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.06);
            text-decoration: none;
            color: inherit;
            box-shadow: 0 10px 18px rgba(0,0,0,0.05);
            transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        }
        .link-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 26px rgba(0,0,0,0.08);
            border-color: rgba(0,0,0,0.08);
        }
        .icon-pill {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: linear-gradient(140deg, rgba(0,0,0,0.05), rgba(0,0,0,0.02));
            font-size: 1.1rem;
        }
        .link-content h3 {
            margin: 0;
            font-size: 1rem;
            letter-spacing: -0.01em;
        }
        .link-content p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 0.92rem;
            line-height: 1.4;
        }
        .chevron {
            margin-left: auto;
            color: rgba(0,0,0,0.35);
            font-weight: 700;
        }
        .info-card {
            margin-top: 18px;
            padding: 16px;
            border-radius: 18px;
            background: rgba(255,255,255,0.94);
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 14px 24px rgba(0,0,0,0.06);
        }
        .info-card h4 {
            margin: 0 0 10px;
            font-size: 1.05rem;
            letter-spacing: -0.01em;
            color: var(--secondary);
        }
        .program-list {
            display: grid;
            gap: 10px;
        }
        .program-item {
            padding: 12px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(0,0,0,0.03), rgba(0,0,0,0.01));
            border: 1px solid rgba(0,0,0,0.05);
        }
        .program-item strong {
            display: block;
            margin-bottom: 4px;
            color: var(--text);
        }
        .muted {
            color: var(--muted);
            font-size: 0.92rem;
            margin: 12px 0 0;
        }
        .contact-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 12px;
        }
        .chip {
            padding: 8px 12px;
            border-radius: 12px;
            background: rgba(0,0,0,0.05);
            font-weight: 600;
            color: var(--secondary);
            text-decoration: none;
            border: 1px solid rgba(0,0,0,0.04);
        }
        .footer {
            text-align: center;
            margin-top: 18px;
            color: var(--muted);
            font-size: 0.9rem;
        }
        @media (max-width: 480px) {
            body { padding: 16px; }
            .canvas { border-radius: 18px; }
            .link-card { padding: 12px; }
        }
    </style>
</head>
<body>
    <div class="canvas">
        <div class="backdrop"></div>
        <div class="inner">
            <header class="hero">
                <div class="logo-wrap">
                    @if($logo)
                        <img src="{{ $logo }}" alt="Logo {{ $appName }}">
                    @else
                        <span style="font-weight: 800; color: var(--secondary); font-size: 1.1rem;">{{ substr($appName, 0, 3) }}</span>
                    @endif
                </div>
                <h1>{{ $appName }}</h1>
                <p>{{ $identificacao }}</p>
            </header>

            <section class="section">
                <p class="section-title">Para membros</p>
                <div class="link-list">
                    @foreach($memberLinks as $link)
                        <a class="link-card" href="{{ $link['href'] }}" @if($link['href'] === '#') aria-disabled="true" @endif>
                            <span class="icon-pill">{{ $link['icon'] }}</span>
                            <div class="link-content">
                                <h3>{{ $link['title'] }}</h3>
                                <p>{{ $link['description'] }}</p>
                            </div>
                            <span class="chevron">›</span>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="section">
                <p class="section-title">Para visitantes</p>
                <div class="link-list">
                    @foreach($visitorLinks as $link)
                        <a class="link-card" href="{{ $link['href'] }}" @if($link['href'] === '#') aria-disabled="true" @endif>
                            <span class="icon-pill">{{ $link['icon'] }}</span>
                            <div class="link-content">
                                <h3>{{ $link['title'] }}</h3>
                                <p>{{ $link['description'] }}</p>
                            </div>
                            <span class="chevron">›</span>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="section">
                <p class="section-title">Siga no Instagram</p>
                <div class="link-list">
                    <a class="link-card" href="{{ $instagramLink }}" @if($instagramLink === '#') aria-disabled="true" @endif>
                        <span class="icon-pill">📸</span>
                        <div class="link-content">
                            <h3>{{ $appName }} nas redes</h3>
                            <p>@ {{ $instagram ? ltrim($instagram, '@') : 'perfil' }}</p>
                        </div>
                        <span class="chevron">›</span>
                    </a>
                </div>
            </section>

            <section id="programacao" class="info-card">
                <h4>Programação básica</h4>
                <div class="program-list">
                    @foreach($programas as $item)
                        <div class="program-item">
                            <strong>{{ $item['titulo'] }}</strong>
                            <span class="muted">{{ $item['descricao'] }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="muted">Horários e locais podem variar. Confira no app ou fale pelo whatsapp.</p>
                <div class="contact-row">
                    <a class="chip" href="{{ route('login') }}">Abrir agenda no app</a>
                    <a class="chip" href="{{ $contactLink }}">Falar conosco</a>
                </div>
            </section>

            <section id="sobre" class="info-card">
                <h4>Conheça a igreja</h4>
                <p class="muted">Parte da {{ optional($congregacao->denominacao)->nome ?? 'nossa denominação' }}, caminhamos para formar discípulos que amam a Deus e servem a cidade.</p>
                <div class="contact-row">
                    <a class="chip" href="{{ $contactLink }}">Quero me apresentar</a>
                    @if($congregacao->endereco)
                        <a class="chip" href="https://www.google.com/maps/search/{{ urlencode($congregacao->endereco) }}" target="_blank" rel="noopener">Como chegar</a>
                    @endif
                </div>
            </section>

            <div class="footer">
                {{ $appName }} • {{ optional($congregacao->cidade)->nome ?? '' }} {{ optional($congregacao->estado)->sigla ?? '' }}
            </div>
        </div>
    </div>
</body>
</html>
