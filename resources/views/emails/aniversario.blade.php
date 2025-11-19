<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Feliz aniversário</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color:#111;">
    <h1 style="color:#0a1929;">Feliz aniversário, {{ $membro->nome }}!</h1>
    <p style="font-size: 16px; line-height: 1.5;">{!! nl2br(e($mensagem)) !!}</p>
    <p style="margin-top: 2rem; font-size: 14px;">Com carinho,<br>{{ optional($membro->congregacao)->nome ?? config('app.name') }}</p>
</body>
</html>
