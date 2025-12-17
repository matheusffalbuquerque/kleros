<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agrupamento;
use App\Models\Celula;
use App\Models\Culto;
use App\Models\Evento;
use App\Models\Ministerio;
use App\Models\Reuniao;
use App\Models\Visitante;
use Illuminate\Support\Facades\Cache;
use App\Models\Pesquisa;
use App\Models\Setor;
use App\Models\TipoEscala;
use App\Models\Caixa;
use App\Models\TipoContribuicao;
use App\Models\TipoLancamento;
use App\Models\CultoCategoria;

class CadastroController extends Controller
{
    public function index() {

        $congregacao = app('congregacao');
        $congregacaoId = $congregacao->id;
        $now = now();

        //Esta parte verifica se há cultos cadastrados para os próximos dias
        $cultos = Culto::with(['preletor', 'evento'])
            ->where('congregacao_id', $congregacaoId)
            ->whereDate('data_culto', '>', $now->toDateString())
            ->orderBy('data_culto', 'asc')
            ->limit(3)
            ->get()
            ->map(function (Culto $culto) {
                $culto->preletor_label = optional($culto->preletor)->nome
                    ?: ($culto->preletor_externo ?? $culto->preletor);
                return $culto;
            });
        
        //Esta parte verifica se há eventos cadastrados para os próximos dias
        $eventos = Evento::where('congregacao_id', $congregacaoId)
            ->where('recorrente', false)
            ->whereDate('data_inicio', '>', $now->toDateString())
            ->orderBy('data_inicio', 'asc')
            ->limit(4)
            ->get();

        //Reuniões
        $reunioes = Reuniao::where('congregacao_id', $congregacaoId)
            ->where('data_inicio', '>=', $now)
            ->orderBy('data_inicio', 'asc')
            ->limit(3)
            ->get();

        $cursos = collect();
        if (module_enabled('cursos') && class_exists(\Modules\Cursos\Models\Curso::class)) {
            $cursos = \Modules\Cursos\Models\Curso::where('ativo', true)
                ->where('congregacao_id', $congregacaoId)
                ->orderBy('titulo')
                ->get();
        }

        $caixas = Caixa::where('congregacao_id', $congregacaoId)
            ->with(['lancamentos' => fn ($query) => $query->orderByDesc('data_lancamento')->orderByDesc('created_at')])
            ->orderBy('nome')
            ->get();

        $caixas->each(function ($caixa) {
            $entradas = $caixa->lancamentos->where('tipo', 'entrada')->sum('valor');
            $saidas = $caixa->lancamentos->where('tipo', 'saida')->sum('valor');

            $caixa->entradas_total = $entradas;
            $caixa->saidas_total = $saidas;
            $caixa->saldo_atual = $entradas - $saidas;
        });

        $tiposLancamento = TipoLancamento::where('congregacao_id', $congregacaoId)
            ->orderBy('nome')
            ->get();

        $cultoCategorias = CultoCategoria::orderBy('nome')->get();

        /*Essa parte verifica o tal de visitantes do mês, se não houver ele receberá uma string vazia*/
        $visitantes_mes = Visitante::where('congregacao_id', $congregacaoId)
            ->whereMonth('data_visita', $now->month)
            ->whereYear('data_visita', $now->year)
            ->count();

        $ministerios = Ministerio::where('denominacao_id', $congregacao->denominacao_id)
            ->orderBy('titulo')
            ->get();

        $grupos = Agrupamento::where('congregacao_id', $congregacaoId)
            ->where('tipo', 'grupo')
            ->get();
        $departamentos = Agrupamento::where('congregacao_id', $congregacaoId)
            ->where('tipo', 'departamento')
            ->get();
        $setores = Setor::where('congregacao_id', $congregacaoId)
            ->orderBy('nome')
            ->with('departamentos')
            ->get();
        $celulas = Celula::where('congregacao_id', $congregacaoId)->get();

        $pesquisas = Pesquisa::with('criador')
            ->forCongregacao($congregacaoId)
            ->where(function ($query) use ($now) {
                $query->whereNull('data_fim')
                    ->orWhereDate('data_fim', '>=', $now->toDateString());
            })
            ->orderByDesc('data_inicio')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        $tiposEscala = TipoEscala::where('congregacao_id', $congregacaoId)->orderBy('nome')->get();

        $noticias = Cache::get('noticias_feed') ?? [];
        $destaques = array_slice($noticias['guiame'] ?? [], 0, 9);

        return view('/cadastros', [
            'eventos' => $eventos,
            'grupos' => $grupos,
            'ministerios' => $ministerios,
            'cultos' => $cultos,
            'visitantes_total' => $visitantes_mes,
            'reunioes' => $reunioes,
            'cursos' => $cursos,
            'congregacao' => $congregacao,
            'departamentos' => $departamentos,
            'setores' => $setores,
            'celulas' => $celulas,
            'pesquisas' => $pesquisas,
            'destaques' => $destaques,
            'tiposEscala' => $tiposEscala,
            'caixas' => $caixas,
            'tiposLancamento' => $tiposLancamento,
            'cultoCategorias' => $cultoCategorias,
        ]);
    }
}
