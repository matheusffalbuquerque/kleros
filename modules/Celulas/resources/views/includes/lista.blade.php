@php
    use Illuminate\Support\Carbon;

    $isPaginator = $celulas instanceof \Illuminate\Contracts\Pagination\Paginator;
    $lista = $isPaginator
        ? collect($celulas->items())
        : collect($celulas);
@endphp

@if ($lista->isNotEmpty())
    @foreach ($lista as $item)
        @php
            $corBorda = $item->cor_borda ?? '#ffffff';
        @endphp
        <div class="list-item" onclick="abrirJanelaModal('{{ route('celulas.form_editar', $item->id) }}')" style="border-left: 4px solid {{ $corBorda }};">
            <div class="item item-1">
                <p><i class="bi bi-house"></i> {{ $item->identificacao }}</p>
            </div>
            <div class="item item-15">
                <p>
                    {{ optional($item->lider)->nome ?? '—' }}
                    @if ($item->colider)
                        {{ ' / ' . $item->colider->nome }}
                    @endif
                </p>
            </div>
            <div class="item item-1">
                <p>{{ optional($item->anfitriao)->nome ?? '—' }}</p>
            </div>
            <div class="item item-1">
                <p>
                    {{ collect([$item->endereco, $item->numero, $item->bairro])->filter()->implode(', ') ?: '—' }}
                </p>
            </div>
        </div>
    @endforeach
@else
    <div class="card">
        <p><i class="bi bi-exclamation-triangle"></i> Nenhuma célula encontrada para os filtros informados.</p>
    </div>
@endif

@if ($isPaginator && method_exists($celulas, 'hasPages') && $celulas->hasPages())
    <div class="pagination">
        {{ $celulas->links('pagination::default') }}
    </div>
@endif
