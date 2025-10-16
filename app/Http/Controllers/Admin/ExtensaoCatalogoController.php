<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExtensaoCatalogo;
use App\Services\ExtensaoCatalogoSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExtensaoCatalogoController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->query('q', '')),
            'tipo' => $request->query('tipo'),
            'status' => $request->query('status'),
            'per_page' => (int) $request->query('per_page', 15),
        ];

        $filters['per_page'] = max(5, min(100, $filters['per_page'] ?: 15));

        $query = ExtensaoCatalogo::query();

        if ($filters['search'] !== '') {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('nome', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%")
                    ->orWhere('descricao', 'like', "%{$term}%")
                    ->orWhere('categoria', 'like', "%{$term}%");
            });
        }

        if ($filters['tipo']) {
            $query->where('tipo', $filters['tipo']);
        }

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        $extensoes = $query->orderBy('nome')
            ->paginate($filters['per_page'])
            ->withQueryString();

        $tipos = [
            'gratuita' => 'Gratuita',
            'paga' => 'Paga (licença única)',
            'assinatura' => 'Assinatura',
            'one_time' => 'Pagamento único',
        ];

        $statuses = [
            'disponivel' => 'Disponível',
            'indisponivel' => 'Indisponível',
            'breve' => 'Em breve',
            'descontinuada' => 'Descontinuada',
        ];

        $stats = [
            'total' => ExtensaoCatalogo::count(),
            'disponiveis' => ExtensaoCatalogo::where('status', 'disponivel')->count(),
            'premium' => ExtensaoCatalogo::whereIn('tipo', ['paga', 'assinatura', 'one_time'])->count(),
        ];

        $perPageOptions = [15, 25, 50, 100];

        return view('admin.extensions', [
            'extensoes' => $extensoes,
            'tipos' => $tipos,
            'statuses' => $statuses,
            'filters' => $filters,
            'stats' => $stats,
            'perPageOptions' => $perPageOptions,
        ]);
    }

    public function sync(Request $request, ExtensaoCatalogoSyncService $service): RedirectResponse
    {
        $atualizar = $request->boolean('atualizar', true);
        $slug = $request->input('slug');
        $sincronizadas = $service->sync($atualizar, $slug);

        return redirect()
            ->route('admin.extensions.index')
            ->with('msg', sprintf(
                '%d extensões sincronizadas com sucesso%s.',
                $sincronizadas->count(),
                $atualizar ? ' (dados atualizados)' : ''
            ));
    }

    public function update(Request $request, ExtensaoCatalogo $extensaoCatalogo): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:180'],
            'categoria' => ['nullable', 'string', 'max:120'],
            'tipo' => ['required', 'string', 'in:gratuita,paga,assinatura,one_time'],
            'status' => ['required', 'string', 'in:disponivel,indisponivel,breve,descontinuada'],
            'preco' => ['nullable', 'numeric', 'min:0'],
            'provider_class' => ['nullable', 'string', 'max:255'],
            'icon_path' => ['nullable', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
        ]);

        if ($dados['preco'] === null || $dados['preco'] === '') {
            $dados['preco'] = null;
        }

        $extensaoCatalogo->update($dados);

        return redirect()
            ->route('admin.extensions.index')
            ->with('msg', sprintf('Extensão "%s" atualizada com sucesso.', $extensaoCatalogo->nome));
    }
}
