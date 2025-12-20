@extends('layouts.main')

@section('title', ($congregacao->nome_curto ?? 'Congregação') . ' | ' . $appName)

@section('content')
<div class="container">
    <div class="nao-imprimir">
        <h1>Painel de Batismos</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="info">
            @php
                $pendentesPorMembro = collect($agendamentosPendentesPorMembro ?? []);
            @endphp
            <h3>Filtrar membros por status</h3>
            <form method="GET" action="{{ route('batismo.painel') }}">
                <div class="search-panel">
                    <div class="search-panel-item">
                        <label for="status">Status:</label>
                        <select id="status" name="status">
                            <option value="todos" @selected($statusFilter === 'todos')>Todos</option>
                            <option value="batizados" @selected($statusFilter === 'batizados')>Batizados</option>
                            <option value="nao_batizados" @selected($statusFilter === 'nao_batizados')>Não Batizados</option>
                            <option value="em_preparacao" @selected($statusFilter === 'em_preparacao')>Em preparação</option>
                        </select>
                        <button type="submit"><i class="bi bi-search"></i> Filtrar</button>
                        <button id="btnAgendarBatismo" type="button" onclick="abrirAgendamentoBatismo()">
                            <i class="bi bi-calendar-plus"></i> Agendar batismo
                        </button>
                    </div>
                    <div class="search-panel-item">
                        @if($proximoBatismo)
                            <div class="card card-compact">
                                <p><strong>Próximo batismo:</strong> {{ optional($proximoBatismo->data_batismo)->format('d/m/Y') ?? 'Sem data' }}</p>
                            </div>
                        @else
                            <div class="card card-compact">
                                <p><strong>Próximo batismo:</strong> Não há agendamentos.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="list">
        <div class="list-title">
            <div class="item-2"><b>Nome</b></div>
            <div class="item-1"><b>Telefone</b></div>
            <div class="item-1"><b>Data do Batismo</b></div>
            <div class="item-1"><b>Status</b></div>
        </div>
        <div id="content">
            @forelse ($membros as $item)
                <a href="{{ url('/membros/exibir/' . $item->id) }}">
                    <div class="list-item">
                        <div class="item item-2">
                            <p style="display:flex; align-items:center; gap:.5em">
                                <img src="{{ $item->foto ? asset('storage/' . $item->foto) : asset('storage/images/newuser.png') }}" class="avatar" alt="Avatar">
                                {{ $item->nome }}
                            </p>
                        </div>
                        <div class="item item-1">
                            <p>{{ $item->telefone ?? 'Não informado' }}</p>
                        </div>
                        @php
                            $agendamentoPendente = $pendentesPorMembro[$item->id] ?? null;
                            $dataBatismoExibida = $agendamentoPendente?->data_batismo ?? $item->data_batismo;
                            $statusBadge = 'batismo-badge batismo-nao';
                            $statusLabel = $item->batizado ? 'Batizado' : 'Não batizado';

                            if ($agendamentoPendente) {
                                $statusBadge = 'batismo-badge batismo-pre';
                                $statusLabel = 'Em preparação';
                            } elseif ($item->batizado) {
                                $statusBadge = 'batismo-badge batismo-sim';
                                $statusLabel = 'Batizado';
                            }
                        @endphp
                        <div class="item item-1">
                            <p>{{ $dataBatismoExibida ? $dataBatismoExibida->format('d/m/Y') : '—' }}</p>
                        </div>
                        <div class="item item-1">
                            <span class="tag {{ $statusBadge }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="card">
                    <p>Nenhum membro encontrado para o filtro aplicado.</p>
                </div>
            @endforelse

            @if ($membros->hasPages())
                <div class="pagination">
                    {{ $membros->links('pagination::default') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .batismo-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 0.75rem;
        letter-spacing: 0.01em;
        border: 1px solid rgba(0, 0, 0, 0.14);
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12);
    }
    .batismo-badge.batismo-sim {
        background: linear-gradient(135deg, rgba(196, 222, 255, 0.52), rgba(171, 209, 255, 0.42));
        color: rgba(32, 73, 118, 0.95);
        border-color: rgba(126, 176, 230, 0.5);
    }
    .batismo-badge.batismo-nao {
        background: linear-gradient(135deg, rgba(99, 179, 237, 0.32), rgba(56, 149, 237, 0.26));
        color: rgba(15, 68, 116, 0.98);
        border-color: rgba(43, 132, 212, 0.45);
    }
    .batismo-badge.batismo-pre {
        background: linear-gradient(135deg, rgba(148, 201, 255, 0.4), rgba(113, 184, 255, 0.36));
        color: rgba(27, 86, 141, 0.97);
        border-color: rgba(84, 164, 232, 0.5);
    }
</style>
@endpush

@push('scripts')
@if(Route::has('batismo.agendar.modal'))
<script>
    function abrirAgendamentoBatismo(preset = {}) {
        const modalRoute = '{{ route('batismo.agendar.modal') }}';
        const statusSelect = document.getElementById('status');
        const defaultStatus = @json($statusFilter);

        const params = new URLSearchParams();
        const statusValue = preset.status ?? (statusSelect ? statusSelect.value : null) ?? defaultStatus;
        params.set('status', statusValue || 'todos');

        if (Array.isArray(preset.membros)) {
            preset.membros
                .filter(value => value !== undefined && value !== null && value !== '')
                .forEach(value => params.append('membros[]', value));
        } else if (preset.membro_id) {
            params.append('membros[]', preset.membro_id);
        }

        if (preset.data_batismo) {
            params.set('data_batismo', preset.data_batismo);
        }

        abrirJanelaModal(`${modalRoute}?${params.toString()}`);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const shouldReopen = @json(old('_source') === 'batismo_agendar');
        if (shouldReopen) {
            abrirAgendamentoBatismo({
                status: @json(old('status', $statusFilter)),
                membros: @json(old('membros', [])),
                data_batismo: @json(old('data_batismo')),
            });
        }
    });
</script>
@endif
@endpush
