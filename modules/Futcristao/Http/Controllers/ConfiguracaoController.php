<?php

namespace Modules\Futcristao\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Futcristao\Models\FutebolConfiguracao;

class ConfiguracaoController extends Controller
{
    public function edit(): View
    {
        $congregacao = app('congregacao');
        abort_if(! $congregacao, 404);

        $config = FutebolConfiguracao::firstOrCreate(
            ['congregacao_id' => $congregacao->id],
            ['numero_jogadores' => 10, 'regras_gerais' => null]
        );

        return view('futcristao::includes.config_modal', [
            'config' => $config,
            'congregacao' => $congregacao,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $congregacao = app('congregacao');
        abort_if(! $congregacao, 404);

        $config = FutebolConfiguracao::firstOrCreate(
            ['congregacao_id' => $congregacao->id],
            ['numero_jogadores' => 10, 'regras_gerais' => null]
        );

        $validated = $request->validate([
            'numero_jogadores' => ['required', 'integer', 'min:5', 'max:30'],
            'regras_gerais' => ['nullable', 'string', 'max:5000'],
        ]);

        $config->update($validated);

        return redirect()->route('futcristao.index')->with('success', 'Configurações atualizadas com sucesso.');
    }
}
