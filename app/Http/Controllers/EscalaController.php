<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Culto;
use App\Models\Escala;
use App\Models\ItemEscala;
use App\Models\Membro;
use App\Models\TipoEscala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class EscalaController extends Controller
{
    public function form_criar(?int $cultoId = null)
    {
        $congregacao = app('congregacao');

        $tiposEscala = TipoEscala::where('congregacao_id', $congregacao->id)->orderBy('nome')->get();
        $membros = Membro::where('congregacao_id', $congregacao->id)
            ->orderBy('nome')
            ->get();

        $culto = null;
        if ($cultoId !== null) {
            $culto = Culto::where('congregacao_id', $congregacao->id)
                ->with('categoria')
                ->findOrFail($cultoId);
        }

        $cultosAgendados = Culto::where('congregacao_id', $congregacao->id)
            ->whereDate('data_culto', '>=', now()->toDateString())
            ->with('categoria')
            ->orderBy('data_culto')
            ->get();

        if ($culto && $cultosAgendados->doesntContain('id', $culto->id)) {
            $cultosAgendados->prepend($culto);
        }

        return view('escalas.includes.form_criar', compact('tiposEscala', 'membros', 'culto', 'cultosAgendados'));
    }

    public function store(Request $request)
    {
        $congregacaoId = app('congregacao')->id;

        $validated = $this->validateEscala($request);

        $culto = null;
        if (! empty($validated['culto_id'])) {
            $culto = Culto::where('congregacao_id', $congregacaoId)
                ->findOrFail($validated['culto_id']);
        }

        $escala = DB::transaction(function () use ($validated, $culto) {
            $escala = Escala::create([
                'culto_id' => $culto?->id,
                'tipo_escala_id' => $validated['tipo_escala_id'],
                'data_hora' => $validated['data_hora'] ?? null,
                'local' => $validated['local'] ?? null,
                'observacoes' => $validated['observacoes'] ?? null,
            ]);

            $this->sincronizarItens($escala, $validated['itens'] ?? []);

            return $escala;
        });

        if ($request->expectsJson()) {
            $escala->load(['tipo', 'itens.membro', 'culto.escalas.tipo', 'culto.escalas.itens.membro']);

            $escalaData = [
                'id' => $escala->id,
                'culto_id' => $escala->culto_id,
                'tipo_escala_id' => $escala->tipo_escala_id,
                'tipo_nome' => optional($escala->tipo)->nome,
                'data_hora' => optional($escala->data_hora)->format('Y-m-d H:i'),
                'local' => $escala->local,
                'observacoes' => $escala->observacoes,
                'itens' => $escala->itens->map(function (ItemEscala $item) {
                    return [
                        'id' => $item->id,
                        'funcao' => $item->funcao,
                        'membro_id' => $item->membro_id,
                        'responsavel_externo' => $item->responsavel_externo,
                        'membro_nome' => optional($item->membro)->nome,
                    ];
                })->values(),
            ];

            $escalasHtml = null;
            if ($escala->culto) {
                $escalasHtml = view('cultos.includes.partials.escalas_lista', [
                    'escalas' => $escala->culto->escalas,
                ])->render();
            }

            return response()->json([
                'success' => true,
                'message' => 'Escala registrada com sucesso!',
                'escala' => $escalaData,
                'data' => $escalaData,
                'escalasHtml' => $escalasHtml,
            ]);
        }

        return redirect()->back()->with('success', 'Escala registrada com sucesso!');
    }

    public function form_editar(int $id)
    {
        $congregacaoId = app('congregacao')->id;

        $escala = Escala::with(['itens.membro', 'tipo', 'culto'])
            ->where('id', $id)
            ->where(function ($query) use ($congregacaoId) {
                $query->whereHas('culto', function ($subQuery) use ($congregacaoId) {
                    $subQuery->where('congregacao_id', $congregacaoId);
                })->orWhereNull('culto_id');
            })
            ->firstOrFail();

        $tiposEscala = TipoEscala::where('congregacao_id', $congregacaoId)->orderBy('nome')->get();
        $membros = Membro::where('congregacao_id', $congregacaoId)
            ->orderBy('nome')
            ->get();

        $cultosAgendados = Culto::where('congregacao_id', $congregacaoId)
            ->whereDate('data_culto', '>=', now()->toDateString())
            ->with('categoria')
            ->orderBy('data_culto')
            ->get();

        if ($escala->culto && $cultosAgendados->doesntContain('id', $escala->culto->id)) {
            $cultosAgendados->prepend($escala->culto);
        }

        return view('escalas.includes.form_editar', compact('escala', 'tiposEscala', 'membros', 'cultosAgendados'));
    }

    public function form_tipo_criar()
    {
        return view('escalas.includes.form_tipo');
    }

    public function painel(Request $request)
    {
        $congregacao = app('congregacao');

        $filters = $this->validateEscalaFilters($request);
        $request->merge($filters);

        $escalas = $this->buildEscalaQuery($request)
            ->paginate(10)
            ->withQueryString();

        $tiposEscala = TipoEscala::where('congregacao_id', $congregacao->id)->orderBy('nome')->get();

        return view('escalas.painel', [
            'congregacao' => $congregacao,
            'escalas' => $escalas,
            'tiposEscala' => $tiposEscala,
            'filters' => $filters,
        ]);
    }

    public function search(Request $request)
    {
        $filters = $this->validateEscalaFilters($request);
        $request->merge($filters);

        $escalas = $this->buildEscalaQuery($request)->paginate(10);
        $escalas->appends($filters);

        $view = view('escalas.includes.lista', ['escalas' => $escalas])->render();

        return response()->json(['view' => $view]);
    }

    public function update(Request $request, int $id)
    {
        $congregacaoId = app('congregacao')->id;

        $escala = Escala::where('id', $id)
            ->where(function ($query) use ($congregacaoId) {
                $query->whereHas('culto', function ($subQuery) use ($congregacaoId) {
                    $subQuery->where('congregacao_id', $congregacaoId);
                })->orWhereNull('culto_id');
            })
            ->firstOrFail();

        $validated = $this->validateEscala($request, $escala->id);

        $culto = null;
        if (! empty($validated['culto_id'])) {
            $culto = Culto::where('congregacao_id', $congregacaoId)
                ->findOrFail($validated['culto_id']);
        }

        DB::transaction(function () use ($escala, $validated, $culto) {
            $escala->update([
                'culto_id' => $culto?->id,
                'tipo_escala_id' => $validated['tipo_escala_id'],
                'data_hora' => $validated['data_hora'] ?? null,
                'local' => $validated['local'] ?? null,
                'observacoes' => $validated['observacoes'] ?? null,
            ]);

            $escala->itens()->delete();
            $this->sincronizarItens($escala, $validated['itens'] ?? []);
        });

        if ($request->expectsJson()) {
            $escala->load(['tipo', 'itens.membro', 'culto.escalas.tipo', 'culto.escalas.itens.membro']);

            $escalaData = [
                'id' => $escala->id,
                'culto_id' => $escala->culto_id,
                'tipo_escala_id' => $escala->tipo_escala_id,
                'tipo_nome' => optional($escala->tipo)->nome,
                'data_hora' => optional($escala->data_hora)->format('Y-m-d H:i'),
                'local' => $escala->local,
                'observacoes' => $escala->observacoes,
                'itens' => $escala->itens->map(function (ItemEscala $item) {
                    return [
                        'id' => $item->id,
                        'funcao' => $item->funcao,
                        'membro_id' => $item->membro_id,
                        'responsavel_externo' => $item->responsavel_externo,
                        'membro_nome' => optional($item->membro)->nome,
                    ];
                })->values(),
            ];

            $escalasHtml = null;
            if ($escala->culto) {
                $escalasHtml = view('cultos.includes.partials.escalas_lista', [
                    'escalas' => $escala->culto->escalas,
                ])->render();
            }

            return response()->json([
                'success' => true,
                'message' => 'Escala atualizada com sucesso!',
                'escala' => $escalaData,
                'data' => $escalaData,
                'escalasHtml' => $escalasHtml,
            ]);
        }

        return redirect()->back()->with('success', 'Escala atualizada com sucesso!');
    }

    public function store_tipo(Request $request)
    {
        $data = $this->validateTipoEscala($request);

        TipoEscala::create($data);

        return redirect('/cadastros#escalas')->with('msg', 'Tipo de escala cadastrado com sucesso!');
    }

    public function form_tipo_editar(int $id)
    {
        $congregacaoId = app('congregacao')->id;
        $tipo = TipoEscala::where('congregacao_id', $congregacaoId)->findOrFail($id);

        return view('escalas.includes.form_tipo', compact('tipo'));
    }

    public function update_tipo(Request $request, int $id)
    {
        $congregacaoId = app('congregacao')->id;
        $tipo = TipoEscala::where('congregacao_id', $congregacaoId)->findOrFail($id);

        $data = $this->validateTipoEscala($request, $tipo->id);

        $tipo->update($data);

        return redirect('/cadastros#escalas')->with('msg', 'Tipo de escala atualizado com sucesso!');
    }

    public function destroy_tipo(int $id)
    {
        $congregacaoId = app('congregacao')->id;
        $tipo = TipoEscala::where('congregacao_id', $congregacaoId)->findOrFail($id);

        $tipo->delete();

        return redirect('/cadastros#escalas')->with('msg', 'Tipo de escala removido com sucesso!');
    }

    public function destroy(Request $request, int $id)
    {
        $congregacaoId = app('congregacao')->id;

        $escala = Escala::where('id', $id)
            ->with('culto')
            ->where(function ($query) use ($congregacaoId) {
                $query->whereHas('culto', function ($subQuery) use ($congregacaoId) {
                    $subQuery->where('congregacao_id', $congregacaoId);
                })->orWhereNull('culto_id');
            })
            ->firstOrFail();

        $culto = $escala->culto;
        $escala->delete();

        if ($request->expectsJson()) {
            $escalasHtml = null;
            if ($culto) {
                $culto->load(['escalas.tipo', 'escalas.itens.membro']);
                $escalasHtml = view('cultos.includes.partials.escalas_lista', [
                    'escalas' => $culto->escalas,
                ])->render();
            }

            return response()->json([
                'success' => true,
                'message' => 'Escala excluída com sucesso!',
                'culto_id' => $culto?->id,
                'escalasHtml' => $escalasHtml,
            ]);
        }

        return redirect()->back()->with('msg', 'Escala excluída com sucesso!');
    }

    protected function validateEscala(Request $request, ?int $escalaId = null): array
    {
        $congregacaoId = app('congregacao')->id;
        $messages = [
            'tipo_escala_id.required' => 'Selecione um tipo de escala.',
            'tipo_escala_id.exists' => 'Tipo de escala inválido.',
            'culto_id.exists' => 'Culto informado não foi encontrado.',
            'itens.required' => 'Informe pelo menos um item para a escala.',
            'itens.*.funcao.required' => 'A função do item é obrigatória.',
        ];

        $data = $request->validate([
            'culto_id' => 'nullable|exists:cultos,id',
            'tipo_escala_id' => [
                'required',
                Rule::exists('tipos_escala', 'id')->where('congregacao_id', $congregacaoId),
            ],
            'data_hora' => 'nullable|date',
            'local' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
            'itens' => 'required|array|min:1',
            'itens.*.funcao' => 'required|string|max:255',
            'itens.*.membro_id' => 'nullable|exists:membros,id',
            'itens.*.responsavel_externo' => 'nullable|string|max:255',
        ], $messages);

        if (empty($data['culto_id']) && empty($data['data_hora'])) {
            throw ValidationException::withMessages([
                'culto_id' => 'Informe a data/hora ou vincule a escala a um culto agendado.',
                'data_hora' => 'Informe a data/hora ou vincule a escala a um culto agendado.',
            ]);
        }

        return $data;
    }

    protected function sincronizarItens(Escala $escala, array $itens): void
    {
        foreach ($itens as $item) {
            $funcao = trim($item['funcao'] ?? '');
            $membroId = $item['membro_id'] ?? null;
            $responsavelExterno = trim($item['responsavel_externo'] ?? '') ?: null;

            if ($funcao === '' && empty($membroId) && empty($responsavelExterno)) {
                continue;
            }

            ItemEscala::create([
                'escala_id' => $escala->id,
                'funcao' => $funcao,
                'membro_id' => $membroId,
                'responsavel_externo' => $responsavelExterno,
            ]);
        }
    }

    protected function validateTipoEscala(Request $request, ?int $id = null): array
    {
        $congregacaoId = app('congregacao')->id;
        $nomeRule = Rule::unique('tipos_escala', 'nome')
            ->where(fn ($query) => $query->where('congregacao_id', $congregacaoId));
        if ($id !== null) {
            $nomeRule->ignore($id);
        }

        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255', $nomeRule],
            'ativo' => 'nullable|boolean',
        ]);

        $data['ativo'] = $request->boolean('ativo');

        if ($id === null && ! $request->has('ativo')) {
            $data['ativo'] = true;
        }

        $data['congregacao_id'] = $congregacaoId;

        return $data;
    }

    protected function validateEscalaFilters(Request $request): array
    {
        $congregacaoId = app('congregacao')->id;
        return $request->validate([
            'tipo' => [
                'nullable',
                Rule::exists('tipos_escala', 'id')->where('congregacao_id', $congregacaoId),
            ],
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
        ]);
    }

    protected function buildEscalaQuery(Request $request): Builder
    {
        $congregacaoId = app('congregacao')->id;

        $query = Escala::with(['tipo', 'culto'])
            ->where(function ($inner) use ($congregacaoId) {
                $inner->whereHas('culto', function ($sub) use ($congregacaoId) {
                    $sub->where('congregacao_id', $congregacaoId);
                })->orWhereNull('culto_id');
            });

        if ($request->filled('tipo')) {
            $query->where('tipo_escala_id', $request->input('tipo'));
        }

        $query = $this->applyDateFilter($query, '>=', $request->input('data_inicio'));
        $query = $this->applyDateFilter($query, '<=', $request->input('data_fim'));

        return $query->orderByDesc('data_hora')->orderByDesc('created_at');
    }

    protected function applyDateFilter(Builder $query, string $operator, ?string $date): Builder
    {
        if (empty($date)) {
            return $query;
        }

        try {
            $parsed = Carbon::parse($date)->toDateString();
        } catch (\Exception $exception) {
            return $query;
        }

        return $query->where(function (Builder $inner) use ($operator, $parsed) {
            $inner->whereDate('data_hora', $operator, $parsed)
                ->orWhereHas('culto', function (Builder $sub) use ($operator, $parsed) {
                    $sub->whereDate('data_culto', $operator, $parsed);
                });
        });
    }
}
