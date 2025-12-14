<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Culto;
use App\Models\Evento;
use App\Models\Agrupamento;
use App\Models\EventoOcorrencia;
use Illuminate\Http\Request;
use DateTime;

class EventoController extends Controller
{
    private $congregacao;

    public function __construct()
    {
        $this->congregacao = app('congregacao');
    }


    public function index() {

        $eventos = Evento::whereDate('data_inicio', '<', date('Y-m-d'))->paginate(10);

        return view('eventos/historico', ['eventos' => $eventos]);
    }

    public function agenda() {
        $congregacao = app('congregacao');
        $congregacaoId = $congregacao->id;

        $eventos = Evento::with('grupo')
            ->where('congregacao_id', $congregacaoId)
            ->where('recorrente', false)
            ->whereDate('data_inicio', '>=', date('Y-m-d'))
            ->orderBy('data_inicio')
            ->paginate(10);

        $titulosFiltro = Evento::where('congregacao_id', $congregacaoId)
            ->where('recorrente', false)
            ->whereDate('data_inicio', '>=', date('Y-m-d'))
            ->orderBy('titulo')
            ->distinct()
            ->pluck('titulo');

        $grupos = Agrupamento::where('congregacao_id', $congregacaoId)
            ->where('tipo', 'grupo')
            ->orderBy('nome')
            ->get();

        return view('eventos/agenda', [
            'eventos' => $eventos,
            'titulosFiltro' => $titulosFiltro,
            'grupos' => $grupos,
            'congregacao' => $congregacao,
        ]);
    }

    public function create() {
        $grupos = Agrupamento::where('tipo', 'grupo')->get();

        return view('eventos/cadastro', ['grupos' => $grupos]);
    }

    public function store(Request $request) {

        $evento = new Evento;

        $request->validate([
            'titulo' => 'required',
            'ocorrencias' => 'required|array|min:1',
            'ocorrencias.*.data_ocorrencia' => 'required|date'
        ],[
            'titulo.required' => 'O título é obrigatório',
            'ocorrencias.required' => 'É necessário adicionar ao menos uma ocorrência',
            'ocorrencias.*.data_ocorrencia.required' => 'Cada ocorrência deve ter uma data'
        ]);

        // Calcula data_inicio e data_encerramento a partir das ocorrências
        $datas = collect($request->input('ocorrencias', []))
            ->pluck('data_ocorrencia')
            ->filter()
            ->sort();

        if ($datas->isEmpty()) {
            return back()->with('msg-error', 'É necessário adicionar ao menos uma ocorrência ao evento.');
        }

        $evento->congregacao_id = $this->congregacao->id;
        $evento->titulo = $request->titulo;
        $evento->agrupamento_id = $request->grupo_id;
        $evento->descricao = $request->descricao;
        $evento->recorrente = $request->evento_recorrente == "1" ? true : false;
        $geracao_cultos = $request->geracao_cultos == "1" ? true : false;
        $evento->local = $request->local;
        $evento->requer_inscricao = $request->requer_inscricao == "1" ? true : false;
        
        // Define data_inicio como a menor data e data_encerramento como a maior
        $evento->data_inicio = $datas->first();
        $evento->data_encerramento = $datas->last();
            
        if($evento->save()) {
            // Salva ocorrências do cronograma
            $ocorrencias = collect($request->input('ocorrencias', []))
                ->filter(fn ($item) => !empty($item['data_ocorrencia']))
                ->map(fn ($item) => [
                    'data_ocorrencia' => $item['data_ocorrencia'],
                    'horario_inicio' => $item['horario_inicio'] ?? null,
                    'descricao' => $item['descricao'] ?? null,
                    'local' => $item['local'] ?? null,
                    'culto_id' => null,
                ]);

            if ($ocorrencias->isNotEmpty()) {
                $evento->ocorrencias()->createMany($ocorrencias->toArray());
            }

            if(!$evento->recorrente && $geracao_cultos){
                $startDate = $evento->data_inicio;                
                $finalDate = $evento->data_encerramento;

                //Calcula quantos dias tem o evento
                $datas = pegarDiasDeIntervaloDatas($startDate, $finalDate);
                
                foreach ($datas as $dia) {
                    $culto = new Culto();
                    
                    $culto->congregacao_id = $this->congregacao->id;
                    $culto->data_culto = $dia;
                    $culto->preletor = "A definir";
                    $culto->quant_visitantes = 0;
                    $culto->evento_id = $evento->id;
                    
                    $culto->save();
                }
            }
        }
        
        // Se for requisição AJAX, retorna JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Evento criado com sucesso!',
                'evento' => [
                    'id' => $evento->id,
                    'titulo' => $evento->titulo,
                    'data_inicio' => $evento->data_inicio,
                    'data_encerramento' => $evento->data_encerramento,
                ],
                'data' => [
                    'id' => $evento->id,
                    'titulo' => $evento->titulo,
                    'data_inicio' => $evento->data_inicio,
                    'data_encerramento' => $evento->data_encerramento,
                ]
            ]);
        }
        
        return redirect()->back()->with('msg', 'Um novo evento foi agendado.');
    }

    public function search(Request $request) {

        $origin = $request->input('origin');

        $query = Evento::with('grupo')
            ->where('congregacao_id', $this->congregacao->id)
            ->where('recorrente', false);

        if ($origin === 'historico') {
            $query->whereDate('data_inicio', '<', now()->toDateString());

            if ($request->filled('data_inicial')) {
                $query->whereDate('data_inicio', '>=', $request->input('data_inicial'));
            }

            if ($request->filled('data_final')) {
                $query->whereDate('data_inicio', '<=', $request->input('data_final'));
            }
        } else {
            // agenda padrão: eventos futuros
            $query->whereDate('data_inicio', '>=', now()->toDateString());

            if ($request->filled('titulo')) {
                $query->where('titulo', $request->input('titulo'));
            }

            if ($request->filled('grupo')) {
                $query->where('agrupamento_id', $request->input('grupo'));
            }
        }

        $eventosCollection = $query->orderBy('data_inicio')->get();
        $eventos = $eventosCollection->isEmpty() ? '' : $eventosCollection;

        $view = view('eventos/eventos_search', ['eventos' => $eventos, 'origin' => $origin])->render();

        return response()->json(['view' => $view]);
    }

    public function form_criar(){
        $grupos = Agrupamento::where('congregacao_id', app('congregacao')->id)->where('tipo', 'grupo')->get();
        return view('eventos/includes/form_criar', ['grupos' => $grupos]);
    }

    public function form_editar($id){
        $evento = Evento::with('ocorrencias')->findOrFail($id);
        $grupos = Agrupamento::where('congregacao_id', $this->congregacao->id)
            ->where('tipo', 'grupo')
            ->orderBy('nome')
            ->get();
        return view('eventos/includes/form_editar', ['evento' => $evento, 'grupos' => $grupos]);
    }

    public function update(Request $request, $id)
    {
        $evento = Evento::where('congregacao_id', $this->congregacao->id)->findOrFail($id);

        $request->validate([
            'titulo' => 'required',
            'ocorrencias' => 'required|array|min:1',
            'ocorrencias.*.data_ocorrencia' => 'required|date'
        ], [
            'titulo.required' => 'O título é obrigatório',
            'ocorrencias.required' => 'É necessário ter ao menos uma ocorrência',
            'ocorrencias.*.data_ocorrencia.required' => 'Cada ocorrência deve ter uma data'
        ]);

        $evento->titulo = $request->titulo;
        $evento->agrupamento_id = $request->grupo_id ?: null;
        $evento->descricao = $request->descricao;
        $evento->local = $request->local;
        $evento->requer_inscricao = $request->requer_inscricao == "1" ? true : false;

        // Coleta os IDs das ocorrências enviadas no request
        $idsEnviados = collect($request->input('ocorrencias', []))
            ->filter(fn ($item) => !empty($item['id']))
            ->pluck('id')
            ->toArray();

        // Remove ocorrências que não estão mais no request
        EventoOcorrencia::where('evento_id', $evento->id)
            ->whereNotIn('id', $idsEnviados)
            ->delete();

        // Atualiza/insere ocorrências do cronograma
        collect($request->input('ocorrencias', []))
            ->filter(fn ($item) => !empty($item['data_ocorrencia']))
            ->each(function ($item) use ($evento) {
                $payload = [
                    'data_ocorrencia' => $item['data_ocorrencia'],
                    'horario_inicio' => $item['horario_inicio'] ?? null,
                    'descricao' => $item['descricao'] ?? null,
                    'local' => $item['local'] ?? null,
                ];

                if (!empty($item['id'])) {
                    EventoOcorrencia::where('evento_id', $evento->id)
                        ->where('id', $item['id'])
                        ->update($payload);
                } else {
                    $evento->ocorrencias()->create($payload);
                }
            });

        // Recalcula data_inicio e data_encerramento baseado nas ocorrências
        $datas = $evento->ocorrencias()
            ->pluck('data_ocorrencia')
            ->filter()
            ->sort();

        if ($datas->isNotEmpty()) {
            $evento->data_inicio = $datas->first();
            $evento->data_encerramento = $datas->last();
        }

        $evento->save();

        return redirect()->back()->with('msg', 'Evento atualizado com sucesso.');
    }

    public function destroy($id)
    {
        try {
            $evento = Evento::findOrFail($id);
            
            // Remove todas as ocorrências associadas
            $evento->ocorrencias()->delete();
            
            // Remove o evento
            $evento->delete();
            
            return redirect()->back()->with('msg', 'Evento excluído com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->with('msg-error', 'Erro ao excluir evento: ' . $e->getMessage());
        }
    }
}
