@extends('layouts.site')

@section('title', 'Gestão de Extensões — Kleros')

@section('content')
<div class="min-h-screen bg-[#1a1821] text-[#f4f3f6] font-[Segoe_UI,Roboto,system-ui,-apple-system,Arial,sans-serif]">
    <header class="sticky top-0 z-40 bg-[#1a1821]/95 border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/kleros-logo.svg') }}" alt="Kleros" class="h-8 w-auto">
                    <div class="leading-tight">
                        <span class="font-semibold text-lg">Kleros</span>
                        <span class="block text-xs text-white/60">Administração</span>
                    </div>
                </a>
                <span class="hidden sm:block text-white/40 text-sm">/ Gestão de Extensões</span>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-white/20 hover:border-white/40 text-white/70">
                    <span class="text-xs uppercase tracking-[0.2em]">Painel</span>
                </a>
                <a href="{{ route('logout') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-white/20 hover:border-rose-300/60 text-rose-200/80">
                    <span class="text-xs uppercase tracking-[0.2em]">Sair</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-12 space-y-10">
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-semibold">Gestão de Extensões</h1>
                <p class="mt-3 text-white/70 max-w-2xl leading-relaxed">
                    Pesquise, edite e sincronize as extensões disponíveis no catálogo administrativo. Utilize a busca para localizar módulos específicos e mantenha as informações alinhadas com os manifestos do servidor.
                </p>
            </div>
            <form action="{{ route('admin.extensions.sync') }}" method="POST" class="flex items-center gap-3">
                @csrf
                <label class="inline-flex items-center gap-2 text-sm text-white/70">
                    <input type="checkbox" name="atualizar" value="1" checked class="rounded border-white/30 bg-[#1a1821] text-[#6449a2] focus:ring-[#6449a2]">
                    Atualizar dados existentes
                </label>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#6449a2] hover:bg-[#584091] px-4 py-2 text-sm font-medium shadow-lg shadow-[#6449a2]/30 transition">
                    <i class="bi bi-arrow-repeat"></i> Sincronizar módulos
                </button>
            </form>
        </section>

        @if (session('msg'))
            <div class="rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
                {{ session('msg') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-sm text-rose-100 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @php
            $statusColors = [
                'disponivel' => 'border-emerald-400/40 text-emerald-200 bg-emerald-400/10',
                'indisponivel' => 'border-rose-400/40 text-rose-100 bg-rose-400/10',
                'breve' => 'border-amber-300/40 text-amber-100 bg-amber-300/10',
                'descontinuada' => 'border-white/20 text-white/40 bg-white/5',
            ];
        @endphp

        <section class="space-y-8">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 space-y-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold">Extensões registradas</h2>
                        <p class="text-sm text-white/60">
                            Mostrando
                            @if ($extensoes->count())
                                <strong>{{ $extensoes->firstItem() }}</strong> –
                                <strong>{{ $extensoes->lastItem() }}</strong>
                                de <strong>{{ $extensoes->total() }}</strong>
                            @else
                                <strong>0</strong>
                            @endif
                            resultados.
                        </p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm">
                            <p class="text-xs uppercase tracking-[0.3em] text-white/40">Total</p>
                            <p class="mt-2 text-xl font-semibold text-white">{{ $stats['total'] }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm">
                            <p class="text-xs uppercase tracking-[0.3em] text-white/40">Disponíveis</p>
                            <p class="mt-2 text-xl font-semibold text-white">{{ $stats['disponiveis'] }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm">
                            <p class="text-xs uppercase tracking-[0.3em] text-white/40">Premium</p>
                            <p class="mt-2 text-xl font-semibold text-white">{{ $stats['premium'] }}</p>
                        </div>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.extensions.index') }}" class="rounded-2xl border border-white/10 bg-[#0f0d15]/60 px-4 py-4">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center">
                        <div class="flex-1">
                            <label for="search" class="text-xs uppercase tracking-[0.3em] text-white/40">Pesquisar</label>
                            <input
                                type="search"
                                id="search"
                                name="q"
                                value="{{ $filters['search'] }}"
                                placeholder="Nome, slug, descrição..."
                                class="mt-1 w-full rounded-lg border border-white/15 bg-[#1a1821]/80 px-3 py-2 text-sm text-white focus:border-[#6449a2] focus:outline-none focus:ring-1 focus:ring-[#6449a2]"
                            >
                        </div>
                        <div class="grid gap-3 sm:grid-cols-3 md:w-auto">
                            <div>
                                <label for="filter_tipo" class="text-xs uppercase tracking-[0.3em] text-white/40">Tipo</label>
                                <select
                                    id="filter_tipo"
                                    name="tipo"
                                    class="mt-1 w-full rounded-lg border border-white/15 bg-[#1a1821]/80 px-3 py-2 text-sm text-white focus:border-[#6449a2] focus:outline-none focus:ring-1 focus:ring-[#6449a2]"
                                >
                                    <option value="">Todos</option>
                                    @foreach ($tipos as $valor => $label)
                                        <option value="{{ $valor }}" @selected($filters['tipo'] === $valor)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="filter_status" class="text-xs uppercase tracking-[0.3em] text-white/40">Status</label>
                                <select
                                    id="filter_status"
                                    name="status"
                                    class="mt-1 w-full rounded-lg border border-white/15 bg-[#1a1821]/80 px-3 py-2 text-sm text-white focus:border-[#6449a2] focus:outline-none focus:ring-1 focus:ring-[#6449a2]"
                                >
                                    <option value="">Todos</option>
                                    @foreach ($statuses as $valor => $label)
                                        <option value="{{ $valor }}" @selected($filters['status'] === $valor)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="per_page" class="text-xs uppercase tracking-[0.3em] text-white/40">Por página</label>
                                <select
                                    id="per_page"
                                    name="per_page"
                                    class="mt-1 w-full rounded-lg border border-white/15 bg-[#1a1821]/80 px-3 py-2 text-sm text-white focus:border-[#6449a2] focus:outline-none focus:ring-1 focus:ring-[#6449a2]"
                                >
                                    @foreach ($perPageOptions as $option)
                                        <option value="{{ $option }}" @selected($filters['per_page'] === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[#6449a2] px-4 py-2 text-sm font-semibold hover:bg-[#584091]">
                                <i class="bi bi-funnel"></i> Aplicar
                            </button>
                            <a href="{{ route('admin.extensions.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-white/15 px-4 py-2 text-sm text-white/70 hover:border-white/40">
                                Limpar
                            </a>
                        </div>
                    </div>
                </form>

                <div class="mt-6 overflow-x-auto rounded-2xl border border-white/10">
                    <table class="min-w-full divide-y divide-white/10 text-sm">
                        <thead class="bg-white/5 text-left text-xs uppercase tracking-[0.3em] text-white/40">
                            <tr>
                                <th class="px-4 py-3">Extensão</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Preço</th>
                                <th class="px-4 py-3">Atualização</th>
                                <th class="px-4 py-3">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10 text-white/70">
                            @forelse ($extensoes as $extensao)
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="space-y-1">
                                            <p class="font-semibold text-white">{{ $extensao->nome }}</p>
                                            <p class="text-xs text-white/50">{{ $extensao->categoria ?? 'Sem categoria' }}</p>
                                            <p class="text-xs text-white/40 uppercase tracking-[0.2em]">{{ $extensao->slug }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3 py-1 text-xs uppercase tracking-[0.2em] text-white/60">
                                            {{ $tipos[$extensao->tipo] ?? ucfirst($extensao->tipo) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        @php
                                            $statusClass = $statusColors[$extensao->status] ?? 'border-white/20 text-white/60 bg-white/5';
                                        @endphp
                                        <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs uppercase tracking-[0.2em] {{ $statusClass }}">
                                            {{ $statuses[$extensao->status] ?? ucfirst($extensao->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-sm font-medium text-white">
                                            {{ $extensao->preco !== null ? 'R$ ' . number_format($extensao->preco, 2, ',', '.') : 'Gratuita' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-white/60">
                                        {{ optional($extensao->updated_at)->format('d/m/Y H:i') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <details class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white/70 open:bg-[#0f0d15]/70">
                                            <summary class="flex cursor-pointer items-center gap-2 text-white/80 hover:text-white">
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </summary>
                                            <div class="mt-3 space-y-4">
                                                <form action="{{ route('admin.extensions.update', $extensao) }}" method="POST" class="space-y-3">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="space-y-2">
                                                        <label class="text-xs uppercase tracking-[0.2em] text-white/40">Nome</label>
                                                        <input type="text" name="nome" value="{{ old('nome', $extensao->nome) }}" class="w-full rounded-lg border border-white/15 bg-[#1a1821]/80 px-3 py-2 focus:border-[#6449a2] focus:outline-none focus:ring-1 focus:ring-[#6449a2]" required>
                                                    </div>
                                                    <div class="space-y-2">
                                                        <label class="text-xs uppercase tracking-[0.2em] text-white/40">Descrição</label>
                                                        <textarea name="descricao" rows="3" class="w-full rounded-lg border border-white/15 bg-[#1a1821]/80 px-3 py-2 focus:border-[#6449a2] focus:outline-none focus:ring-1 focus:ring-[#6449a2] resize-y">{{ old('descricao', $extensao->descricao) }}</textarea>
                                                    </div>
                                                    <div class="grid gap-3 sm:grid-cols-2">
                                                        <div class="space-y-2">
                                                            <label class="text-xs uppercase tracking-[0.2em] text-white/40">Categoria</label>
                                                            <input type="text" name="categoria" value="{{ old('categoria', $extensao->categoria) }}" class="w-full rounded-lg border border-white/15 bg-[#1a1821]/80 px-3 py-2 focus:border-[#6449a2] focus:outline-none focus:ring-1 focus:ring-[#6449a2]">
                                                        </div>
                                                        <div class="space-y-2">
                                                            <label class="text-xs uppercase tracking-[0.2em] text-white/40">Classe Provider</label>
                                                            <input type="text" name="provider_class" value="{{ old('provider_class', $extensao->provider_class) }}" class="w-full rounded-lg border border-white/15 bg-[#1a1821]/80 px-3 py-2 focus:border-[#6449a2] focus:outline-none focus:ring-1 focus:ring-[#6449a2]">
                                                        </div>
                                                    </div>
                                                    <div class="grid gap-3 sm:grid-cols-3">
                                                        <div class="space-y-2">
                                                            <label class="text-xs uppercase tracking-[0.2em] text-white/40">Tipo</label>
                                                            <select name="tipo" class="w-full rounded-lg border border-white/15 bg-[#1a1821]/80 px-3 py-2 focus:border-[#6449a2] focus:outline-none focus:ring-1 focus:ring-[#6449a2]">
                                                                @foreach ($tipos as $valor => $label)
                                                                    <option value="{{ $valor }}" @selected(old('tipo', $extensao->tipo) === $valor)>{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="space-y-2">
                                                            <label class="text-xs uppercase tracking-[0.2em] text-white/40">Status</label>
                                                            <select name="status" class="w-full rounded-lg border border-white/15 bg-[#1a1821]/80 px-3 py-2 focus:border-[#6449a2] focus:outline-none focus:ring-1 focus:ring-[#6449a2]">
                                                                @foreach ($statuses as $valor => $label)
                                                                    <option value="{{ $valor }}" @selected(old('status', $extensao->status) === $valor)>{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="space-y-2">
                                                            <label class="text-xs uppercase tracking-[0.2em] text-white/40">Preço (R$)</label>
                                                            <input type="number" name="preco" step="0.01" min="0" value="{{ old('preco', $extensao->preco) }}" class="w-full rounded-lg border border-white/15 bg-[#1a1821]/80 px-3 py-2 focus:border-[#6449a2] focus:outline-none focus:ring-1 focus:ring-[#6449a2]">
                                                        </div>
                                                    </div>
                                                    <div class="space-y-2">
                                                        <label class="text-xs uppercase tracking-[0.2em] text-white/40">Ícone (caminho)</label>
                                                        <input type="text" name="icon_path" value="{{ old('icon_path', $extensao->icon_path) }}" class="w-full rounded-lg border border-white/15 bg-[#1a1821]/80 px-3 py-2 focus:border-[#6449a2] focus:outline-none focus:ring-1 focus:ring-[#6449a2]">
                                                    </div>
                                                    @if ($extensao->metadata)
                                                        <details class="rounded-lg border border-white/10 bg-[#090712]/80 px-3 py-2 text-xs text-white/50">
                                                            <summary class="cursor-pointer text-white/60">Ver metadados</summary>
                                                            <pre class="mt-2 max-h-52 overflow-y-auto text-[11px]">{{ json_encode($extensao->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                        </details>
                                                    @endif
                                                    <div class="flex flex-wrap items-center gap-2 pt-2">
                                                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-emerald-500/80 hover:bg-emerald-500 px-4 py-2 text-xs font-semibold text-white transition">
                                                            <i class="bi bi-save"></i> Salvar alterações
                                                        </button>
                                                    </div>
                                                </form>
                                                <form action="{{ route('admin.extensions.sync') }}" method="POST" class="pt-1">
                                                    @csrf
                                                    <input type="hidden" name="atualizar" value="1">
                                                    <input type="hidden" name="slug" value="{{ $extensao->slug }}">
                                                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-white/15 bg-white/5 px-3 py-2 text-xs font-semibold text-white/70 hover:border-white/40 transition">
                                                        <i class="bi bi-arrow-repeat"></i> Reimportar do módulo
                                                    </button>
                                                </form>
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-sm text-white/50">Nenhuma extensão encontrada para os filtros aplicados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pt-6">
                    {{ $extensoes->links() }}
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-white/10 py-8 text-center text-xs text-white/40">
        Painel Administrativo Kleros — {{ now()->format('Y') }}
    </footer>
</div>
@endsection
