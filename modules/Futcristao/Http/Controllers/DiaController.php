<?php

namespace Modules\Futcristao\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Futcristao\Models\FutebolDia;
use Modules\Futcristao\Models\FutebolGrupo;

class DiaController extends Controller
{
    public function create(): View
    {
        $congregacao = app('congregacao');
        abort_if(! $congregacao, 404);

        $grupos = FutebolGrupo::where('congregacao_id', $congregacao->id)
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return view('futcristao::dias.form', [
            'appName' => config('app.name'),
            'congregacao' => $congregacao,
            'grupos' => $grupos,
            'dia' => new FutebolDia(['status' => 'agendado']),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $congregacao = app('congregacao');
        abort_if(! $congregacao, 404);

        $validated = $this->validateDia($request);

        FutebolDia::create([
            'futebol_grupo_id' => $validated['futebol_grupo_id'],
            'congregacao_id' => $congregacao->id,
            'data_jogo' => $validated['data_jogo'],
            'hora_jogo' => $validated['hora_jogo'] ?? null,
            'local' => $validated['local'] ?? null,
            'status' => $validated['status'],
            'placar_time_a' => $validated['placar_time_a'] ?? 0,
            'placar_time_b' => $validated['placar_time_b'] ?? 0,
            'observacoes' => $validated['observacoes'] ?? null,
        ]);

        return redirect()->route('futcristao.index')->with('success', 'Dia de jogo registrado.');
    }

    public function edit(FutebolDia $dia): View
    {
        $congregacao = app('congregacao');
        abort_if(! $congregacao || $dia->congregacao_id !== $congregacao->id, 403);

        $grupos = FutebolGrupo::where('congregacao_id', $congregacao->id)
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return view('futcristao::dias.form', [
            'appName' => config('app.name'),
            'congregacao' => $congregacao,
            'grupos' => $grupos,
            'dia' => $dia,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, FutebolDia $dia): RedirectResponse
    {
        $congregacao = app('congregacao');
        abort_if(! $congregacao || $dia->congregacao_id !== $congregacao->id, 403);

        $validated = $this->validateDia($request);

        $dia->update([
            'futebol_grupo_id' => $validated['futebol_grupo_id'],
            'data_jogo' => $validated['data_jogo'],
            'hora_jogo' => $validated['hora_jogo'] ?? null,
            'local' => $validated['local'] ?? null,
            'status' => $validated['status'],
            'placar_time_a' => $validated['placar_time_a'] ?? 0,
            'placar_time_b' => $validated['placar_time_b'] ?? 0,
            'observacoes' => $validated['observacoes'] ?? null,
        ]);

        return redirect()->route('futcristao.index')->with('success', 'Dia de jogo atualizado.');
    }

    public function destroy(FutebolDia $dia): RedirectResponse
    {
        $congregacao = app('congregacao');
        abort_if(! $congregacao || $dia->congregacao_id !== $congregacao->id, 403);

        $dia->delete();

        return redirect()->route('futcristao.index')->with('success', 'Dia de jogo removido.');
    }

    protected function validateDia(Request $request): array
    {
        return $request->validate([
            'futebol_grupo_id' => ['required', 'exists:futebol_grupos,id'],
            'data_jogo' => ['required', 'date'],
            'hora_jogo' => ['nullable', 'date_format:H:i'],
            'local' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:' . implode(',', FutebolDia::STATUS)],
            'placar_time_a' => ['nullable', 'integer', 'min:0', 'max:99'],
            'placar_time_b' => ['nullable', 'integer', 'min:0', 'max:99'],
            'observacoes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
