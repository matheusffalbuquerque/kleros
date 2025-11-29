@php
    $step = $step ?? 'email';
    $congregacaoNome = $congregacao->nome_curto ?? config('app.name', 'Kleros');
    $tituloPagina = [
        'email' => 'Recuperar senha',
        'code' => 'Confirmar código',
        'reset' => 'Definir nova senha',
    ][$step] ?? 'Recuperar senha';
@endphp

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tituloPagina }} - {{ $congregacaoNome }}</title>
    <link rel="shortcut icon" href="/storage/{{$congregacao->config->logo_caminho}}" type="image/x-icon">
    @vite(['resources/css/app.scss'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Teko&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: {{ $congregacao->config->conjunto_cores['primaria'] ?? '#677b96' }};
            --secondary-color: {{ $congregacao->config->conjunto_cores['secundaria'] ?? '#0a1929' }};
            --terciary-color: {{ $congregacao->config->conjunto_cores['terciaria'] ?? '#f44916' }};
            --primary-contrast: {{ getContrastTextColor($congregacao->config->conjunto_cores['primaria']) }};
            --secondary-contrast: {{ getContrastTextColor($congregacao->config->conjunto_cores['secundaria']) }};
            --terciary-contrast: {{ getContrastTextColor($congregacao->config->conjunto_cores['terciaria']) }};
            --text-font: {{ $congregacao->config->font_family }};

            --background-color: {{ $congregacao->config->tema->propriedades['cor-fundo'] ?? '#0f172a' }};
            --text-color: {{ $congregacao->config->tema->propriedades['cor-texto'] ?? '#e2e8f0' }};
            --border-style: {{ $congregacao->config->tema->propriedades['borda'] ?? '1px solid rgba(255, 255, 255, 0.1)' }};
        }
    </style>
</head>
<body id="login" style="background-image: url(/storage/{{$congregacao->config->banner_caminho}});">
    <div class="login-container">
        <img src="/storage/{{$congregacao->config->logo_caminho}}" alt="{{ $congregacao->denominacao->nome }}" class="logo">
        <h2>{{ $tituloPagina }}</h2>
        <p class="login-hint">
            @switch($step)
                @case('code')
                    @php
                        $emailPara = session('password_reset_email');
                        $emailMascarado = $emailPara
                            ? \Illuminate\Support\Str::mask($emailPara, '*', 3, 100)
                            : null;
                    @endphp
                    Informe o código de 6 dígitos enviado para {{ $emailMascarado ?? 'o seu e-mail cadastrado' }}.
                    @break
                @case('reset')
                    Defina uma nova senha segura para acessar o painel novamente.
                    @break
                @default
                    Digite o e-mail cadastrado para receber um código de verificação.
            @endswitch
        </p>

        @if (session('status'))
            <div class="alert alert-success center">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger center">{{ $errors->first() }}</div>
        @endif

        <div class="login-form">
            @if ($step === 'code')
                <form method="POST" action="{{ route('password.verify') }}">
                    @csrf
                    <div class="form-group">
                        <label for="code">Código de verificação</label>
                        <input type="text" name="code" id="code" maxlength="6" inputmode="numeric" required autofocus>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Confirmar código</button>
                    </div>
                </form>
                <div class="form-group center">
                    <form method="POST" action="{{ route('password.sendCode') }}" class="inline-form">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('password_reset_email') }}">
                        <button type="submit" class="link-standard">Reenviar código</button>
                    </form>
                </div>
            @elseif ($step === 'reset')
                <form method="POST" action="{{ route('password.reset') }}">
                    @csrf
                    <div class="form-group">
                        <label for="password">Nova senha</label>
                        <input type="password" name="password" id="password" minlength="6" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirmar nova senha</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" minlength="6" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Salvar nova senha</button>
                    </div>
                </form>
            @else
                <form method="POST" action="{{ route('password.sendCode') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">E-mail cadastrado</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Enviar código</button>
                    </div>
                </form>
            @endif
        </div>

        <div class="form-group center" style="margin-top: 20px;">
            @if ($step === 'email')
                <a href="{{ route('login') }}" class="link-standard">Voltar para o login</a>
            @else
                <a href="{{ route('password.request') }}" class="link-standard">Começar novamente</a>
            @endif
        </div>
    </div>
</body>
</html>
