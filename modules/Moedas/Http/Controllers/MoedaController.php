<?php

namespace Modules\Moedas\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Moedas\Models\Carteira;
use Modules\Moedas\Models\Moeda;
use Modules\Moedas\Models\Transacao;

class MoedaController extends Controller
{
    public function index(Request $request)
    {
        $congregacao = app('congregacao');

        abort_if(! $congregacao, 403, 'Congregação não encontrada para o usuário autenticado.');

        $moedas = Moeda::with(['regras', 'responsavel'])
            ->where('congregacao_id', $congregacao->id)
            ->orderBy('nome')
            ->get();

        $moedaSelecionada = null;

        if ($moedas->isNotEmpty()) {
            $selecionada = (int) $request->query('moeda');
            $moedaSelecionada = $moedas->firstWhere('id', $selecionada) ?? $moedas->first();
        }

        $carteiras = collect();
        $transacoes = collect();
        $saldoTotal = 0;
        $usuarios = User::query()
            ->where('congregacao_id', $congregacao->id)
            ->orderBy('name')
            ->get();

        if ($moedaSelecionada) {
            $carteiras = Carteira::with('usuario')
                ->where('moeda_id', $moedaSelecionada->id)
                ->orderByDesc('saldo')
                ->paginate(12, ['*'], 'carteiras_pagina');

            $transacoes = Transacao::with(['remetente', 'destinatario'])
                ->where('moeda_id', $moedaSelecionada->id)
                ->latest('criado_em')
                ->paginate(12, ['*'], 'transacoes_pagina');

            $saldoTotal = Carteira::where('moeda_id', $moedaSelecionada->id)->sum('saldo');
        }

        return view('moedas::painel', [
            'congregacao' => $congregacao,
            'appName' => config('app.name'),
            'moedas' => $moedas,
            'moedaSelecionada' => $moedaSelecionada,
            'carteiras' => $carteiras,
            'transacoes' => $transacoes,
            'saldoTotal' => $saldoTotal,
            'usuarios' => $usuarios,
        ]);
    }

    public function store(Request $request)
    {
        $congregacao = app('congregacao');
        abort_if(! $congregacao, 403);

        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'simbolo' => ['required', 'string', 'max:10'],
            'imagem_url' => ['nullable', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'taxa_conversao' => ['nullable', 'numeric', 'min:0'],
            'ativo' => ['nullable', 'boolean'],
            'responsavel_id' => ['nullable', Rule::exists('users', 'id')->where(fn ($q) => $q->where('congregacao_id', $congregacao->id))],
        ]);

        $moeda = Moeda::create([
            'congregacao_id' => $congregacao->id,
            'nome' => $dados['nome'],
            'simbolo' => $dados['simbolo'],
            'imagem_url' => $dados['imagem_url'] ?? null,
            'descricao' => $dados['descricao'] ?? null,
            'taxa_conversao' => $dados['taxa_conversao'] ?? null,
            'ativo' => $dados['ativo'] ?? true,
            'criado_por' => $dados['responsavel_id'] ?? Auth::id(),
        ]);

        $moeda->regras()->create();

        return redirect()
            ->route('moedas.painel', ['moeda' => $moeda->id])
            ->with('success', 'Moeda criada com sucesso.');
    }

    public function update(Request $request, Moeda $moeda)
    {
        $congregacao = app('congregacao');
        abort_if(! $congregacao || $moeda->congregacao_id !== $congregacao->id, 404);

        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'simbolo' => ['required', 'string', 'max:10'],
            'imagem_url' => ['nullable', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'taxa_conversao' => ['nullable', 'numeric', 'min:0'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        $moeda->fill([
            'nome' => $dados['nome'],
            'simbolo' => $dados['simbolo'],
            'imagem_url' => $dados['imagem_url'] ?? null,
            'descricao' => $dados['descricao'] ?? null,
            'taxa_conversao' => $dados['taxa_conversao'] ?? null,
            'ativo' => $dados['ativo'] ?? true,
        ]);

        $moeda->save();

        return redirect()
            ->route('moedas.painel', ['moeda' => $moeda->id])
            ->with('success', 'Dados da moeda atualizados com sucesso.');
    }

    public function emitir(Request $request, Moeda $moeda)
    {
        $congregacao = app('congregacao');
        abort_if(! $congregacao || $moeda->congregacao_id !== $congregacao->id, 404);

        $dados = $request->validate([
            'usuario_id' => ['required', Rule::exists('users', 'id')->where(fn ($q) => $q->where('congregacao_id', $congregacao->id))],
            'valor' => ['required', 'numeric', 'min:0.01'],
            'descricao' => ['nullable', 'string', 'max:500'],
            'referencia_id' => ['nullable', 'integer', 'min:1'],
        ]);

        $usuario = User::where('congregacao_id', $congregacao->id)->findOrFail($dados['usuario_id']);

        DB::transaction(function () use ($dados, $moeda, $usuario) {
            $carteira = Carteira::query()
                ->where('moeda_id', $moeda->id)
                ->where('usuario_id', $usuario->id)
                ->lockForUpdate()
                ->first();

            if (! $carteira) {
                $carteira = new Carteira([
                    'moeda_id' => $moeda->id,
                    'usuario_id' => $usuario->id,
                    'saldo' => 0,
                ]);
                $carteira->save();
                $carteira->refresh();
            }

            abort_if($carteira->bloqueado, 422, 'A carteira do usuário está bloqueada.');

            $valorCentavos = (int) round($dados['valor'] * 100);
            $saldoAtualCentavos = (int) round($carteira->saldo * 100);
            $novoSaldo = ($saldoAtualCentavos + $valorCentavos) / 100;

            $carteira->saldo = $novoSaldo;
            $carteira->save();

            Transacao::create([
                'moeda_id' => $moeda->id,
                'remetente_id' => Auth::id(),
                'destinatario_id' => $usuario->id,
                'tipo' => 'emissao',
                'valor' => $dados['valor'],
                'descricao' => $dados['descricao'] ?? null,
                'referencia_id' => $dados['referencia_id'] ?? null,
            ]);
        });

        return redirect()
            ->route('moedas.painel', ['moeda' => $moeda->id])
            ->with('success', 'Moedas emitidas com sucesso.');
    }

    public function updateRules(Request $request, Moeda $moeda)
    {
        $congregacao = app('congregacao');
        abort_if(! $congregacao || $moeda->congregacao_id !== $congregacao->id, 404);

        $dados = $request->validate([
            'permitir_transferencias' => ['nullable', 'boolean'],
            'permitir_resgate' => ['nullable', 'boolean'],
            'permitir_uso_em_jogos' => ['nullable', 'boolean'],
            'limite_diario' => ['nullable', 'numeric', 'min:0'],
            'taxa_transacao' => ['nullable', 'numeric', 'between:0,100'],
            'minimo_resgate' => ['nullable', 'numeric', 'min:0'],
            'observacoes' => ['nullable', 'string'],
            'responsavel_id' => ['nullable', Rule::exists('users', 'id')->where(fn ($q) => $q->where('congregacao_id', $congregacao->id))],
        ]);

        if (array_key_exists('responsavel_id', $dados)) {
            $moeda->criado_por = $dados['responsavel_id'] ?: null;
            $moeda->save();
        }

        $moeda->regras()->updateOrCreate(
            ['moeda_id' => $moeda->id],
            [
                'permitir_transferencias' => (bool) ($dados['permitir_transferencias'] ?? false),
                'permitir_resgate' => (bool) ($dados['permitir_resgate'] ?? false),
                'permitir_uso_em_jogos' => (bool) ($dados['permitir_uso_em_jogos'] ?? false),
                'limite_diario' => $dados['limite_diario'] ?? null,
                'taxa_transacao' => $dados['taxa_transacao'] ?? null,
                'minimo_resgate' => $dados['minimo_resgate'] ?? null,
                'observacoes' => $dados['observacoes'] ?? null,
            ]
        );

        return redirect()
            ->route('moedas.painel', ['moeda' => $moeda->id])
            ->with('success', 'Regras atualizadas com sucesso.');
    }
}
