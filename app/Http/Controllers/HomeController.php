<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Culto;
use App\Models\Evento;
use App\Models\Membro;
use App\Models\Recado;
use App\Models\Visitante;
use App\Models\Agrupamento;
use App\Models\Celula;
use App\Models\Setor;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    private $congregacao;

    public function __construct() {
        $this->middleware('auth')->except(['login', 'authenticate']);
        $this->congregacao = app('congregacao');
    }

    public function login() {

        // Verifica se a aplicação está rodando em modo admin
        if (!app('site_publico') && !app('modo_admin')) {
            $this->middleware('auth')->except(['login', 'authenticate']);
        }

        return view('login', ['congregacao' => $this->congregacao]);
    }

    public function authenticate(Request $request) {
        $credentials = $request->only('name', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->put('congregacao_id', Auth::user()->congregacao_id);
            $request->session()->put('user_id', Auth::user()->id);
            $request->session()->put('membro_id', Auth::user()->membro_id);
            $request->session()->regenerate();
            return redirect()->route('index');
        }

        return redirect()
            ->back()
            ->withInput($request->only('name'))
            ->withErrors(['user' => 'Usuário ou senha inválidos. Tente novamente.']);
    }

    public function index() {

        Log::debug('Entrou no index', [
            'host' => request()->getHost(),
            'site_publico' => app('site_publico'),
            'auth_middleware_aplicado' => in_array('auth', array_keys(app('router')->getMiddleware())),
            'usuario_logado' => Auth::check(),
        ]);

        if (app('modo_admin')) {
            // Painel geral da plataforma
            return view('admin.dashboard');
        }

        if (app('site_publico')) {
            // Painel geral da plataforma
            return redirect()->route('site.home');
        }
        
        $congregacao = app('congregacao');
        $usuario = Auth::user();

        /*Esta parte pega informações do culto e verifica se existe um cadastro realizado para o dia atual

            Se não houver ele envia uma informação vazia, liberando uma mensagem e link para cadastro.
        */
        $culto_hoje = Culto::where('data_culto', date('Y/m/d'))->get();
        
        if($culto_hoje->isEmpty()) {
            $culto_hoje = '';
        }
        
        /*Esta parte verifica se há recados do dia de hoje

            Se não houver ele envia uma informação vazia, com mensagem sobre a ausencia de mensagens.
        */
        $recados = '';

        if (module_enabled('recados')) {
            $recadosCollection = Recado::where('data_recado', date('Y/m/d'))->get();

            if ($recadosCollection->isNotEmpty()) {
                $recados = $recadosCollection;
            }
        }

        /*Esta parte verifica se há eventos cadastrados para os próximos dias

            Se não houver ele envia uma informação vazia, com mensagem sobre a ausencia de eventos.
        */
        
        $eventos = Evento::where('recorrente', false)->whereDate('data_inicio', '>', date('Y/m/d'))->limit(4)->orderBy('data_inicio', 'asc')->get();
        
        if($eventos->isEmpty()) {
            $eventos = '';
        }

        /*Esta parte verifica se há visitantes já cadastrados

            Se não houver ele envia uma informação vazia, com mensagem sobre a ausencia de visitantes.
        */

        $visitantes = Visitante::where('congregacao_id', $congregacao->id)->whereDate('data_visita', date("Y/m/d"))->get();

        if($visitantes->isEmpty()) {
            $visitantes = '';
        }

        $visitas_count = Visitante::where('congregacao_id', $congregacao->id)
            ->whereMonth('data_visita', Carbon::now()->month)
            ->whereYear('data_visita', Carbon::now()->year)
            ->count();

        /*Esta parte verifica se há membros fazendo aniversário neste mês

            Se não houver ele envia uma informação vazia, com mensagem sobre a ausencia de aniversariantes.
        */

        // Busca aniversariantes do mês atual ordenados a partir do dia de hoje
        $hoje = Carbon::now();
        $diaAtual = $hoje->day;
        
        $aniversariantes = Membro::DaCongregacao()
            ->whereMonth('data_nascimento', $hoje->month)
            ->orderByRaw("DAY(data_nascimento) >= ? DESC, DAY(data_nascimento) ASC", [$diaAtual])
            ->get();

        if($aniversariantes->isEmpty()) {
            $aniversariantes = '';
        }

        $membros_count = Membro::where('congregacao_id', $congregacao->id)
            ->where('ativo', true)
            ->count();

        $membrosTotal = Membro::where('congregacao_id', $congregacao->id)->count();
        $membrosNovosMes = Membro::where('congregacao_id', $congregacao->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $membrosEmGrupos = DB::table('agrupamentos_membros')
            ->join('agrupamentos', 'agrupamentos.id', '=', 'agrupamentos_membros.agrupamento_id')
            ->where('agrupamentos.congregacao_id', $congregacao->id)
            ->where('agrupamentos.tipo', 'grupo')
            ->distinct('agrupamentos_membros.membro_id')
            ->count('agrupamentos_membros.membro_id');

        $visitantesHistorico = Visitante::where('congregacao_id', $congregacao->id)->count();

        $gruposCount = Agrupamento::where('congregacao_id', $congregacao->id)
            ->where('tipo', 'grupo')
            ->count();
        $celulasCount = Celula::where('congregacao_id', $congregacao->id)->count();
        $departamentosCount = Agrupamento::where('congregacao_id', $congregacao->id)
            ->where('tipo', 'departamento')
            ->count();
        $setoresCount = Setor::where('congregacao_id', $congregacao->id)->count();

        $gruposDestaque = Agrupamento::where('congregacao_id', $congregacao->id)
            ->where('tipo', 'grupo')
            ->withCount('integrantes')
            ->orderByDesc('integrantes_count')
            ->take(6)
            ->get();

        $dashboardStats = [
            'membros_total' => $membrosTotal,
            'membros_ativos' => $membros_count,
            'membros_novos' => $membrosNovosMes,
            'membros_em_grupos' => $membrosEmGrupos,
            'membros_sem_grupo' => max($membros_count - $membrosEmGrupos, 0),
            'visitantes_mes' => $visitas_count,
            'visitantes_total' => $visitantesHistorico,
            'grupos_total' => $gruposCount,
            'celulas_total' => $celulasCount,
            'departamentos_total' => $departamentosCount,
            'setores_total' => $setoresCount,
            'cultos_proximos' => is_countable($culto_hoje) ? count($culto_hoje) : 0,
            'eventos_proximos' => is_countable($eventos) ? count($eventos) : 0,
        ];

        return view('home', [
            'visitantes' => $visitantes,
            'culto_hoje' => $culto_hoje,
            'recados' => $recados,
            'eventos' => $eventos,
            'aniversariantes' => $aniversariantes,
            'congregacao' => $congregacao,
            'usuario' => $usuario,
            'dashboardStats' => $dashboardStats,
            'gruposDestaque' => $gruposDestaque,
        ]);
    }
}
