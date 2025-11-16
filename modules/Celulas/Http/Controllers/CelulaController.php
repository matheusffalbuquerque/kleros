<?php

namespace Modules\Celulas\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Celula;
use App\Models\EncontroCelula;
use App\Models\Membro;
use App\Models\SituacaoVisitante;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class CelulaController extends Controller
{
    public function painel()
    {
        $celulas = Celula::with([
                'lider',
                'colider',
                'anfitriao',
                'congregacao.cidade',
                'congregacao.estado',
                'congregacao.pais',
            ])
            ->where('congregacao_id', app('congregacao')->id)
            ->orderBy('identificacao')
            ->paginate(10);

        $membros = Membro::orderBy('nome')->get();

        $celulasMapa = collect($celulas->items())
            ->map(function (Celula $celula) {
                if (is_null($celula->latitude) || is_null($celula->longitude)) {
                    return null;
                }

                $endereco = collect([
                    $celula->endereco,
                    $celula->numero,
                    $celula->bairro,
                    optional(optional($celula->congregacao)->cidade)->nome,
                    optional(optional($celula->congregacao)->estado)->nome,
                    optional(optional($celula->congregacao)->pais)->nome,
                ])->filter()->implode(', ');

                return [
                    'id' => $celula->id,
                    'nome' => $celula->identificacao,
                    'endereco' => $endereco,
                    'latitude' => (float) $celula->latitude,
                    'longitude' => (float) $celula->longitude,
                    'cor' => $celula->cor_borda ?? '#4285F4',
                ];
            })
            ->filter()
            ->values();

        $googleMapsKey = config('services.google_maps.api_key');
        $googleMapsMapId = config('services.google_maps.map_id');

        return view('celulas::painel', compact('membros', 'celulas', 'celulasMapa', 'googleMapsKey', 'googleMapsMapId'));
    }

    public function encontros(Request $request)
    {
        $congregacao = app('congregacao');

        $celulas = Celula::where('congregacao_id', $congregacao->id)
            ->orderBy('identificacao')
            ->get();

        $selectedCelulaId = (int) $request->input('celula_id', $celulas->first()->id ?? 0) ?: null;

        $selectedDateInput = $request->input('data');

        try {
            $selectedDate = $selectedDateInput
                ? Carbon::parse($selectedDateInput)->format('Y-m-d')
                : Carbon::today()->format('Y-m-d');
        } catch (\Throwable $exception) {
            $selectedDate = Carbon::today()->format('Y-m-d');
        }

        $baseQuery = EncontroCelula::with(['celula', 'preletor', 'presentes.membro'])
            ->where('congregacao_id', $congregacao->id);

        if ($selectedCelulaId) {
            $baseQuery->where('celula_id', $selectedCelulaId);
        }

        $encontroDoDia = (clone $baseQuery)
            ->whereDate('data_encontro', $selectedDate)
            ->orderByDesc('hora_encontro')
            ->orderByDesc('id')
            ->first();

        if ($encontroDoDia) {
            $encontroDoDia->loadMissing('presentes.membro');
        }

        $historicoEncontros = (clone $baseQuery)
            ->orderByDesc('data_encontro')
            ->orderByDesc('hora_encontro')
            ->limit(10)
            ->get();

        $situacoesVisitante = SituacaoVisitante::orderBy('titulo')->get();

        $statusOptions = [
            'pendente' => 'Pendente',
            'confirmado' => 'Confirmado',
            'cancelado' => 'Cancelado',
        ];

        $selectedCelula = $celulas->firstWhere('id', $selectedCelulaId);
        $selectedDateCarbon = Carbon::parse($selectedDate);

        return view('celulas::encontros', [
            'congregacao' => $congregacao,
            'celulas' => $celulas,
            'selectedCelula' => $selectedCelula,
            'selectedCelulaId' => $selectedCelulaId,
            'selectedDate' => $selectedDate,
            'selectedDateCarbon' => $selectedDateCarbon,
            'encontroDoDia' => $encontroDoDia,
            'historicoEncontros' => $historicoEncontros,
            'situacoesVisitante' => $situacoesVisitante,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function modalAdicionarPresente(Request $request)
    {
        $congregacao = app('congregacao');

        $celulaId = $request->integer('celula_id') ?: null;
        $dataInput = $request->input('data');

        try {
            $dataEncontro = $dataInput
                ? Carbon::parse($dataInput)->format('Y-m-d')
                : Carbon::today()->format('Y-m-d');
        } catch (\Throwable $exception) {
            $dataEncontro = Carbon::today()->format('Y-m-d');
        }

        $celula = null;

        if ($celulaId) {
            $celula = Celula::where('congregacao_id', $congregacao->id)->find($celulaId);

            if (! $celula) {
                $celulaId = null;
            }
        }

        $membros = Membro::DaCongregacao()
            ->orderBy('nome')
            ->get();

        $situacoesVisitante = SituacaoVisitante::orderBy('titulo')->get();

        $panelParams = array_filter([
            'celula_id' => $celulaId,
            'data' => $dataEncontro,
        ], static fn ($value) => ! is_null($value));

        $panelUrl = route('celulas.encontros', $panelParams);

        return view('celulas::includes.modal_presenca', [
            'celula' => $celula,
            'celulaId' => $celulaId,
            'dataEncontro' => $dataEncontro,
            'membros' => $membros,
            'situacoesVisitante' => $situacoesVisitante,
            'panelUrl' => $panelUrl,
        ]);
    }

    public function integrantes($celulaId)
    {
        $congregacao = app('congregacao');

        $celula = Celula::with(['lider', 'colider', 'anfitriao'])
            ->where('congregacao_id', $congregacao->id)
            ->findOrFail($celulaId);

        $integrantes = $celula->participantes()
            ->with('ministerio')
            ->orderBy('nome')
            ->paginate(10);

        $membrosDisponiveis = Membro::DaCongregacao()
            ->orderBy('nome')
            ->whereDoesntHave('celulas', function ($query) use ($celula) {
                $query->where('celulas.id', $celula->id);
            })
            ->get();

        return view('celulas::integrantes', [
            'congregacao' => $congregacao,
            'celula' => $celula,
            'integrantes' => $integrantes,
            'membros' => $membrosDisponiveis,
        ]);
    }

    public function search(Request $request)
    {
        $congregacaoId = app('congregacao')->id;
        $termo = trim($request->input('membro', ''));

        $query = Celula::with(['lider', 'colider', 'anfitriao'])
            ->where('congregacao_id', $congregacaoId)
            ->orderBy('identificacao');

        if ($termo !== '') {
            $query->where(function ($builder) use ($termo) {
                $builder
                    ->whereHas('lider', function ($sub) use ($termo) {
                        $sub->where('nome', 'like', '%' . $termo . '%');
                    })
                    ->orWhereHas('colider', function ($sub) use ($termo) {
                        $sub->where('nome', 'like', '%' . $termo . '%');
                    })
                    ->orWhereHas('anfitriao', function ($sub) use ($termo) {
                        $sub->where('nome', 'like', '%' . $termo . '%');
                    })
                    ->orWhereHas('participantes', function ($sub) use ($termo) {
                        $sub->where('nome', 'like', '%' . $termo . '%');
                    });
            });
        }

        $celulas = $query->get();

        $view = view('celulas::includes.lista', [
            'celulas' => $celulas,
        ])->render();

        return response()->json(['view' => $view]);
    }

    public function adicionarParticipante(Request $request)
    {
        $congregacaoId = app('congregacao')->id;

        $validated = $request->validate([
            'membro' => ['required', 'exists:membros,id'],
            'celula' => ['required', 'exists:celulas,id'],
        ]);

        $celula = Celula::where('congregacao_id', $congregacaoId)
            ->findOrFail($validated['celula']);

        $membro = Membro::DaCongregacao()->findOrFail($validated['membro']);

        DB::transaction(function () use ($celula, $membro) {
            $participantesAtuais = $celula->participantes()->pluck('membro_id')->toArray();

            if (! in_array($membro->id, $participantesAtuais)) {
                $participantesAtuais[] = $membro->id;
            }

            $this->sincronizarParticipantesExclusivos($celula, $participantesAtuais);
        });

        return redirect()
            ->route('celulas.integrantes', $celula->id)
            ->with('msg', 'Membro adicionado à célula com sucesso.');
    }

    public function form_criar()
    {
        $membros = Membro::orderBy('nome')->get();
        return view('celulas::includes.form_criar', compact('membros'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'identificacao' => 'required|string|max:255',
            'cor_borda' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ], [
            'identificacao.required' => 'O campo Identificação é obrigatório.',
            'cor_borda.regex' => 'Selecione uma cor válida.',
            'latitude.numeric' => 'Latitude deve ser um número válido.',
            'latitude.between' => 'Latitude deve estar entre -90 e 90.',
            'longitude.numeric' => 'Longitude deve ser um número válido.',
            'longitude.between' => 'Longitude deve estar entre -180 e 180.',
        ]);

        $celula = new Celula();

        DB::transaction(function () use ($request, $celula) {
            $celula->congregacao_id = app('congregacao')->id;
            $celula->identificacao = $request->identificacao;
            $celula->lider_id = $request->lider_id ?: null;
            $celula->colider_id = $request->colider_id ?: null;
            $celula->anfitriao_id = $request->anfitriao_id ?: null;
            $celula->endereco = $request->endereco;
            $celula->numero = $request->numero;
            $celula->bairro = $request->bairro;
            $celula->cep = $request->cep;
            $celula->dia_encontro = $request->dia_encontro;
            $celula->hora_encontro = $request->hora_encontro;
            $celula->ativa = $request->ativa ?? 1;
            $celula->descricao = $request->descricao;
            $celula->cor_borda = $request->cor_borda ?: '#ffffff';
            $celula->latitude = $request->latitude;
            $celula->longitude = $request->longitude;
            $celula->created_at = now();
            $celula->updated_at = now();

            $this->preencherCoordenadas($celula);
            $celula->save();

            $participantes = $this->obterParticipantesIncluindoLideres($request);
            $this->sincronizarParticipantesExclusivos($celula, $participantes);
        });

        return redirect()->back()->with('msg', 'Célula criada com sucesso!');
    }

    public function form_editar($celula)
    {
        $membros = Membro::orderBy('nome')->get();
        $celula = Celula::with('participantes')->findOrFail($celula);
        return view('celulas::includes.form_editar', compact('celula', 'membros'));
    }

    public function update(Request $request, $id)
    {
        $celula = Celula::findOrFail($id);

        $request->validate([
            'identificacao' => 'required|string|max:255',
            'cor_borda' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ], [
            'identificacao.required' => 'O campo Identificação é obrigatório.',
            'cor_borda.regex' => 'Selecione uma cor válida.',
            'latitude.numeric' => 'Latitude deve ser um número válido.',
            'latitude.between' => 'Latitude deve estar entre -90 e 90.',
            'longitude.numeric' => 'Longitude deve ser um número válido.',
            'longitude.between' => 'Longitude deve estar entre -180 e 180.',
        ]);

        DB::transaction(function () use ($request, $celula) {
            $celula->identificacao = $request->identificacao;
            $celula->lider_id = $request->lider_id ?: null;
            $celula->colider_id = $request->colider_id ?: null;
            $celula->anfitriao_id = $request->anfitriao_id ?: null;
            $celula->endereco = $request->endereco;
            $celula->numero = $request->numero;
            $celula->bairro = $request->bairro;
            $celula->cep = $request->cep;
            $celula->dia_encontro = $request->dia_encontro;
            $celula->hora_encontro = $request->hora_encontro;
            $celula->ativa = $request->ativa ?? 1;
            $celula->descricao = $request->descricao;
            $celula->cor_borda = $request->cor_borda ?: '#ffffff';
            $celula->latitude = $request->latitude;
            $celula->longitude = $request->longitude;
            $celula->updated_at = now();

            $this->preencherCoordenadas($celula);
            $celula->save();

            $participantes = $this->obterParticipantesIncluindoLideres($request);
            $this->sincronizarParticipantesExclusivos($celula, $participantes);
        });

        return redirect()->back()->with('msg', 'Célula atualizada com sucesso!');
    }

    public function destroy(Celula $celula)
    {
        $celula->delete();
        return redirect()->back()->with('success', 'Célula removida com sucesso!');
    }

    public function membrosPorCelula($celulaId)
    {
        $celula = Celula::with(['participantes' => function ($query) {
                $query->orderBy('nome');
            }])
            ->where('congregacao_id', app('congregacao')->id)
            ->findOrFail($celulaId);

        return response()->json([
            'celula_id' => $celula->id,
            'participantes' => $celula->participantes->map(fn ($membro) => [
                'id' => $membro->id,
                'nome' => $membro->nome,
            ])->values(),
        ]);
    }

    private function obterParticipantesIncluindoLideres(Request $request): array
    {
        return collect($request->input('participantes', []))
            ->merge([$request->lider_id, $request->colider_id])
            ->filter(fn ($id) => ! is_null($id) && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function sincronizarParticipantesExclusivos(Celula $celula, array $participantesIds): void
    {
        if (! empty($participantesIds)) {
            $outrasCelulasIds = Celula::where('congregacao_id', $celula->congregacao_id)
                ->where('id', '!=', $celula->id)
                ->pluck('id');

            if ($outrasCelulasIds->isNotEmpty()) {
                DB::table('membro_celula')
                    ->whereIn('membro_id', $participantesIds)
                    ->whereIn('celula_id', $outrasCelulasIds)
                    ->delete();
            }

            Celula::where('congregacao_id', $celula->congregacao_id)
                ->where('id', '!=', $celula->id)
                ->whereIn('lider_id', $participantesIds)
                ->get()
                ->each(function (Celula $outraCelula) {
                    $outraCelula->lider_id = null;
                    $outraCelula->save();
                });

            Celula::where('congregacao_id', $celula->congregacao_id)
                ->where('id', '!=', $celula->id)
                ->whereIn('colider_id', $participantesIds)
                ->get()
                ->each(function (Celula $outraCelula) {
                    $outraCelula->colider_id = null;
                    $outraCelula->save();
                });
        }

        $celula->participantes()->sync($participantesIds);
    }

    private function preencherCoordenadas(Celula $celula): void
    {
        if (! is_null($celula->latitude) && ! is_null($celula->longitude)) {
            return;
        }

        $coordenadas = $this->obterCoordenadasPorEndereco($celula);
        if ($coordenadas) {
            $celula->latitude = $coordenadas['lat'];
            $celula->longitude = $coordenadas['lng'];
        }
    }

    private function obterCoordenadasPorEndereco(Celula $celula): ?array
    {
        $apiKey = config('services.google_maps.api_key');
        if (! $apiKey) {
            return null;
        }

        $endereco = $this->montarEnderecoCompleto($celula);
        if (! $endereco) {
            return null;
        }

        try {
            $response = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $endereco,
                'key' => $apiKey,
                'language' => 'pt-BR',
            ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();
            if (($data['status'] ?? '') !== 'OK' || empty($data['results'][0]['geometry']['location'])) {
                return null;
            }

            $location = $data['results'][0]['geometry']['location'];

            return [
                'lat' => (float) $location['lat'],
                'lng' => (float) $location['lng'],
            ];
        } catch (\Throwable $exception) {
            logger()->warning('Falha ao geocodificar endereço da célula', [
                'celula_id' => $celula->id,
                'endereco' => $endereco,
                'erro' => $exception->getMessage(),
            ]);
        }

        return null;
    }

    private function montarEnderecoCompleto(Celula $celula): ?string
    {
        $partes = [
            $celula->endereco,
            $celula->numero,
            $celula->bairro,
        ];

        $celula->loadMissing('congregacao.cidade', 'congregacao.estado', 'congregacao.pais');
        if ($celula->congregacao) {
            $partes[] = optional($celula->congregacao->cidade)->nome;
            $partes[] = optional($celula->congregacao->estado)->nome;
            $partes[] = optional($celula->congregacao->pais)->nome;
        }

        $partes = array_filter($partes);

        return empty($partes) ? null : implode(', ', $partes);
    }
}
