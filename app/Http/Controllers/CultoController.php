<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Culto;
use App\Models\CultoCategoria;
use App\Models\Evento;
use App\Models\Membro;
use App\Models\SituacaoVisitante;
use App\Models\Visitante;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class CultoController extends Controller
{
    public function index() {

        $cultos = $this->buildHistoricoCultosQuery()
            ->paginate(10);

        $cultos->getCollection()->transform(function (Culto $culto) {
            $culto->preletor_label = optional($culto->preletor)->nome ?: $culto->preletor_externo;
            return $culto;
        });
        
        if($cultos->isEmpty()){
            $cultos = '';
        }

        return view('cultos/historico', ['cultos' => $cultos]);
    }

    public function imprimirHistorico(Request $request)
    {
        $congregacao = app('congregacao')->loadMissing('config');
        $dataInicial = $request->input('data_inicial');
        $dataFinal = $request->input('data_final');

        $cultos = $this->buildHistoricoCultosQuery($dataInicial, $dataFinal)
            ->get()
            ->map(function (Culto $culto) {
                $culto->preletor_label = optional($culto->preletor)->nome ?: $culto->preletor_externo ?: 'Nao informado';
                $culto->categoria_label = optional($culto->categoria)->nome ?: 'Regular';
                $culto->evento_label = optional($culto->evento)->titulo ?: 'Nenhum';
                $culto->publico_total = (int) ($culto->quant_adultos ?? 0) + (int) ($culto->quant_criancas ?? 0) + (int) ($culto->quant_visitantes ?? 0);

                return $culto;
            });

        $periodo = match (true) {
            $dataInicial && $dataFinal => 'De ' . Carbon::parse($dataInicial)->format('d/m/Y') . ' ate ' . Carbon::parse($dataFinal)->format('d/m/Y'),
            $dataInicial => 'A partir de ' . Carbon::parse($dataInicial)->format('d/m/Y'),
            $dataFinal => 'Ate ' . Carbon::parse($dataFinal)->format('d/m/Y'),
            default => 'Todo o historico de cultos',
        };

        $logoDataUri = $this->resolveCongregacaoLogoDataUri($congregacao);

        $totalCultos = $cultos->count();

        $resumo = [
            'total_cultos' => $totalCultos,
            'adultos_media' => $totalCultos ? round($cultos->avg(fn (Culto $culto) => (int) ($culto->quant_adultos ?? 0)), 1) : 0,
            'criancas_media' => $totalCultos ? round($cultos->avg(fn (Culto $culto) => (int) ($culto->quant_criancas ?? 0)), 1) : 0,
            'visitantes_media' => $totalCultos ? round($cultos->avg(fn (Culto $culto) => (int) ($culto->quant_visitantes ?? 0)), 1) : 0,
            'publico_total_media' => $totalCultos ? round($cultos->avg(fn (Culto $culto) => (int) $culto->publico_total), 1) : 0,
        ];

        return Pdf::view('cultos.relatorios.historico_pdf', [
            'congregacao' => $congregacao,
            'cultos' => $cultos,
            'periodo' => $periodo,
            'dataInicial' => $dataInicial,
            'dataFinal' => $dataFinal,
            'resumo' => $resumo,
            'logoDataUri' => $logoDataUri,
            'geradoEm' => now(),
        ])
            ->format('A4')
            ->name('relatorio-cultos.pdf');
    }

    public function create() {
        $congregacao = app('congregacao');

        $cultos = Culto::where('congregacao_id', $congregacao->id)
            ->whereDate('data_culto', '>', date('Y/m/d'))
            ->orderBy('data_culto', 'asc')
            ->limit(4)
            ->get();
        $cultos = $cultos->isEmpty() ? '' : $cultos;

        $eventos = Evento::where('congregacao_id', $congregacao->id)
            ->orderBy('titulo')
            ->get();

        $categorias = CultoCategoria::where('congregacao_id', $congregacao->id)
            ->orderBy('nome')
            ->get();
        $membros = Membro::orderBy('nome')->get();

        return view('cultos/checkin', [
            'cultos' => $cultos,
            'eventos' => $eventos,
            'categorias' => $categorias,
            'membros' => $membros,
            'congregacao' => $congregacao
        ]);
    }

    public function agenda() {

        $congregacao = app('congregacao');
        $congregacaoId = $congregacao->id;

        $cultos = Culto::with(['preletor', 'evento'])
            ->where('congregacao_id', $congregacaoId)
            ->whereDate('data_culto', '>=', date('Y-m-d'))
            ->orderBy('data_culto')
            ->paginate(10);

        $cultos->getCollection()->transform(function (Culto $culto) {
            $culto->preletor_label = optional($culto->preletor)->nome ?: $culto->preletor_externo;
            return $culto;
        });

        $eventosFiltro = Evento::where('congregacao_id', $congregacaoId)
            ->whereDate('data_inicio', '>=', date('Y-m-d'))
            ->orderBy('titulo')
            ->get(['id', 'titulo']);

        $categorias = CultoCategoria::where('congregacao_id', $congregacaoId)
            ->orderBy('nome')
            ->get();
        $membros = Membro::orderBy('nome')->get();
        $preletoresMembros = Membro::where('congregacao_id', $congregacaoId)
        ->whereIn('id',
                Culto::where('congregacao_id', $congregacaoId)
                    ->whereNotNull('preletor_id')
                    ->pluck('preletor_id')
            )->pluck('nome');

        $preletoresExternos = Culto::where('congregacao_id', $congregacaoId)
            ->whereNotNull('preletor_externo')
            ->pluck('preletor_externo');

        $preletores = $preletoresMembros
            ->merge($preletoresExternos)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('cultos/agenda', [
            'cultos' => $cultos->isEmpty() ? '' : $cultos,
            'eventosFiltro' => $eventosFiltro,
            'categorias' => $categorias,
            'membros' => $membros,
            'preletores' => $preletores,
            'congregacao' => $congregacao,
        ]);
    }

    public function store(Request $request) {

        $culto = new Culto;

        // Combinar data e horário
        $horario = $request->horario_culto ?? $request->horario_inicio ?? '19:00';
        $culto->data_culto = $request->data_culto . ' ' . $horario;
        
        $preletorId = $request->preletor_id ?: null;
        $preletorExterno = $request->preletor_externo ?: null;

        $categoriaNome = $request->culto_categoria;
        $categoriaId = $request->culto_categoria_id;
        if (! $categoriaId && $categoriaNome) {
            $categoriaId = CultoCategoria::where('nome', $categoriaNome)->value('id');
        }

        $culto->preletor_id = $preletorId;
        $culto->preletor_externo = $preletorId ? null : $preletorExterno;
        $culto->quant_visitantes = $request->quantidade_visitantes ?? 0;
        $culto->evento_id = $request->evento_id;
        $culto->culto_categoria_id = $categoriaId ?: null;
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

        if ($origin === 'historico') {
            $query = $this->buildHistoricoCultosQuery(
                $request->input('data_inicial'),
                $request->input('data_final')
            );
        } else {
            $congregacaoId = app('congregacao')->id;

            $query = Culto::with(['evento', 'preletor'])
                ->where('congregacao_id', $congregacaoId);

            $query->whereDate('data_culto', '>=', date('Y-m-d'));

            if ($request->filled('preletor')) {
                $preletorBusca = $request->input('preletor');
                $query->where(function ($q) use ($preletorBusca) {
                    $q->whereHas('preletor', function ($sub) use ($preletorBusca) {
                        $sub->where('nome', 'like', '%' . $preletorBusca . '%');
                    })->orWhere('preletor_externo', 'like', '%' . $preletorBusca . '%');
                });
            }

            if ($request->filled('evento')) {
                $query->where('evento_id', $request->input('evento'));
            }
        }

        $cultosCollection = $query->get()
            ->map(function (Culto $culto) {
                $culto->preletor_label = optional($culto->preletor)->nome ?: $culto->preletor_externo;
                return $culto;
            });
        $cultos = $cultosCollection->isEmpty() ? '' : $cultosCollection;

        $view = view('cultos/cultos_search', ['cultos' => $cultos, 'origin' => $origin])->render();

        return response()->json(['view' => $view]);
    }

    
    public function update(Request $request, $id){

        $culto = Culto::findOrFail($id);
        
        $culto->congregacao_id = app('congregacao')->id;
        
        // Combinar data e horário
        $horario = $request->horario_culto ?? $request->horario_inicio ?? '19:00';
        $culto->data_culto = $request->data_culto . ' ' . $horario;
        
        $preletorId = $request->preletor_id ?: null;
        $preletorExterno = $request->preletor_externo ?: null;

        $categoriaNome = $request->culto_categoria;
        $categoriaId = $request->culto_categoria_id;
        if (! $categoriaId && $categoriaNome) {
            $categoriaId = CultoCategoria::where('nome', $categoriaNome)->value('id');
        }

        $culto->preletor_id = $preletorId;
        $culto->preletor_externo = $preletorId ? null : $preletorExterno;
        $culto->quant_visitantes = $request->quantidade_visitantes ?? 0;
        $culto->evento_id = $request->evento_id;
        $culto->culto_categoria_id = $categoriaId ?: null;
        $culto->tema_sermao = $request->tema_sermao ?? null;
        $culto->texto_base = $request->texto_base ?? null;
        $culto->quant_adultos = $request->quantidade_adultos ?? 0;
        $culto->quant_criancas = $request->quantidade_criancas ?? 0;
        $culto->observacoes = $request->observacoes ?? null;

        $culto->save();

        return redirect()->to(url()->previous())->with('msg', 'Registro de culto atualizado com sucesso.');

    }

    public function form_criar(){
        $eventos = Evento::where('congregacao_id', app('congregacao')->id)->get();
        $categorias = CultoCategoria::where('congregacao_id', app('congregacao')->id)->orderBy('nome')->get();
        $membros = Membro::where('congregacao_id', app('congregacao')->id)->orderBy('nome')->get();
        return view('cultos/includes/form_criar', [
            'eventos' => $eventos,
            'categorias' => $categorias,
            'membros' => $membros
        ]);
    }

    public function form_editar($id){
        $culto = Culto::with(['escalas.tipo', 'escalas.itens.membro'])->findOrFail($id);
        $culto->escalas = $culto->escalas->sortBy('data_hora')->values();
        $eventos = Evento::where('congregacao_id', $culto->congregacao_id)->get();
        $categorias = CultoCategoria::where('congregacao_id', $culto->congregacao_id)->orderBy('nome')->get();
        $membros = Membro::where('congregacao_id', $culto->congregacao_id)->orderBy('nome')->get();
        return view('cultos/includes/form_editar', [
            'culto' => $culto,
            'eventos' => $eventos,
            'categorias' => $categorias,
            'membros' => $membros
        ]);
    }

    public function destroy(Request $request, $id){
        // Evita exclusões acidentais por requisições incorretas
        if (! $request->isMethod('DELETE')) {
            abort(405);
        }

        $congregacaoId = app('congregacao')->id;

        $culto = Culto::where('congregacao_id', $congregacaoId)->findOrFail($id);
        logger()->warning('[Culto] Exclusão solicitada', [
            'culto_id' => $culto->id,
            'congregacao_id' => $congregacaoId,
            'route' => $request->path(),
            'referer' => $request->headers->get('referer'),
        ]);
        $culto->delete();

        return redirect()->to(url()->previous())->with('msg', 'Registro de culto excluído com sucesso.');
    }

    public function painel(Request $request)
    {
        $congregacao = app('congregacao');
        $selectedDateInput = $request->input('data');
        $cultoIndex = $request->input('culto_index', 0);

        try {
            $selectedDate = $selectedDateInput
                ? Carbon::parse($selectedDateInput)->format('Y-m-d')
                : Carbon::today()->format('Y-m-d');
        } catch (\Throwable $exception) {
            $selectedDate = Carbon::today()->format('Y-m-d');
        }

        // Buscar todos os cultos do dia
        $cultosDoDia = Culto::where('congregacao_id', $congregacao->id)
            ->whereDate('data_culto', $selectedDate)
            ->orderBy('data_culto')
            ->get();

        $totalCultosDia = $cultosDoDia->count();
        $cultoIndex = max(0, min($cultoIndex, $totalCultosDia - 1));
        
        $culto = $cultosDoDia->get($cultoIndex);

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
            'totalCultosDia' => $totalCultosDia,
            'cultoIndex' => $cultoIndex,
        ]);
    }

    private function buildHistoricoCultosQuery(?string $dataInicial = null, ?string $dataFinal = null)
    {
        $query = Culto::with(['preletor', 'evento', 'categoria'])
            ->where('congregacao_id', app('congregacao')->id)
            ->whereDate('data_culto', '<=', date('Y-m-d'));

        if ($dataInicial) {
            $query->whereDate('data_culto', '>=', $dataInicial);
        }

        if ($dataFinal) {
            $query->whereDate('data_culto', '<=', $dataFinal);
        }

        return $query->orderByDesc('data_culto');
    }

    private function resolveCongregacaoLogoDataUri($congregacao): ?string
    {
        $logoPath = (string) data_get($congregacao, 'config.logo_caminho', '');

        if ($logoPath === '') {
            return null;
        }

        $normalizedPath = ltrim($logoPath, '/');
        $normalizedPath = str_starts_with($normalizedPath, 'storage/') ? substr($normalizedPath, 8) : $normalizedPath;
        $normalizedPath = str_starts_with($normalizedPath, 'public/') ? substr($normalizedPath, 7) : $normalizedPath;

        $candidates = array_values(array_unique(array_filter([
            $normalizedPath,
            'congregacoes/' . $congregacao->id . '/imagens/' . basename($normalizedPath),
        ])));

        $directoryPath = Storage::disk('public')->path('congregacoes/' . $congregacao->id . '/imagens');

        if (is_dir($directoryPath)) {
            $fallbackFiles = glob($directoryPath . '/*.{png,jpg,jpeg,webp,svg}', GLOB_BRACE) ?: [];

            usort($fallbackFiles, fn (string $a, string $b) => filemtime($b) <=> filemtime($a));

            foreach ($fallbackFiles as $fallbackFile) {
                $relativeFallback = 'congregacoes/' . $congregacao->id . '/imagens/' . basename($fallbackFile);
                $candidates[] = $relativeFallback;
            }

            $candidates = array_values(array_unique($candidates));
        }

        foreach ($candidates as $candidate) {
            if (! Storage::disk('public')->exists($candidate)) {
                continue;
            }

            $absolutePath = Storage::disk('public')->path($candidate);

            if (! is_file($absolutePath)) {
                continue;
            }

            $mimeType = mime_content_type($absolutePath) ?: 'image/png';
            return 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($absolutePath));
        }

        return null;
    }
}
