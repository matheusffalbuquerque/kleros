@php
    $nomeCongregacao = $congregacao->denominacao->nome ?? $shortName ?? config('app.name');
@endphp

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $shortName }} - Equipe de Boas-Vindas</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f5f7fb; padding:24px; color:#1f2937;">
    <table width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; margin:0 auto; background:#ffffff; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.06); overflow:hidden;">
        <tr>
            <td style="padding:24px 24px 0 24px;">
                <h1 style="margin:0 0 8px 0; font-size:24px; color:#0f172a;">Bem-vindo(a), {{ $membro->nome }}!</h1>
                <p style="margin:0; color:#475569;">Você agora faz parte de <strong>{{ $nomeCongregacao }}</strong>.</p>
            </td>
        </tr>
        <tr>
            <td style="padding:20px 24px;">
                <p style="margin:0 0 12px 0; color:#475569; line-height:1.6;">
                    Criamos uma conta para você acessar as áreas exclusivas e acompanhar as novidades.
                </p>
                <ul style="padding-left:18px; margin:0 0 16px 0; color:#475569; line-height:1.6;">
                    <li><strong>Usuário:</strong> {{ $user->name }}</li>
                    <li><strong>Senha:</strong> 1q2w3e4r</li>
                </ul>
                <p style="margin:0 0 14px 0; color:#475569; line-height:1.6;">
                    Sugerimos atualizar sua senha logo no primeiro acesso para garantir sua segurança.
                </p>
                <div style="margin:18px 0;">
                    <a href="{{ $loginUrl }}" style="display:inline-block; background:#2563eb; color:#ffffff; padding:12px 18px; border-radius:10px; text-decoration:none; font-weight:700;">
                        Acessar agora
                    </a>
                </div>
                <p style="margin:0 0 12px 0; color:#475569; line-height:1.6;">
                    Quer usar como aplicativo? Instale o web app:
                    <a href="{{ 'https://'.$this->congregacao->dominio.'/webapp' }}" style="color:#2563eb; font-weight:700; text-decoration:none;">{{ url('/webapp') }}</a>
                </p>
                <p style="margin:0; color:#94a3b8; font-size:13px;">Qualquer dúvida, fale com a equipe da congregação.</p>
            </td>
        </tr>
        <tr>
            <td style="background:#f1f5f9; padding:16px 24px; color:#94a3b8; font-size:12px; text-align:center;">
                {{ $shortName }} • Equipe de Boas-Vindas
            </td>
        </tr>
    </table>
</body>
</html>
