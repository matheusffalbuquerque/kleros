<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BaseDoutrinaria;
use App\Models\Denominacao;
use App\Models\Ministerio;
use Illuminate\Http\Request;

class DenominacaoController extends Controller
{
    public function create()
    {
        $bases_doutrinarias = BaseDoutrinaria::all();

        return view('denominacoes.cadastro', compact('bases_doutrinarias'));
    }

    public function store(Request $request)
    {
        $denominacao = new Denominacao;

        $request->validate([
            'nome' => 'required|string|max:255',
            'base_doutrinaria' => 'required|exists:bases_doutrinarias,id',
            'ministerios_eclesiasticos' => 'required',
        ], [
            '*.required' => __('denominations.validation.required'),
            '*.string' => __('denominations.validation.string'),
            '*.max' => __('denominations.validation.max'),
        ]);

        $denominacao->nome = $request->nome;
        $denominacao->base_doutrinaria = $request->base_doutrinaria;
        $denominacao->ativa = true;
        $denominacao->ministerios_eclesiasticos = $request->ministerios_eclesiasticos;

        if($denominacao->save()){
            session(['denominacao_id' => $denominacao->id]);

            if($denominacao->ministerios_eclesiasticos){

                // Decodifica o JSON dos ministérios eclesiásticos
                $ministerios = json_decode($denominacao->ministerios_eclesiasticos, true);

                //Criar os ministérios eclesiásticos
                foreach ($ministerios as $ministerio) {
                    $novoMinisterio = new Ministerio();
                    $novoMinisterio->titulo = $ministerio;
                    $novoMinisterio->denominacao_id = $denominacao->id;
                    $novoMinisterio->save();
                }

            } 

        } else {
            return redirect()->back()->with('error', __('denominations.alerts.error'));
        }

        return redirect()
            ->route('congregacoes.cadastro')
            ->with('success', __('denominations.alerts.success'));
    }

    public function configuracoes(Request $request)
    {
        $user = $request->user();

        $query = Denominacao::with('ministerios')->orderBy('nome');

        $denomId = null;
        if ($user) {
            if (! method_exists($user, 'hasRole') || ! $user->hasRole('kleros')) {
                if ($user->denominacao_id) {
                    $denomId = $user->denominacao_id;
                } else {
                    $denomFromMembership = optional(optional(optional($user)->membro)->congregacao)->denominacao;
                    if ($denomFromMembership) {
                        $denomId = $denomFromMembership->id;
                    }
                }
            }
        }

        if ($denomId) {
            $query->where('id', $denomId);
        }

        $denominacoes = $query->get();

        if ($denominacoes->isEmpty()) {
            return redirect()
                ->route('denominacoes.create')
                ->with('msg', 'Cadastre uma denominação antes de acessar as configurações.');
        }

        $selectedId = (int) $request->query('denominacao', $denominacoes->first()->id);
        $denominacao = $denominacoes->firstWhere('id', $selectedId) ?? $denominacoes->first();

        if ($denominacao->relationLoaded('ministerios') === false) {
            $denominacao->load('ministerios');
        }

        $basesDoutrinarias = BaseDoutrinaria::orderBy('nome')->get();

        return view('denominacoes.configuracoes', [
            'denominacoes' => $denominacoes,
            'denominacao' => $denominacao,
            'basesDoutrinarias' => $basesDoutrinarias,
        ]);
    }

    public function update(Request $request, $id)
    {
        $denominacao = Denominacao::with('ministerios')->findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'base_doutrinaria' => 'nullable|exists:bases_doutrinarias,id',
            'ativa' => 'nullable|boolean',
            'ministerios' => 'array',
            'ministerios.*.id' => 'nullable',
            'ministerios.*.titulo' => 'nullable|string|max:255',
            'ministerios.*.sigla' => 'nullable|string|max:50',
            'ministerios.*.descricao' => 'nullable|string',
            'ministerios_removidos' => 'array',
            'ministerios_removidos.*' => 'integer|exists:ministerios,id',
        ]);

        $denominacao->nome = $validated['nome'];
        $denominacao->base_doutrinaria = $validated['base_doutrinaria'] ?? null;
        $denominacao->ativa = $request->boolean('ativa');
        $denominacao->save();

        $ministeriosRemovidos = collect($request->input('ministerios_removidos', []))
            ->filter()
            ->map(fn ($value) => (int) $value);

        if ($ministeriosRemovidos->isNotEmpty()) {
            $denominacao->ministerios()
                ->whereIn('id', $ministeriosRemovidos->all())
                ->delete();
        }

        $ministeriosPayload = $request->input('ministerios', []);

        foreach ($ministeriosPayload as $payload) {
            $titulo = trim($payload['titulo'] ?? '');
            $sigla = trim($payload['sigla'] ?? '');
            $descricao = trim($payload['descricao'] ?? '');
            $ministerioId = $payload['id'] ?? null;

            if ($titulo === '' && $sigla === '' && $descricao === '') {
                continue;
            }

            if ($ministerioId) {
                $ministerio = $denominacao->ministerios->firstWhere('id', (int) $ministerioId);
                if (! $ministerio) {
                    continue;
                }
            } else {
                $ministerio = new Ministerio();
                $ministerio->denominacao_id = $denominacao->id;
            }

            $ministerio->titulo = $titulo !== '' ? $titulo : null;
            $ministerio->sigla = $sigla !== '' ? $sigla : null;
            $ministerio->descricao = $descricao !== '' ? $descricao : null;
            $ministerio->save();
        }

        $denominacao->refresh();

        $ministeriosLista = $denominacao->ministerios()
            ->orderBy('titulo')
            ->pluck('titulo')
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->values()
            ->all();

        $denominacao->ministerios_eclesiasticos = json_encode($ministeriosLista);
        $denominacao->save();

        return redirect()
            ->route('denominacoes.configuracoes', ['denominacao' => $denominacao->id])
            ->with('success', 'Configurações da denominação atualizadas com sucesso.');
    }
}
