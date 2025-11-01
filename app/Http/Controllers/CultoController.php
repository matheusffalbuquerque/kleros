<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Culto;
use App\Models\Evento;
use App\Models\SituacaoVisitante;
use App\Models\Visitante;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CultoController extends Controller
{
    public function index() {

        $congregacaoId = app('congregacao')->id;

        $cultos = Culto::where('congregacao_id', $congregacaoId)
            ->whereDate('data_culto', '<', date('Y-m-d'))
            ->paginate(10);
        
        if($cultos->isEmpty()){
            $cultos = '';
        }

        return view('cultos/historico', ['cultos' => $cultos]);
    }

    public function create() {
        $congregacao = app('congregacao');

        $cultos = Culto::where('congregacao_id', $congregacao->id)
            ->whereDate('data_culto', '>', date('Y/m/d'))
            ->orderBy('data_culto', 'asc')
            ->limit(4)
            ->get();
        $cultos = $cultos->isEmpty() ? '' : $cultos;

        $eventos = Evento::all();

        return view('cultos/checkin', ['cultos' => $cultos, 'eventos' => $eventos, 'congregacao' => $congregacao]);
    }

    public function agenda() {

        $congregacao = app('congregacao');
        $congregacaoId = $congregacao->id;

        $cultos = Culto::where('congregacao_id', $congregacaoId)
            ->whereDate('data_culto', '>=', date('Y-m-d'))
            ->orderBy('data_culto')
            ->paginate(10);

        $eventosFiltro = Evento::where('congregacao_id', $congregacaoId)
            ->whereDate('data_inicio', '>=', date('Y-m-d'))
            ->orderBy('titulo')
            ->get(['id', 'titulo']);

        $preletores = Culto::where('congregacao_id', $congregacaoId)
            ->whereNotNull('preletor')
            ->distinct()
            ->orderBy('preletor')
            ->pluck('preletor');

        return view('cultos/agenda', [
            'cultos' => $cultos->isEmpty() ? '' : $cultos,
            'eventosFiltro' => $eventosFiltro,
            'preletores' => $preletores,
            'congregacao' => $congregacao,
        ]);
    }

    public function store(Request $request) {

        $culto = new Culto;

        $culto->data_culto = $request->data_culto . ' ' . $request->horario_inicio;
        $culto->preletor = $request->preletor;
        $culto->quant_visitantes = $request->quantidade_visitantes ?? 0;
        $culto->evento_id = $request->evento_id;
        $culto->tema_sermao = $request->tema_sermao ?? null;
        $culto->texto_base = $request->texto_base ?? null;
        $culto->quant_adultos = $request->quantidade_adultos ?? 0;
        $culto->quant_criancas = $request->quantidade_criancas ?? 0;
        $culto->observacoes = $request->observacoes ?? null;
        $culto->congregacao_id = app('congregacao')->id;

        $culto->save();

        $cultoDateTime = Carbon::parse($culto->data_culto);
        $data_formatada = $cultoDateTime->format('d/m');

        $message = $cultoDateTime->isPast()
            ? 'Registro de culto salvo com sucesso.'
            : "Um novo culto foi agendado para o dia {$data_formatada}.";

        return redirect()->to(url()->previous())->with('msg', $message);

    }

    public function search(Request $request) {

        $origin = $request->origin;
        $congregacaoId = app('congregacao')->id;

        $query = Culto::with('evento')
            ->where('congregacao_id', $congregacaoId);

        if ($origin === 'historico') {
            $query->whereDate('data_culto', '<=', date('Y-m-d'));

            if ($request->filled('data_inicial')) {
                $query->whereDate('data_culto', '>=', $request->input('data_inicial'));
            }

            if ($request->filled('data_final')) {
                $query->whereDate('data_culto', '<=', $request->input('data_final'));
            }
        } else {
            $query->whereDate('data_culto', '>=', date('Y-m-d'));

            if ($request->filled('preletor')) {
                $query->where('preletor', $request->input('preletor'));
            }

            if ($request->filled('evento')) {
                $query->where('evento_id', $request->input('evento'));
            }
        }

        $cultosCollection = $query->orderBy('data_culto')->get();
        $cultos = $cultosCollection->isEmpty() ? '' : $cultosCollection;

        $view = view('cultos/cultos_search', ['cultos' => $cultos, 'origin' => $origin])->render();

        return response()->json(['view' => $view]);
    }

    public function complete($id) {
        

        if($id == 'adicionar'){
            $culto = null;
        } else {
            $culto = Culto::findOrFail($id);
        }

        $congregacao = app('congregacao');
        $eventos = Evento::all();

        return view('cultos/checkout', ['culto' => $culto, 'eventos' => $eventos, 'congregacao' => $congregacao]);
    }

    public function update(Request $request, $id){

        $culto = Culto::findOrFail($id);
        
        $culto->congregacao_id = app('congregacao')->id;
        $culto->data_culto = $request->data_culto . ' ' . $request->horario_inicio;
        $culto->preletor = $request->preletor;
        $culto->quant_visitantes = $request->quantidade_visitantes ?? 0;
        $culto->evento_id = $request->evento_id;
        $culto->tema_sermao = $request->tema_sermao ?? null;
        $culto->texto_base = $request->texto_base ?? null;
        $culto->quant_adultos = $request->quantidade_adultos ?? 0;
        $culto->quant_criancas = $request->quantidade_criancas ?? 0;
        $culto->observacoes = $request->observacoes ?? null;

        $culto->save();

        return redirect()->to(url()->previous())->with('msg', 'Registro de culto atualizado com sucesso.');

    }

    public function form_criar(){
        $eventos = Evento::all();
        return view('cultos/includes/form_criar', ['eventos' => $eventos]);
    }

    public function form_editar($id){
        $culto = Culto::with(['escalas.tipo', 'escalas.itens.membro'])->findOrFail($id);
        $culto->escalas = $culto->escalas->sortBy('data_hora')->values();
        $eventos = Evento::all();
        return view('cultos/includes/form_editar', ['culto' => $culto, 'eventos' => $eventos]);
    }

    public function destroy($id){
        $culto = Culto::findOrFail($id);
        $culto->delete();

        return redirect()->to(url()->previous())->with('msg', 'Registro de culto excluído com sucesso.');
    }

    public function painel(Request $request)
    {
        $congregacao = app('congregacao');
        $selectedDateInput = $request->input('data');

        try {
            $selectedDate = $selectedDateInput
                ? Carbon::parse($selectedDateInput)->format('Y-m-d')
                : Carbon::today()->format('Y-m-d');
        } catch (\Throwable $exception) {
            $selectedDate = Carbon::today()->format('Y-m-d');
        }

        $culto = Culto::where('congregacao_id', $congregacao->id)
            ->whereDate('data_culto', $selectedDate)
            ->orderByDesc('data_culto')
            ->first();

        $eventos = Evento::where('congregacao_id', $congregacao->id)
            ->orderBy('titulo')
            ->get();

        $situacoesVisitantes = SituacaoVisitante::orderBy('titulo')->get();

        $visitantesDia = Visitante::with('sit_visitante')
            ->where('congregacao_id', $congregacao->id)
            ->whereDate('data_visita', $selectedDate)
            ->orderBy('nome')
            ->get();

        $visitasPorPessoa = Visitante::where('congregacao_id', $congregacao->id)
            ->select('nome', 'telefone', DB::raw('COUNT(*) as total'))
            ->groupBy('nome', 'telefone')
            ->get()
            ->mapWithKeys(function ($item) {
                $key = mb_strtolower(sprintf('%s|%s', $item->nome, $item->telefone));
                return [$key => (int) $item->total];
            });

        $visitantesDia = $visitantesDia->map(function (Visitante $visitante) use ($visitasPorPessoa) {
            $key = mb_strtolower(sprintf('%s|%s', $visitante->nome, $visitante->telefone));
            $visitante->visit_count = $visitasPorPessoa[$key] ?? 1;
            return $visitante;
        });

        $dashboard = trans('dashboard');
        $dashboardCards = data_get($dashboard, 'general.cards', []);
        $dashboardDays = data_get($dashboard, 'days', []);
        $carbonLocale = str_replace('-', '_', app()->getLocale());
        Carbon::setLocale($carbonLocale);

        $selectedCarbon = Carbon::parse($selectedDate);
        $horarioInicio = $culto ? Carbon::parse($culto->data_culto)->format('H:i') : '';
        $dataCulto = $culto ? Carbon::parse($culto->data_culto)->format('Y-m-d') : $selectedDate;
        $selectedDateFull = $selectedCarbon->format('d/m/Y');
        $dashboardDayName = $dashboardDays[$selectedCarbon->dayOfWeek] ?? $selectedCarbon->translatedFormat('l');
        $panelUrl = route('cultos.painel', ['data' => $selectedDate]);

        return view('cultos/painel', [
            'culto' => $culto,
            'eventos' => $eventos,
            'situacoesVisitantes' => $situacoesVisitantes,
            'visitantesDia' => $visitantesDia,
            'selectedDate' => $selectedDate,
            'horarioInicio' => $horarioInicio,
            'dataCulto' => $dataCulto,
            'panelUrl' => $panelUrl,
            'congregacao' => $congregacao,
            'dashboardCards' => $dashboardCards,
            'dashboardDayName' => $dashboardDayName,
            'selectedDateFull' => $selectedDateFull,
        ]);
    }
}
