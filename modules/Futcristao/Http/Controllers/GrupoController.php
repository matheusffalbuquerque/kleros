<?php

namespace Modules\Futcristao\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Futcristao\Models\FutebolGrupo;

class GrupoController extends Controller
{
    public function create(): View
    {
        $congregacao = app('congregacao');
        abort_if(! $congregacao, 404);

        return view('futcristao::grupos.form', [
            'appName' => config('app.name'),
            'congregacao' => $congregacao,
            'grupo' => new FutebolGrupo(['ativo' => true]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $congregacao = app('congregacao');
        abort_if(! $congregacao, 404);

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:2000'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        FutebolGrupo::create([
            'congregacao_id' => $congregacao->id,
            'nome' => $validated['nome'],
            'descricao' => $validated['descricao'] ?? null,
            'ativo' => $request->boolean('ativo', true),
        ]);

        return redirect()->route('futcristao.index')->with('success', 'Grupo criado com sucesso.');
    }

    public function edit(FutebolGrupo $grupo): View
    {
        $congregacao = app('congregacao');
        abort_if(! $congregacao || $grupo->congregacao_id !== $congregacao->id, 403);

        return view('futcristao::grupos.form', [
            'appName' => config('app.name'),
            'congregacao' => $congregacao,
            'grupo' => $grupo,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, FutebolGrupo $grupo): RedirectResponse
    {
        $congregacao = app('congregacao');
        abort_if(! $congregacao || $grupo->congregacao_id !== $congregacao->id, 403);

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:2000'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        $grupo->update([
            'nome' => $validated['nome'],
            'descricao' => $validated['descricao'] ?? null,
            'ativo' => $request->boolean('ativo'),
        ]);

        return redirect()->route('futcristao.index')->with('success', 'Grupo atualizado com sucesso.');
    }

    public function destroy(FutebolGrupo $grupo): RedirectResponse
    {
        $congregacao = app('congregacao');
        abort_if(! $congregacao || $grupo->congregacao_id !== $congregacao->id, 403);

        $grupo->delete();

        return redirect()->route('futcristao.index')->with('success', 'Grupo removido.');
    }
}
