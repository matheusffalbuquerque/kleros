<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Visitantes</title>
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
            max-height: 110px;
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
            margin-bottom: 18px;
        }

        .summary td {
            width: 25%;
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
            margin: -8px 0 12px;
            font-size: 10px;
            color: #6b7280;
        }

        .visitor-date-group {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .visitor-date-heading {
            margin: 0 0 6px;
            font-size: 14px;
            color: #111827;
        }

        .visitor-card {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 4px 5px;
            margin-bottom: 5px;
            page-break-inside: avoid;
        }

        .visitor-header {
            margin-bottom: 3px;
        }

        .visitor-header h3 {
            margin: 0 0 1px;
            font-size: 13px;
            line-height: 1.2;
        }

        .visitor-header p {
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
            <h1>Relatório de Visitantes</h1>
            <p><strong>Congregação:</strong> {{ $congregacao->nome ?? $congregacao->nome_curto ?? 'Congregação' }}</p>
            <p><strong>Período:</strong> {{ $periodo }}</p>
            @if (!empty($nomeFiltro))
                <p><strong>Filtro por nome:</strong> {{ $nomeFiltro }}</p>
            @endif
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
            <td><strong>Total de registros</strong><span>{{ $resumo['total_registros'] }}</span></td>
            <td><strong>Visitantes únicos</strong><span>{{ $resumo['visitantes_unicos'] }}</span></td>
            <td><strong>Novos membros</strong><span>{{ $resumo['tornaram_membros'] }}</span></td>
            <td><strong>* Média de visitas</strong><span>{{ $resumo['media_visitas'] }}</span></td>
        </tr>
    </table>

    <p class="summary-note">* Média por registro filtrado</p>

    @php
        $visitantesPorData = $visitantes->groupBy(function ($visitante) {
            return $visitante->data_visita
                ? \Illuminate\Support\Carbon::parse($visitante->data_visita)->format('Y-m-d')
                : 'sem-data';
        });
    @endphp

    @forelse ($visitantesPorData as $data => $visitantesDoDia)
        <div class="visitor-date-group">
            <h2 class="visitor-date-heading">
                {{ $data === 'sem-data' ? 'Data não informada' : \Illuminate\Support\Carbon::parse($data)->format('d/m/Y') }}
            </h2>

            @foreach ($visitantesDoDia as $visitante)
                <section class="visitor-card">
                    <div class="visitor-header">
                        <h3>{{ $visitante->nome }}</h3>
                    </div>

                    <table class="grid">
                        <tr>
                            <td>
                                <span class="label">Telefone</span>
                                {{ $visitante->telefone ?: 'Não informado' }}
                            </td>
                            <td>
                                <span class="label">Situação</span>
                                {{ $visitante->status_label }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="stats-inline">
                                    <strong>Total de visitas:</strong> {{ $visitante->total_visitas_label }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <span class="label">Observações</span>
                                {{ $visitante->observacoes ?: 'Nenhuma observação registrada.' }}
                            </td>
                        </tr>
                    </table>
                </section>
            @endforeach
        </div>
    @empty
        <div class="empty">Nenhum visitante foi encontrado para os filtros selecionados.</div>
    @endforelse

    <div class="footer">Relatório gerado automaticamente pelo Kleros.</div>
</body>
</html>
