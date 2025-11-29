<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{$congregacao->nome_curto}}</title>
    <link rel="shortcut icon" href="/storage/{{$congregacao->config->logo_caminho}}" type="image/x-icon">
    <!-- SCSS managed by Vite -->
    @vite(['resources/css/app.scss'])
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Teko" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Saira" rel="stylesheet">

    <style>
        /* CSS dinâmico injetado aqui */
        :root {
            
            --primary-color: {{$congregacao->config->conjunto_cores['primaria'] ?? '#677b96'}};
            --secondary-color: {{$congregacao->config->conjunto_cores['secundaria'] ?? '#0a1929'}};
            --terciary-color: {{$congregacao->config->conjunto_cores['terciaria'] ?? '#f44916'}};
            --primary-contrast: {{ getContrastTextColor($congregacao->config->conjunto_cores['primaria'])}};
            --secondary-contrast: {{ getContrastTextColor($congregacao->config->conjunto_cores['secundaria'])}};
            --terciary-contrast: {{ getContrastTextColor($congregacao->config->conjunto_cores['terciaria'])}};
            --text-font: {{$congregacao->config->font_family}};

            --background-color: {{$congregacao->config->tema->propriedades['cor-fundo']}};
            --text-color: {{$congregacao->config->tema->propriedades['cor-texto']}};
            --border-style: {{$congregacao->config->tema->propriedades['borda']}}
        }
        </style>
</head>

<body id="login" style="background-image: url(/storage/{{$congregacao->config->banner_caminho}})">
    <div class="login-container">
        <img src="/storage/{{$congregacao->config->logo_caminho}}" alt="{{$congregacao->denominacao->nome}}" class="logo">
        <h2>Login</h2>
        <div class="login-form">
            <form action="/login" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Usuário:</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Senha:</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Entrar</button>
                </div>
                <div class="form-group">
                    @if ($errors->has('user'))
                        <div class="alert-error">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span>{{ $errors->first('user') }}</span>
                        </div>
                    @endif
                </div>
                <div class="form-group center" style="margin-top: 20px;">
                    <a href="{{ route('password.request') }}" class="link-standard">Esqueci a senha</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="login-footer">
        Ecossistema Kleros - Tecnologia a serviço do Reino
    </div>
</body>

</html>
