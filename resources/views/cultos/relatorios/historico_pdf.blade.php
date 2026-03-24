<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Cultos</title>
    <style>
        @page {
            margin: 24mm 18mm 22mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 12px;
            line-height: 1.45;
            margin: 0;
        }

        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #111827;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .header-copy,
        .header-logo {
            display: table-cell;
            vertical-align: top;
        }

        .header-copy {
            width: 76%;
            padding-right: 16px;
        }

        .header-logo {
            width: 24%;
            text-align: right;
        }

        .header-logo img {
            max-width: 120px;
            max-height: 72px;
            object-fit: contain;
        }

        .header h1 {
            margin: 0 0 6px;
            font-size: 24px;
        }

        .header p {
            margin: 2px 0;
            color: #4b5563;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .summary td {
            width: 20%;
            border: 1px solid #d1d5db;
            padding: 10px;
            vertical-align: top;
        }

        .summary strong {
            display: block;
            margin-bottom: 4px;
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
        }

        .summary span {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }

        .summary-note {
            margin: -10px 0 12px;
            font-size: 10px;
            color: #6b7280;
        }

        .culto-card {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 4px 5px;
            margin-bottom: 5px;
            page-break-inside: avoid;
        }

        .culto-header {
            margin-bottom: 3px;
        }

        .culto-header h2 {
            margin: 0 0 1px;
            font-size: 13px;
            line-height: 1.2;
        }

        .culto-header p {
            margin: 0;
            color: #4b5563;
            font-size: 11px;
            line-height: 1.2;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }

        .grid td {
            width: 50%;
            padding: 2px 4px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            font-size: 11px;
            line-height: 1.2;
        }

        .label {
            display: block;
            margin-bottom: 0;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            line-height: 1.1;
        }

        .stats-inline {
            margin-top: 2px;
            font-size: 10px;
            color: #374151;
        }

        .stats-inline strong {
            color: #111827;
        }

        .empty {
            padding: 18px;
            border: 1px solid #d1d5db;
            color: #4b5563;
        }

        .footer {
            margin-top: 16px;
            font-size: 10px;
            color: #6b7280;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-copy">
            <h1>Relatório de Cultos</h1>
            <p><strong>Congregação:</strong> {{ $congregacao->nome ?? $congregacao->nome_curto ?? 'Congregação' }}</p>
            <p><strong>Período:</strong> {{ $periodo }}</p>
            <p><strong>Gerado em:</strong> {{ $geradoEm->format('d/m/Y H:i') }}</p>
        </div>
        <div class="header-logo">
            @if (!empty($logoDataUri))
                <img src="{{ $logoDataUri }}" alt="Logo da congregação">
            @endif
        </div>
    </div>

    <table class="summary">
        <tr>
            <td><strong>Total de cultos</strong><span>{{ $resumo['total_cultos'] }}</span></td>
            <td><strong>* Adultos</strong><span>{{ $resumo['adultos_media'] }}</span></td>
            <td><strong>* Crianças</strong><span>{{ $resumo['criancas_media'] }}</span></td>
            <td><strong>* Visitantes</strong><span>{{ $resumo['visitantes_media'] }}</span></td>
            <td><strong>* Público total</strong><span>{{ $resumo['publico_total_media'] }}</span></td>
        </tr>
    </table>

    <p class="summary-note">* Média por culto</p>

    @forelse ($cultos as $culto)
        <section class="culto-card">
            <div class="culto-header">
                <h2>{{ $culto->tema_sermao ?: 'Culto sem tema informado' }}</h2>
                <p>{{ \Carbon\Carbon::parse($culto->data_culto)->format('d/m/Y H:i') }}</p>
            </div>

            <table class="grid">
                <tr>
                    <td>
                        <span class="label">Preletor</span>
                        {{ $culto->preletor_label }}
                    </td>
                    <td>
                        <span class="label">Categoria</span>
                        {{ $culto->categoria_label }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Evento associado</span>
                        {{ $culto->evento_label }}
                    </td>
                    <td>
                        <span class="label">Texto-base</span>
                        {{ $culto->texto_base ?: 'Não informado' }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="stats-inline">
                            <strong>Adultos:</strong> {{ $culto->quant_adultos ?? 0 }}
                            | <strong>Crianças:</strong> {{ $culto->quant_criancas ?? 0 }}
                            | <strong>Visitantes:</strong> {{ $culto->quant_visitantes ?? 0 }}
                            | <strong>Público total:</strong> {{ $culto->publico_total }}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <span class="label">Observações</span>
                        {{ $culto->observacoes ?: 'Nenhuma observação registrada.' }}
                    </td>
                </tr>
            </table>
        </section>
    @empty
        <div class="empty">
            Nenhum culto foi encontrado para os filtros selecionados.
        </div>
    @endforelse

    <div class="footer">
        Relatório gerado automaticamente pelo sistema Kleros.
    </div>
</body>
</html>
