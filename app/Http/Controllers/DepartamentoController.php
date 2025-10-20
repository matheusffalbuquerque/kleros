<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agrupamento;
use Illuminate\Http\Request;
use App\Models\Membro;
use App\Models\Setor;
use Illuminate\Validation\Rule;

class DepartamentoController extends Controller
{
    public function painel()
    {
        $congregacao = app('congregacao');
        $departamentos = Agrupamento::where('tipo', 'departamento')
            ->where('congregacao_id', app('congregacao')->id)
            ->paginate(10);
        $setores = collect();

        if ($congregacao && optional($congregacao->config)->agrupamentos === 'setor') {
            $setores = Setor::where('congregacao_id', $congregacao->id)
                ->orderBy('nome')
                ->get();
        }

        return view('departamentos.painel', compact('departamentos', 'setores'));
    }

    public function create()
    {
        // Lógica para exibir o formulário de criação de departamento
        return view('departamentos.cadastro');
    }

    public function store(Request $request)
    {
        $congregacao = app('congregacao');
        $congregacaoId = $congregacao->id;

        $request->validate([
            'nome' => 'required|unique:agrupamentos,nome,NULL,id,congregacao_id,' . $congregacaoId . '|max:255',
            'lider_id' => 'nullable|exists:membros,id',
            'colider_id' => 'nullable|exists:membros,id',
            'agrupamento_pai_id' => [
                'nullable',
                Rule::exists('agrupamentos', 'id')->where(function ($query) use ($congregacaoId) {
                    $query->where('tipo', 'setor')
                        ->where('congregacao_id', $congregacaoId);
                }),
            ],
        ], [
            'nome.required' => 'O nome do departamento é obrigatório.',
            'nome.unique' => 'Já existe um departamento com esse nome nesta congregação.',
            'nome.max' => 'O nome do departamento não pode exceder 255 caracteres.',
        ]);

        $departamento = new Agrupamento();
        $departamento->nome = $request->nome;
        $departamento->descricao = $request->descricao;
        $departamento->tipo = 'departamento';
        $departamento->congregacao_id = app('congregacao')->id;
        $departamento->lider_id = $request->lider_id;
        $departamento->colider_id = $request->colider_id;
        $departamento->agrupamento_pai_id = $request->filled('agrupamento_pai_id') ? $request->agrupamento_pai_id : null;

        $departamento->save();

        return redirect()->back()->with('success', 'Departamento criado com sucesso!');
    }

    public function show($id)
    {
        $congregacao = app('congregacao');

        $departamento = Agrupamento::where('id', $id)
            ->where('tipo', 'departamento')
            ->where('congregacao_id', $congregacao->id)
            ->firstOrFail();

        $integrantes = $departamento->integrantes()
            ->orderBy('nome')
            ->paginate(10);

        $membros = Membro::DaCongregacao()
            ->whereDoesntHave('agrupamentos', function ($query) use ($departamento) {
                $query->where('agrupamento_id', $departamento->id);
            })
            ->orderBy('nome')
            ->get();

        return view('departamentos.integrantes', [
            'congregacao' => $congregacao,
            'departamento' => $departamento,
            'integrantes' => $integrantes,
            'membros' => $membros,
        ]);
    }

    public function form_criar()
    {
        $congregacao = app('congregacao');

        $membros = Membro::where('congregacao_id', $congregacao->id)
            ->orderBy('nome')
            ->get();

        $setores = collect();

        if ($congregacao && optional($congregacao->config)->agrupamentos === 'setor') {
            $setores = Agrupamento::where('tipo', 'setor')
                ->where('congregacao_id', $congregacao->id)
                ->orderBy('nome')
                ->get();
        }

        return view('departamentos.includes.form_criar', compact('membros', 'setores'));
    }

    public function form_editar($id)
    {
        $departamento = Agrupamento::where('id', $id)
            ->where('congregacao_id', app('congregacao')->id)
            ->where('tipo', 'departamento')
            ->firstOrFail();

        $membros = Membro::where('congregacao_id', app('congregacao')->id)
            ->orderBy('nome')
            ->get();

        $setores = collect();
        $congregacao = app('congregacao');

        if ($congregacao && optional($congregacao->config)->agrupamentos === 'setor') {
            $setores = Agrupamento::where('tipo', 'setor')
                ->where('congregacao_id', $congregacao->id)
                ->orderBy('nome')
                ->get();
        }

        return view('departamentos.includes.form_editar', compact('membros', 'departamento', 'setores'));
    }

    public function update(Request $request, $id)
    {
        $congregacaoId = app('congregacao')->id;

        $departamento = Agrupamento::where('id', $id)
            ->where('tipo', 'departamento')
            ->where('congregacao_id', $congregacaoId)
            ->firstOrFail();

        $request->validate([
            'nome' => 'required|max:255|unique:agrupamentos,nome,' . $departamento->id . ',id,congregacao_id,' . $congregacaoId,
            'lider_id' => 'nullable|exists:membros,id',
            'colider_id' => 'nullable|exists:membros,id',
            'agrupamento_pai_id' => [
                'nullable',
                Rule::exists('agrupamentos', 'id')->where(function ($query) use ($congregacaoId) {
                    $query->where('tipo', 'setor')
                        ->where('congregacao_id', $congregacaoId);
                }),
            ],
        ], [
            'nome.required' => 'O nome do departamento é obrigatório.',
            'nome.unique' => 'Já existe um departamento com esse nome nesta congregação.',
            'nome.max' => 'O nome do departamento não pode exceder 255 caracteres.',
        ]);

        $departamento->nome = $request->nome;
        $departamento->descricao = $request->descricao;
        $departamento->lider_id = $request->lider_id;
        $departamento->colider_id = $request->colider_id;
        $departamento->agrupamento_pai_id = $request->filled('agrupamento_pai_id') ? $request->agrupamento_pai_id : null;

        $departamento->save();

        return redirect()->back()->with('success', 'Departamento atualizado com sucesso!');
    }

    public function search(Request $request)
    {
        $congregacaoId = app('congregacao')->id;

        $query = Agrupamento::where('tipo', 'departamento')
            ->where('congregacao_id', $congregacaoId)
            ->with(['lider', 'colider']);

        if ($request->filled('setor')) {
            $query->where('agrupamento_pai_id', $request->input('setor'));
        }

        if ($request->filled('membro')) {
            $membro = $request->input('membro');

            $query->where(function ($q) use ($membro) {
                $q->whereHas('lider', function ($sub) use ($membro) {
                        $sub->where('nome', 'like', "%{$membro}%");
                    })
                    ->orWhereHas('colider', function ($sub) use ($membro) {
                        $sub->where('nome', 'like', "%{$membro}%");
                    });
            });
        }

        $departamentos = $query->orderBy('nome')->paginate(10);

        $view = view('departamentos.includes.lista', compact('departamentos'))->render();

        return response()->json(['view' => $view]);
    }

    public function destroy(Request $request, $id)
    {
        $departamento = Agrupamento::where('id', $id)
            ->where('tipo', 'departamento')
            ->where('congregacao_id', app('congregacao')->id)
            ->firstOrFail();

        $departamento->delete();

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Departamento excluído com sucesso!']);
        }

        return redirect('/cadastros#departamentos')->with('msg', 'Departamento excluído com sucesso!');
    }

    public function addMember(Request $request)
    {
        $congregacao = app('congregacao');
        $congregacaoId = $congregacao->id;

        $data = $request->validate([
            'departamento' => [
                'required',
                'integer',
                Rule::exists('agrupamentos', 'id')->where(function ($query) use ($congregacaoId) {
                    $query->where('tipo', 'departamento')
                        ->where('congregacao_id', $congregacaoId);
                }),
            ],
            'membro' => [
                'required',
                'integer',
                Rule::exists('membros', 'id')->where(function ($query) use ($congregacaoId) {
                    $query->where('congregacao_id', $congregacaoId);
                }),
            ],
        ]);

        $departamento = Agrupamento::findOrFail($data['departamento']);

        $departamento->integrantes()->syncWithoutDetaching([
            $data['membro'] => ['congregacao_id' => $congregacaoId],
        ]);

        return redirect()
            ->route('departamentos.integrantes', $departamento->id)
            ->with('msg', 'Membro adicionado ao departamento.');
    }

    public function removeMember($departamentoId, $membroId)
    {
        $departamento = Agrupamento::where('id', $departamentoId)
            ->where('tipo', 'departamento')
            ->where('congregacao_id', app('congregacao')->id)
            ->firstOrFail();

        $departamento->integrantes()->detach($membroId);

        return redirect()
            ->route('departamentos.integrantes', $departamento->id)
            ->with('msg', 'Membro removido do departamento.');
    }
}
