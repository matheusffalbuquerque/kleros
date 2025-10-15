<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <p>Emitido em: {{ $dataEmissao }}</p>
    </div>

    <main>
        <table>
            <thead>
                <tr>
                    <th>Congregação</th>
                    <th>Localização</th>
                    <th>Domínio</th>
                    <th>Membros</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($congregacoes as $congregacao)
                    <tr>
                        <td>{{ $congregacao->identificacao ?? '—' }}</td>
                        <td>
                            {{ $congregacao->cidade->nome ?? '—' }}, {{ $congregacao->estado->uf ?? '' }}
                        </td>
                        <td>{{ $congregacao->dominio->dominio ?? 'N/A' }}</td>
                        <td>{{ $congregacao->membros_count ?? 0 }}</td>
                        <td>{{ $congregacao->ativa ? 'Ativa' : 'Inativa' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">Nenhuma congregação encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>

    <div class="footer">
        Administração Kleros &copy; {{ date('Y') }}
    </div>
</body>
</html>
