<?php

namespace App\Http\Controllers;

use App\Models\Culto;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class ProgramacaoController extends Controller
{
    /**
     * Exibe a agenda consolidada de cultos e eventos.
     */
    public function index(Request $request)
    {
        $congregacao = app('congregacao');
        $agora = Carbon::now();
        $perPage = 10;
        $page = max((int) $request->input('page', 1), 1);

        $eventos = Evento::query()
            ->where('congregacao_id', $congregacao->id)
            ->whereNotNull('data_inicio')
            ->where(function ($query) use ($agora) {
                $query->whereDate('data_inicio', '>=', $agora->toDateString())
                    ->orWhere(function ($inner) use ($agora) {
                        $inner->whereDate('data_inicio', '<', $agora->toDateString())
                            ->whereNotNull('data_encerramento')
                            ->whereDate('data_encerramento', '>=', $agora->toDateString());
                    });
            })
            ->orderBy('data_inicio')
            ->get([
                'id',
                'titulo',
                'descricao',
                'data_inicio',
                'data_encerramento',
                'local',
                'requer_inscricao',
            ])
            ->map(function (Evento $evento) {
                $inicio = Carbon::parse($evento->data_inicio);
                $fim = $evento->data_encerramento ? Carbon::parse($evento->data_encerramento) : null;

                return [
                    'id' => $evento->id,
                    'tipo' => 'evento',
                    'tipo_label' => __('Eventos'),
                    'titulo' => $evento->titulo,
                    'descricao' => $evento->descricao,
                    'inicio' => $inicio,
                    'fim' => $fim,
                    'local' => $evento->local,
                    'requer_inscricao' => (bool) $evento->requer_inscricao,
                    'modal_url' => route('programacoes.eventos.show', $evento, false),
                ];
            })
            ->values()
            ->toBase();

        $cultos = Culto::query()
            ->with('preletor')
            ->where('congregacao_id', $congregacao->id)
            ->whereNotNull('data_culto')
            ->where('data_culto', '>=', $agora)
            ->orderBy('data_culto')
            ->get([
                'id',
                'data_culto',
                'preletor_id',
                'preletor_externo',
                'tema_sermao',
                'texto_base',
                'observacoes',
                'evento_id',
            ])
            ->map(function (Culto $culto) {
                $inicio = Carbon::parse($culto->data_culto);
                $preletorLabel = optional($culto->preletor)->nome ?: $culto->preletor_externo;

                return [
                    'id' => $culto->id,
                    'tipo' => 'culto',
                    'tipo_label' => __('Cultos'),
                    'titulo' => $culto->tema_sermao ?: __('Culto Especial'),
                    'descricao' => $culto->observacoes,
                    'inicio' => $inicio,
                    'fim' => null,
                    'local' => null,
                    'requer_inscricao' => false,
                    'modal_url' => route('programacoes.cultos.show', $culto, false),
                    'preletor' => $preletorLabel,
                    'texto_base' => $culto->texto_base,
                ];
            })
            ->values()
            ->toBase();

        $programacoes = $eventos
            ->merge($cultos)
            ->sortBy(fn ($item) => $item['inicio'])
            ->values();

        $total = $programacoes->count();
        $items = $programacoes
            ->slice(($page - 1) * $perPage, $perPage)
            ->values();

        $paginator = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => route('programacoes.painel'),
                'query' => $request->query(),
            ]
        );

        $paginator->appends($request->except('page'));

        return view('programacoes.painel', [
            'programacoes' => $paginator,
        ]);
    }

    /**
     * Visualização detalhada de um evento.
     */
    public function showEvento(Evento $evento)
    {
        $congregacao = app('congregacao');

        abort_if($evento->congregacao_id !== $congregacao->id, 404);

        $evento->loadMissing('grupo');

        return view('programacoes.includes.evento_detalhes', [
            'evento' => $evento,
        ]);
    }

    /**
     * Visualização detalhada de um culto.
     */
    public function showCulto(Culto $culto)
    {
        $congregacao = app('congregacao');

        abort_if($culto->congregacao_id !== $congregacao->id, 404);

        $culto->loadMissing(['evento', 'preletor']);
        $culto->preletor_label = optional($culto->preletor)->nome ?: $culto->preletor_externo;

        return view('programacoes.includes.culto_detalhes', [
            'culto' => $culto,
        ]);
    }
}
