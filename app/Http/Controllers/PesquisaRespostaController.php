<?php

namespace App\Http\Controllers;

use App\Models\Pesquisa;
use App\Models\Resposta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PesquisaRespostaController extends Controller
{
    public function index(Request $request): View
    {
        $membro = $this->resolveMembro();
        $status = $request->query('status', 'nao-respondidas');

        $pesquisasQuery = Pesquisa::with(['criador'])
            ->forCongregacao($membro->congregacao_id)
            ->withCount([
                'respostas as respostas_do_membro' => fn ($query) => $query->where('membro_id', $membro->id),
            ]);

        if ($status === 'respondidas') {
            $pesquisasQuery->whereHas('respostas', fn ($query) => $query->where('membro_id', $membro->id));
        } else {
            $status = 'nao-respondidas';
            $pesquisasQuery->whereDoesntHave('respostas', fn ($query) => $query->where('membro_id', $membro->id));
        }

        $pesquisas = $pesquisasQuery
            ->orderByDesc('data_inicio')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('pesquisas.replies', [
            'pesquisas' => $pesquisas,
            'status' => $status,
        ]);
    }

    public function show(int $pesquisaId): View
    {
        $membro = $this->resolveMembro();

        $pesquisa = Pesquisa::forCongregacao($membro->congregacao_id)
            ->with(['perguntas' => fn ($query) => $query->with('opcoes')->orderBy('created_at')])
            ->findOrFail($pesquisaId);

        $respostas = $pesquisa->respostas()
            ->where('membro_id', $membro->id)
            ->with('opcoes')
            ->get()
            ->keyBy('pergunta_id');

        return view('pesquisas.reply', [
            'pesquisa' => $pesquisa,
            'respostas' => $respostas,
        ]);
    }

    public function submit(Request $request, int $pesquisaId): RedirectResponse
    {
        $membro = $this->resolveMembro();

        $pesquisa = Pesquisa::forCongregacao($membro->congregacao_id)
            ->with(['perguntas' => fn ($query) => $query->with('opcoes')->orderBy('created_at')])
            ->findOrFail($pesquisaId);

        $rules = [];
        $messages = [];

        foreach ($pesquisa->perguntas as $pergunta) {
            $questionKey = "respostas.{$pergunta->id}";

            switch ($pergunta->tipo) {
                case 'texto':
                    $rules["{$questionKey}.texto"] = ['required', 'string'];
                    $messages["{$questionKey}.texto.required"] = 'Responda esta pergunta.';
                    break;
                case 'radio':
                    if ($pergunta->opcoes->isEmpty()) {
                        break;
                    }
                    $rules["{$questionKey}.opcao"] = [
                        'required',
                        Rule::in($pergunta->opcoes->pluck('id')->all()),
                    ];
                    $messages["{$questionKey}.opcao.required"] = 'Escolha uma opção.';
                    break;
                case 'checkbox':
                    if ($pergunta->opcoes->isEmpty()) {
                        break;
                    }
                    $rules["{$questionKey}.opcao"] = ['required', 'array', 'min:1'];
                    $rules["{$questionKey}.opcao.*"] = [
                        'distinct',
                        Rule::in($pergunta->opcoes->pluck('id')->all()),
                    ];
                    $messages["{$questionKey}.opcao.required"] = 'Selecione ao menos uma opção.';
                    break;
                default:
                    break;
            }
        }

        $validated = $request->validate($rules, $messages);

        DB::transaction(function () use ($pesquisa, $membro, $validated) {
            foreach ($pesquisa->perguntas as $pergunta) {
                $payload = $validated['respostas'][$pergunta->id] ?? null;

                if ($payload === null) {
                    continue;
                }

                $resposta = Resposta::firstOrNew([
                    'pesquisa_id' => $pesquisa->id,
                    'pergunta_id' => $pergunta->id,
                    'membro_id' => $membro->id,
                ]);

                $resposta->resposta_texto = $pergunta->tipo === 'texto'
                    ? trim((string) ($payload['texto'] ?? ''))
                    : null;
                $resposta->save();

                $resposta->opcoes()->delete();

                if ($pergunta->tipo === 'texto') {
                    continue;
                }

                if ($pergunta->tipo === 'radio') {
                    $opcaoId = (int) $payload['opcao'];
                    $resposta->opcoes()->create(['opcao_id' => $opcaoId]);
                    $opcaoSelecionada = $pergunta->opcoes->firstWhere('id', $opcaoId);
                    if ($opcaoSelecionada) {
                        $resposta->resposta_texto = $opcaoSelecionada->texto;
                        $resposta->save();
                    }
                }

                if ($pergunta->tipo === 'checkbox') {
                    $opcaoIds = collect($payload['opcao'] ?? [])
                        ->map(fn ($value) => (int) $value)
                        ->unique()
                        ->all();

                    foreach ($opcaoIds as $opcaoId) {
                        $resposta->opcoes()->create(['opcao_id' => $opcaoId]);
                    }

                    $textoSelecionado = $pergunta->opcoes
                        ->whereIn('id', $opcaoIds)
                        ->pluck('texto')
                        ->implode(', ');

                    if ($textoSelecionado !== '') {
                        $resposta->resposta_texto = $textoSelecionado;
                        $resposta->save();
                    }
                }
            }
        });

        return redirect()
            ->route('pesquisas.replies.show', $pesquisa->id)
            ->with('msg', 'Respostas registradas com sucesso!');
    }

    protected function resolveMembro()
    {
        $user = auth()->user();

        abort_unless($user && $user->membro, 403, 'Apenas membros podem responder pesquisas.');

        return $user->membro;
    }
}
