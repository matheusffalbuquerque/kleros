<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ __('congregations.emails.gestor_welcome.subject') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f5f5f7;font-family:'Segoe UI',Roboto,Arial,sans-serif;color:#202124;">
@php
    $congregacao = $congregacao ?? null;
    $gestor = $gestor ?? null;
    $membro = $membro ?? null;

    $gestorNome = $membro->nome ?? $gestor->name ?? 'Gestor';
    $congregacaoNome = optional($congregacao)->identificacao ?? '';

    $normalizeDigits = static fn (?string $value): string => preg_replace('/\D+/', '', (string) $value);
    $formatCpf = static function (?string $value) use ($normalizeDigits): ?string {
        $digits = $normalizeDigits($value);
        if (strlen($digits) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digits);
        }
        return $value;
    };
    $formatPhone = static function (?string $value) use ($normalizeDigits): ?string {
        $digits = $normalizeDigits($value);
        if (strlen($digits) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $digits);
        }
        if (strlen($digits) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $digits);
        }
        return $value;
    };

    $gestorTelefone = $formatPhone($membro->telefone ?? optional($gestor?->membro)->telefone ?? null);
    $gestorCpf = $formatCpf($membro->cpf ?? optional($gestor?->membro)->cpf ?? null);
    $gestorNascimento = null;
    if ($membro && $membro->data_nascimento) {
        try {
            $gestorNascimento = \Carbon\Carbon::parse($membro->data_nascimento)->format('d/m/Y');
        } catch (\Throwable $e) {
            $gestorNascimento = $membro->data_nascimento;
        }
    }

    $congregacaoTelefone = $formatPhone(optional($congregacao)->telefone);
    $congregacaoEmail = optional($congregacao)->email;
    $congregacaoDominio = optional(optional($congregacao)->dominio)->dominio;

    $enderecoPartes = array_filter([
        optional($congregacao)->endereco,
        optional($congregacao)->numero,
        optional($congregacao)->complemento,
        optional($congregacao)->bairro,
    ]);
    $enderecoLinha = implode(', ', $enderecoPartes);
    $cidadeUf = trim(implode(' / ', array_filter([
        optional(optional($congregacao)->cidade)->nome,
        optional(optional($congregacao)->estado)->uf,
    ])));
    $denominacaoNome = optional(optional($congregacao)->denominacao)->nome;
@endphp
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f7;padding:32px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e2e5ea;">
                <tr>
                    <td style="padding:32px;">
                        <h1 style="margin:0;font-size:24px;font-weight:600;color:#1a1f36;">
                            {{ __('congregations.emails.gestor_welcome.greeting', ['nome' => $gestorNome]) }}
                        </h1>
                        <p style="margin:16px 0 24px;font-size:15px;line-height:1.6;color:#4d515a;">
                            {{ __('congregations.emails.gestor_welcome.intro', ['congregacao' => $congregacaoNome]) }}
                        </p>

                        <h2 style="margin:24px 0 12px;font-size:16px;font-weight:600;color:#1a1f36;text-transform:uppercase;letter-spacing:0.08em;">
                            {{ __('congregations.emails.gestor_welcome.congregacao_title') }}
                        </h2>
                        <ul style="margin:0 0 16px;padding-left:20px;font-size:14px;color:#4d515a;line-height:1.6;">
                            <li>{{ __('congregations.emails.gestor_welcome.items.identificacao', ['valor' => $congregacaoNome]) }}</li>
                            @if($congregacaoTelefone)
                                <li>{{ __('congregations.emails.gestor_welcome.items.telefone', ['valor' => $congregacaoTelefone]) }}</li>
                            @endif
                            @if($congregacaoEmail)
                                <li>{{ __('congregations.emails.gestor_welcome.items.email', ['valor' => $congregacaoEmail]) }}</li>
                            @endif
                            @if($congregacaoDominio)
                                <li>{{ __('congregations.emails.gestor_welcome.items.dominio', ['valor' => $congregacaoDominio]) }}</li>
                            @endif
                            @if($enderecoLinha)
                                <li>{{ __('congregations.emails.gestor_welcome.items.endereco', ['valor' => $enderecoLinha]) }}</li>
                            @endif
                            @if($cidadeUf)
                                <li>{{ __('congregations.emails.gestor_welcome.items.cidade', ['valor' => $cidadeUf]) }}</li>
                            @endif
                            @if($denominacaoNome)
                                <li>{{ __('congregations.emails.gestor_welcome.items.denominacao', ['valor' => $denominacaoNome]) }}</li>
                            @endif
                        </ul>

                        <h2 style="margin:24px 0 12px;font-size:16px;font-weight:600;color:#1a1f36;text-transform:uppercase;letter-spacing:0.08em;">
                            {{ __('congregations.emails.gestor_welcome.gestor_title') }}
                        </h2>
                        <ul style="margin:0 0 16px;padding-left:20px;font-size:14px;color:#4d515a;line-height:1.6;">
                            <li>{{ __('congregations.emails.gestor_welcome.items.gestor_nome', ['valor' => $gestorNome]) }}</li>
                            @if($gestorTelefone)
                                <li>{{ __('congregations.emails.gestor_welcome.items.gestor_telefone', ['valor' => $gestorTelefone]) }}</li>
                            @endif
                            @if($gestorCpf)
                                <li>{{ __('congregations.emails.gestor_welcome.items.gestor_cpf', ['valor' => $gestorCpf]) }}</li>
                            @endif
                            @if($gestorNascimento)
                                <li>{{ __('congregations.emails.gestor_welcome.items.gestor_data_nascimento', ['valor' => $gestorNascimento]) }}</li>
                            @endif
                        </ul>

                        <h2 style="margin:24px 0 12px;font-size:16px;font-weight:600;color:#1a1f36;text-transform:uppercase;letter-spacing:0.08em;">
                            {{ __('congregations.emails.gestor_welcome.login_title') }}
                        </h2>
                        <ul style="margin:0 0 24px;padding-left:20px;font-size:14px;color:#4d515a;line-height:1.6;">
                            <li>{{ __('congregations.emails.gestor_welcome.items.login_email', ['valor' => $gestor->email]) }}</li>
                            <li>{{ __('congregations.emails.gestor_welcome.items.login_senha', ['valor' => $senhaTemporaria]) }}</li>
                        </ul>

                        <div style="text-align:center;margin:32px 0;">
                            <a href="{{ $loginUrl }}" style="display:inline-block;padding:12px 28px;border-radius:9999px;background-color:#6449a2;color:#ffffff;font-size:14px;font-weight:600;text-decoration:none;">
                                {{ __('congregations.emails.gestor_welcome.cta') }}
                            </a>
                        </div>

                        <p style="margin:0;font-size:13px;line-height:1.6;color:#6b7280;text-align:center;">
                            {{ __('congregations.emails.gestor_welcome.footer') }}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
