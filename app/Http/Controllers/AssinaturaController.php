<?php

namespace App\Http\Controllers;

use App\Models\ProdutoAssinatura;
use App\Models\TipoProduto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssinaturaController extends Controller
{
    public function index(Request $request)
    {
        $congregacao = app('congregacao');

        $tipoSelecionado = (int) $request->query('tipo', 0);
        $busca = trim((string) $request->query('q', ''));

        $tipos = TipoProduto::orderBy('nome')->get();

        $resumoTipos = ProdutoAssinatura::query()
            ->select('tipo_id', DB::raw('count(*) as total'))
            ->daCongregacao()
            ->ativos()
            ->groupBy('tipo_id')
            ->pluck('total', 'tipo_id');

        $produtos = ProdutoAssinatura::with('tipo')
            ->daCongregacao()
            ->ativos()
            ->doTipo($tipoSelecionado ?: null)
            ->when($busca, function ($query) use ($busca) {
                $query->where(function ($search) use ($busca) {
                    $search->where('titulo', 'like', '%' . $busca . '%')
                        ->orWhere('descricao', 'like', '%' . $busca . '%');
                });
            })
            ->orderByDesc('criado_em')
            ->orderBy('titulo')
            ->get();

        return view('assinaturas.index', [
            'congregacao' => $congregacao,
            'produtos' => $produtos,
            'tipos' => $tipos,
            'resumoTipos' => $resumoTipos,
            'tipoSelecionado' => $tipoSelecionado,
            'busca' => $busca,
        ]);
    }
}
