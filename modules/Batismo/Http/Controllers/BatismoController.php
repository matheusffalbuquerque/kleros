<?php

namespace Modules\Batismo\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Membro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Batismo\Models\BatismoAgendado;

class BatismoController extends Controller
{
    public function index(Request $request): View
    {
        $congregacao = app('congregacao');

        abort_if(! $congregacao, 404, 'Congregação não encontrada.');

        $agendamentos = BatismoAgendado::query()
            ->daCongregacao()
            ->with('membro')
            ->orderByDesc('data_batismo')
            ->get();

        $pendingAgendamentos = $agendamentos->where('concluido', false);

        $validPending = $pendingAgendamentos->filter(function (BatismoAgendado $agendamento) {
            $membro = $agendamento->membro;
            return $membro && ! $membro->batizado;
        });

        $pendingMemberIds = $validPending->pluck('membro_id')->unique()->values()->all();

        $agendamentosPendentesPorMembro = $validPending
            ->sortBy('data_batismo')
            ->groupBy('membro_id')
            ->map(fn ($group) => $group->first());
        $proximoBatismo = $validPending->sortBy('data_batismo')->first();

        $statusFilter = $request->string('status')->lower()->value();
        $statusFilter = in_array($statusFilter, ['batizados', 'nao_batizados', 'em_preparacao'], true)
            ? $statusFilter
            : 'todos';

        $membrosQuery = Membro::daCongregacao()
            ->where('ativo', true);

        if ($statusFilter === 'batizados') {
            $membrosQuery->where('batizado', true);
        } elseif ($statusFilter === 'nao_batizados') {
            $membrosQuery->where('batizado', false);
            if (! empty($pendingMemberIds)) {
                $membrosQuery->whereNotIn('id', $pendingMemberIds);
            }
        } elseif ($statusFilter === 'em_preparacao') {
            $membrosQuery->where('batizado', false);
            $membrosQuery->whereIn('id', ! empty($pendingMemberIds) ? $pendingMemberIds : [-1]);
        }

        $membros = $membrosQuery
            ->orderBy('nome')
            ->paginate(10)
            ->withQueryString();

        return view('batismo::painel', [
            'appName' => config('app.name'),
            'congregacao' => $congregacao,
            'membros' => $membros,
            'statusFilter' => $statusFilter,
            'agendamentos' => $agendamentos,
            'agendamentosPendentesPorMembro' => $agendamentosPendentesPorMembro,
            'proximoBatismo' => $proximoBatismo,
        ]);
    }

    public function modalAgendar(Request $request): View
    {
        $congregacao = app('congregacao');

        abort_if(! $congregacao, 404, 'Congregação não encontrada.');

        $membrosSemBatismo = Membro::daCongregacao()
            ->where('ativo', true)
            ->where('batizado', false)
            ->orderBy('nome')
            ->get(['id', 'nome']);

        $statusFilter = $request->string('status')->value() ?: 'todos';

        $rawSelection = $request->input('membros', []);
        if (empty($rawSelection) && $request->filled('membro_id')) {
            $rawSelection = [$request->input('membro_id')];
        }

        $selectedMembros = collect($rawSelection)
            ->map(fn ($value) => (int) $value)
            ->filter();

        return view('batismo::includes.agendamento-modal', [
            'membrosSemBatismo' => $membrosSemBatismo,
            'statusFilter' => $statusFilter,
            'selectedMembros' => $selectedMembros->values()->all(),
            'dataBatismo' => $request->input('data_batismo'),
        ]);
    }

    public function agendar(Request $request): RedirectResponse
    {
        $congregacao = app('congregacao');

        abort_if(! $congregacao, 404, 'Congregação não encontrada.');

        $validated = $request->validate([
            'membros' => ['required', 'array', 'min:1'],
            'membros.*' => ['integer', 'exists:membros,id'],
            'data_batismo' => ['required', 'date'],
        ]);

        $membros = Membro::daCongregacao()
            ->whereIn('id', $validated['membros'])
            ->where('ativo', true)
            ->get();

        if ($membros->isEmpty() || $membros->count() !== count($validated['membros'])) {
            return redirect()
                ->route('batismo.painel')
                ->withErrors(['membros' => 'Nenhum membro válido selecionado para esta congregação.'])
                ->withInput($request->all());
        }

        foreach ($membros as $membro) {
            BatismoAgendado::create([
                'membro_id' => $membro->id,
                'congregacao_id' => $congregacao->id,
                'data_batismo' => $validated['data_batismo'],
                'concluido' => false,
            ]);
        }

        return redirect()
            ->route('batismo.painel', array_filter([
                'status' => $request->input('status'),
            ]))
            ->with('success', $membros->count() > 1 ? 'Batismos agendados com sucesso.' : 'Batismo agendado com sucesso.');
    }

    public function atualizarStatus(Request $request, BatismoAgendado $agendamento): RedirectResponse
    {
        $congregacao = app('congregacao');

        abort_if(! $congregacao, 404, 'Congregação não encontrada.');

        if ((int) $agendamento->congregacao_id !== (int) $congregacao->id) {
            abort(403, 'Agendamento não pertence a esta congregação.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:pendente,concluido'],
        ]);

        $isConcluido = $validated['status'] === 'concluido';

        $agendamento->update([
            'concluido' => $isConcluido,
        ]);

        if ($isConcluido && $agendamento->membro && ! $agendamento->membro->batizado) {
            $agendamento->membro->data_batismo = $agendamento->data_batismo;
            $agendamento->membro->batizado = true;
            $agendamento->membro->save();
        }

        return redirect()
            ->route('batismo.painel')
            ->with('success', 'Status do agendamento atualizado.');
    }
}
