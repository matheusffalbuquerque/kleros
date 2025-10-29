<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Membro;
use App\Models\SituacaoVisitante;
use App\Models\Visitante;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class VisitanteController extends Controller
{
    public function create(Request $request)
    {
        $situacao_visitante = SituacaoVisitante::orderBy('titulo')->get();
        $congregacao = app('congregacao');

        return view('visitantes/cadastro', [
            'situacao_visitante' => $situacao_visitante,
            'congregacao' => $congregacao,
            'returnTo' => $request->input('return_to'),
        ]);
    }

    public function store(Request $request)
    {
        $visitante = new Visitante;

        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:100'],
            'data_visita' => ['required', 'date'],
        ], [
            'nome.required' => __('visitors.validation.name_required'),
            'telefone.required' => __('visitors.validation.phone_required'),
            'data_visita.required' => __('visitors.validation.date_required'),
        ]);

        $visitante->nome = $request->nome;
        $visitante->telefone = $request->telefone;
        $visitante->data_visita = $request->data_visita;
        $visitante->sit_visitante_id = $request->situacao;
        $visitante->observacoes = $request->observacoes;
        $visitante->congregacao_id = app('congregacao')->id;
        $visitante->created_at = now();
        $visitante->updated_at = now();

        $visitante->save();

        $successMessage = __('visitors.flash.created', ['name' => $visitante->nome]);
        $redirectTo = $request->input('return_to');

        if ($redirectTo && (Str::startsWith($redirectTo, '/') || Str::startsWith($redirectTo, url('/')))) {
            return redirect()
                ->to($redirectTo)
                ->with('msg', $successMessage);
        }

        return redirect()
            ->route('visitantes.adicionar')
            ->with('msg', $successMessage);
    }

    public function form_criar(Request $request)
    {
        $situacao_visitante = SituacaoVisitante::orderBy('titulo')->get();

        return view('visitantes/includes/form_criar', [
            'situacao_visitante' => $situacao_visitante,
            'returnTo' => $request->input('return_to'),
        ]);
    }

    public function historico()
    {
        $visitantes = Visitante::where('congregacao_id', app('congregacao')->id)
            ->orderByDesc('data_visita')
            ->paginate(10);

        return view('visitantes/historico', ['visitantes' => $visitantes]);
    }

    public function search(Request $request)
    {
        $query = Visitante::where('congregacao_id', app('congregacao')->id);

        $dataInicial = $request->input('data_inicial');
        $dataFinal = $request->input('data_final');

        if ($dataInicial && $dataFinal && $dataInicial > $dataFinal) {
            [$dataInicial, $dataFinal] = [$dataFinal, $dataInicial];
        }

        if ($request->filled('nome')) {
            $query->where('nome', 'LIKE', '%' . $request->nome . '%');
        }

        if ($dataInicial) {
            $query->whereDate('data_visita', '>=', $dataInicial);
        }

        if ($dataFinal) {
            $query->whereDate('data_visita', '<=', $dataFinal);
        }

        $visitantes = $query->orderByDesc('data_visita')->get();

        $view = view('visitantes/includes/visitantes_search', ['visitantes' => $visitantes])->render();

        return response()->json(['view' => $view]);
    }

    public function export(Request $request)
    {
        $query = Visitante::where('congregacao_id', app('congregacao')->id)
            ->with('sit_visitante')
            ->orderByDesc('data_visita');

        $dataInicial = $request->input('data_inicial');
        $dataFinal = $request->input('data_final');

        if ($dataInicial && $dataFinal && $dataInicial > $dataFinal) {
            [$dataInicial, $dataFinal] = [$dataFinal, $dataInicial];
        }

        if ($dataInicial) {
            $query->whereDate('data_visita', '>=', $dataInicial);
        }

        if ($dataFinal) {
            $query->whereDate('data_visita', '<=', $dataFinal);
        }

        if ($request->filled('nome')) {
            $query->where('nome', 'LIKE', '%' . $request->input('nome') . '%');
        }

        $visitantes = $query->get();

        $filename = __('visitors.export.filename_prefix') . now()->format('Y-m-d_H-i-s') . '.csv';

        $callback = function () use ($visitantes) {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fwrite($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $headers = __('visitors.export.headers');
            if (!is_array($headers)) {
                $headers = ['Nome', 'Data da visita', 'Telefone', 'Situação', 'Observações'];
            }

            fputcsv($handle, $headers, ';');

            foreach ($visitantes as $visitante) {
                $dataVisita = $visitante->data_visita
                    ? Carbon::parse($visitante->data_visita)->format('Y-m-d')
                    : '';

                fputcsv($handle, [
                    $visitante->nome,
                    $dataVisita,
                    $visitante->telefone,
                    optional($visitante->sit_visitante)->titulo ?? __('visitors.common.statuses.not_informed'),
                    $visitante->observacoes,
                ], ';');
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exibir($id)
    {
        $visitante = Visitante::findOrFail($id);

        return view('visitantes/exibir', ['visitante' => $visitante]);
    }

    public function form_editar($id)
    {
        $visitante = Visitante::findOrFail($id);
        $situacao_visitante = SituacaoVisitante::orderBy('titulo')->get();

        return view('visitantes/includes/form_editar', [
            'visitante' => $visitante,
            'situacao_visitante' => $situacao_visitante,
            'returnTo' => request('return_to'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $visitante = Visitante::findOrFail($id);

        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:100'],
            'data_visita' => ['required', 'date'],
        ], [
            'nome.required' => __('visitors.validation.name_required'),
            'telefone.required' => __('visitors.validation.phone_required'),
            'data_visita.required' => __('visitors.validation.date_required'),
        ]);

        $visitante->nome = $request->nome;
        $visitante->telefone = $request->telefone;
        $visitante->data_visita = $request->data_visita;
        $visitante->sit_visitante_id = $request->sit_visitante;
        $visitante->observacoes = $request->observacoes;
        $visitante->updated_at = now();

        $visitante->save();

        if ($request->expectsJson()) {
            return response()->json([
                'visitante' => [
                    'id' => $visitante->id,
                    'nome' => $visitante->nome,
                    'telefone' => $visitante->telefone,
                    'situacao' => optional($visitante->sit_visitante)->titulo,
                    'edit_url' => route('visitantes.form_editar', $visitante->id),
                    'destroy_url' => route('visitantes.destroy', $visitante->id),
                ],
                'message' => __('visitors.flash.updated', ['name' => $visitante->nome]),
            ]);
        }

        return redirect()
            ->route('visitantes.historico')
            ->with('msg', __('visitors.flash.updated', ['name' => $visitante->nome]));
    }

    public function destroy(Request $request, $id)
    {
        $visitante = Visitante::findOrFail($id);
        $visitante->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('visitors.flash.deleted'),
            ]);
        }

        return redirect()
            ->route('visitantes.historico')
            ->with('msg', __('visitors.flash.deleted'));
    }

    public function tornarMembro(Request $request)
    {
        $membro = new Membro;
        $membro->nome = $request->nome;
        $membro->telefone = $request->telefone;
        $membro->data_nascimento = null;
        $membro->congregacao_id = app('congregacao')->id;
        $membro->created_at = now();
        $membro->updated_at = now();
        $membro->save();

        return redirect()
            ->route('membros.editar', $membro->id)
            ->with('msg', __('visitors.flash.converted', ['name' => $membro->nome]));
    }

    public function quickSearch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'term' => ['required', 'string', 'min:2'],
            'data' => ['nullable', 'date'],
        ]);

        $term = trim($validated['term']);
        $congregacaoId = app('congregacao')->id;

        $selectedDate = $validated['data'] ?? Carbon::today()->format('Y-m-d');
        try {
            $selectedDate = Carbon::parse($selectedDate)->format('Y-m-d');
        } catch (\Throwable $exception) {
            $selectedDate = Carbon::today()->format('Y-m-d');
        }

        $visitantes = Visitante::with('sit_visitante')
            ->where('congregacao_id', $congregacaoId)
            ->where(function ($query) use ($term) {
                $query->where('nome', 'LIKE', '%' . $term . '%')
                    ->orWhere('telefone', 'LIKE', '%' . $term . '%');
            })
            ->orderByDesc('data_visita')
            ->limit(10)
            ->get();

        $registradosDoDia = Visitante::where('congregacao_id', $congregacaoId)
            ->whereDate('data_visita', $selectedDate)
            ->get();

        $results = $visitantes->map(function (Visitante $visitante) use ($registradosDoDia) {
            $visitanteNome = Str::lower($visitante->nome);
            $visitanteTelefone = $visitante->telefone;

            $alreadyRegistered = $registradosDoDia->contains(function ($item) use ($visitanteNome, $visitanteTelefone) {
                if (Str::lower($item->nome) !== $visitanteNome) {
                    return false;
                }

                if (!$visitanteTelefone) {
                    return true;
                }

                return $item->telefone === $visitanteTelefone;
            });

            return [
                'id' => $visitante->id,
                'nome' => $visitante->nome,
                'telefone' => $visitante->telefone,
                'situacao' => optional($visitante->sit_visitante)->titulo,
                'already_registered' => $alreadyRegistered,
            ];
        });

        return response()->json([
            'results' => $results,
            'selected_date' => $selectedDate,
        ]);
    }

    public function registrarPresenca(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'visitante_id' => ['required', 'integer', 'exists:visitantes,id'],
            'data' => ['required', 'date'],
        ]);

        $congregacaoId = app('congregacao')->id;

        try {
            $dataVisita = Carbon::parse($validated['data'])->format('Y-m-d');
        } catch (\Throwable $exception) {
            return response()->json([
                'message' => __('visitors.validation.date_required'),
            ], 422);
        }

        $visitanteBase = Visitante::where('congregacao_id', $congregacaoId)
            ->with('sit_visitante')
            ->findOrFail($validated['visitante_id']);

        $jaRegistrado = Visitante::where('congregacao_id', $congregacaoId)
            ->whereDate('data_visita', $dataVisita)
            ->where('nome', $visitanteBase->nome)
            ->where('telefone', $visitanteBase->telefone)
            ->orderByDesc('id')
            ->first();

        if ($jaRegistrado) {
            $jaRegistrado->loadMissing('sit_visitante');

            return response()->json([
                'visitante' => [
                    'id' => $jaRegistrado->id,
                    'nome' => $jaRegistrado->nome,
                    'telefone' => $jaRegistrado->telefone,
                    'situacao' => optional($jaRegistrado->sit_visitante)->titulo,
                    'edit_url' => route('visitantes.form_editar', $jaRegistrado->id),
                    'destroy_url' => route('visitantes.destroy', $jaRegistrado->id),
                ],
                'already_registered' => true,
            ]);
        }

        $novoVisitante = new Visitante;
        $novoVisitante->nome = $visitanteBase->nome;
        $novoVisitante->telefone = $visitanteBase->telefone;
        $novoVisitante->data_visita = $dataVisita;
        $novoVisitante->sit_visitante_id = $visitanteBase->sit_visitante_id;
        $novoVisitante->observacoes = $visitanteBase->observacoes;
        $novoVisitante->congregacao_id = $congregacaoId;
        $novoVisitante->created_at = now();
        $novoVisitante->updated_at = now();
        $novoVisitante->save();
        $novoVisitante->setRelation('sit_visitante', $visitanteBase->sit_visitante);

        return response()->json([
            'visitante' => [
                'id' => $novoVisitante->id,
                'nome' => $novoVisitante->nome,
                'telefone' => $novoVisitante->telefone,
                'situacao' => optional($novoVisitante->sit_visitante)->titulo,
                'edit_url' => route('visitantes.form_editar', $novoVisitante->id),
                'destroy_url' => route('visitantes.destroy', $novoVisitante->id),
            ],
            'already_registered' => false,
        ]);
    }
}
