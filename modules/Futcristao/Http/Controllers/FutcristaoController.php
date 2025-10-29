<?php

namespace Modules\Futcristao\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\Futcristao\Models\FutebolConfiguracao;
use Modules\Futcristao\Models\FutebolConvidado;
use Modules\Futcristao\Models\FutebolDia;
use Modules\Futcristao\Models\FutebolGrupo;
use Modules\Futcristao\Models\FutebolGrupoMembro;

class FutcristaoController extends Controller
{
    public function index(Request $request): View
    {
        $congregacao = app('congregacao');
        abort_if(! $congregacao, 404);

        $config = FutebolConfiguracao::firstOrCreate(
            ['congregacao_id' => $congregacao->id],
            ['numero_jogadores' => 10, 'regras_gerais' => null]
        );

        $totalGrupos = FutebolGrupo::where('congregacao_id', $congregacao->id)->count();
        $totalMembros = FutebolGrupoMembro::where('congregacao_id', $congregacao->id)
            ->distinct('membro_id')
            ->count('membro_id');
        $totalConvidados = FutebolConvidado::where('congregacao_id', $congregacao->id)->count();

        $semanaAtualInicio = Carbon::now()->startOfWeek();
        $semanaAtualFim = Carbon::now()->endOfWeek();

        $diasSemana = FutebolDia::where('congregacao_id', $congregacao->id)
            ->whereBetween('data_jogo', [$semanaAtualInicio, $semanaAtualFim])
            ->count();

        $proximoDia = FutebolDia::with('grupo')
            ->where('congregacao_id', $congregacao->id)
            ->whereDate('data_jogo', '>=', now()->toDateString())
            ->orderBy('data_jogo')
            ->orderBy('hora_jogo')
            ->first();

        $mediaTime = $totalGrupos > 0 && $totalMembros > 0
            ? round($totalMembros / $totalGrupos, 1)
            : $config->numero_jogadores;

        $stats = [
            'grupos' => $totalGrupos,
            'jogadores' => $totalMembros,
            'convidados' => $totalConvidados,
            'semana' => $diasSemana,
            'proximoDia' => $proximoDia,
            'mediaTime' => $mediaTime,
        ];

        $dias = FutebolDia::with('grupo')
            ->where('congregacao_id', $congregacao->id)
            ->orderBy('data_jogo')
            ->orderBy('hora_jogo')
            ->get();

        $hoje = Carbon::now()->startOfDay();
        $proximosDias = $dias->filter(fn (FutebolDia $dia) => $dia->data_jogo === null || $dia->data_jogo->gte($hoje))
            ->values()
            ->take(8);
        $historicoDias = $dias->filter(fn (FutebolDia $dia) => $dia->data_jogo !== null && $dia->data_jogo->lt($hoje))
            ->sortByDesc('data_jogo')
            ->values()
            ->take(6);

        $grupos = FutebolGrupo::where('congregacao_id', $congregacao->id)
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return view('futcristao::index', [
            'appName' => config('app.name'),
            'congregacao' => $congregacao,
            'config' => $config,
            'stats' => $stats,
            'proximosDias' => $proximosDias,
            'historicoDias' => $historicoDias,
            'grupos' => $grupos,
            'usuario' => Auth::user(),
        ]);
    }
}
