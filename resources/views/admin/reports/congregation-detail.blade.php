<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo ?? 'Relatório da Congregação' }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1e40af;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 10px;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .info-section h2 {
            font-size: 16px;
            color: #1e40af;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        .info-item {
            padding: 8px;
            background-color: #f8fafc;
            border-left: 4px solid #2563eb;
        }
        .info-item strong {
            display: block;
            color: #374151;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .info-item span {
            color: #1f2937;
            font-size: 12px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: #f3f4f6;
            padding: 12px;
            text-align: center;
            border-radius: 4px;
        }
        .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            display: block;
        }
        .stat-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background-color: #1e40af;
            color: white;
            font-weight: bold;
        }
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo ?? 'Relatório da Congregação' }}</h1>
        <p>Emitido em: {{ $dataEmissao ?? now()->format('d/m/Y H:i') }}</p>
    </div>

    <main>
        @if(isset($congregacaoReport) && $congregacaoReport)
        <!-- Informações Básicas da Congregação -->
        <div class="info-section">
            <h2>Informações Gerais</h2>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Identificação</strong>
                    <span>{{ $congregacaoReport->identificacao ?? 'Não informado' }}</span>
                </div>
                <div class="info-item">
                    <strong>Status</strong>
                    <span>{{ ($congregacaoReport->ativa ?? false) ? 'Ativa' : 'Inativa' }}</span>
                </div>
                <div class="info-item">
                    <strong>Cidade</strong>
                    <span>{{ isset($congregacaoReport->cidade) && $congregacaoReport->cidade ? $congregacaoReport->cidade->nome : 'Não informado' }}</span>
                </div>
                <div class="info-item">
                    <strong>Estado</strong>
                    <span>
                        {{ isset($congregacaoReport->estado) && $congregacaoReport->estado ? $congregacaoReport->estado->nome : 'Não informado' }}
                        {{ isset($congregacaoReport->estado) && $congregacaoReport->estado && $congregacaoReport->estado->uf ? '(' . $congregacaoReport->estado->uf . ')' : '' }}
                    </span>
                </div>
                <div class="info-item">
                    <strong>Domínio</strong>
                    <span>{{ isset($congregacaoReport->dominio) && $congregacaoReport->dominio ? $congregacaoReport->dominio->dominio : 'Não configurado' }}</span>
                </div>
                <div class="info-item">
                    <strong>Última Atividade</strong>
                    <span>{{ $estatisticas['ultima_atividade'] ? $estatisticas['ultima_atividade']->format('d/m/Y') : 'Nenhuma registrada' }}</span>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="info-section">
            <h2>Estatísticas</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number">{{ $estatisticas['total_membros'] }}</span>
                    <div class="stat-label">Total de Membros</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ $estatisticas['membros_ativos'] }}</span>
                    <div class="stat-label">Membros Ativos</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ $estatisticas['cultos_ano'] }}</span>
                    <div class="stat-label">Cultos em {{ now()->year }}</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ number_format($estatisticas['frequencia_media'], 0) }}</span>
                    <div class="stat-label">Frequência Média</div>
                </div>
            </div>
        </div>

        @if($congregacaoReport->membros->count() > 0)
        <!-- Lista de Membros -->
        <div class="info-section">
            <h2>Lista de Membros</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($congregacaoReport->membros->sortBy('nome') as $membro)
                        <tr>
                            <td>{{ $membro->nome ?? 'Nome não informado' }}</td>
                            <td>{{ $membro->email ?? '—' }}</td>
                            <td>{{ $membro->telefone ?? '—' }}</td>
                            <td>{{ $membro->ativo ? 'Ativo' : 'Inativo' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($congregacaoReport->cultos->count() > 0)
        <!-- Histórico de Cultos (últimos 10) -->
        <div class="info-section page-break">
            <h2>Histórico Recente de Cultos</h2>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Evento</th>
                        <th>Preletor</th>
                        <th>Tema</th>
                        <th>Frequência Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($congregacaoReport->cultos->sortByDesc('data_culto')->take(10) as $culto)
                        <tr>
                            <td>{{ $culto->data_culto->format('d/m/Y') }}</td>
                            <td>{{ $culto->evento->nome ?? '—' }}</td>
                            <td>{{ $culto->preletor ?? '—' }}</td>
                            <td>{{ Str::limit($culto->tema_sermao ?? '—', 30) }}</td>
                            <td>
                                @if($culto->quant_adultos || $culto->quant_criancas || $culto->quant_visitantes)
                                    {{ ($culto->quant_adultos ?? 0) + ($culto->quant_criancas ?? 0) + ($culto->quant_visitantes ?? 0) }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        @else
        <div class="info-section">
            <h2>Erro</h2>
            <p>Congregação não encontrada ou dados inválidos.</p>
        </div>
        @endif
    </main>

    <div class="footer">
        <p>Administração Kleros &copy; {{ date('Y') }} | Relatório gerado automaticamente</p>
    </div>
</body>
</html>