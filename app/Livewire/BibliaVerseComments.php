<?php

namespace App\Livewire;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class BibliaVerseComments extends Component
{
    public int $verseId = 0;
    public string $verseNumber = '';
    public string $verseText = '';
    public array $comments = [];
    public string $newComment = '';
    public bool $showForm = false;

    #[On('setVerse')]
    public function loadVerse($payload = null): void
    {
        if ($payload instanceof \Livewire\Mechanisms\Events\EventPayload) {
            $payload = $payload->params ?? [];
        }

        if (is_object($payload)) {
            $payload = (array) $payload;
        }

        if (! is_array($payload)) {
            $payload = [];
        }

        $this->verseId = (int) ($payload['verseId'] ?? ($payload[0] ?? 0));
        $this->verseNumber = (string) ($payload['verseNumber'] ?? ($payload[1] ?? ''));
        $this->verseText = (string) ($payload['verseText'] ?? ($payload[2] ?? ''));

        $this->resetForm();
        $this->fetchComments();
    }

    public function resetForm(): void
    {
        $this->newComment = '';
        $this->showForm = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function toggleForm(): void
    {
        if (! $this->isAuthorized()) {
            return;
        }

        $this->showForm = ! $this->showForm;

        if (! $this->showForm) {
            $this->newComment = '';
        }
    }

    public function fetchComments(): void
    {
        if (! $this->verseId) {
            $this->comments = [];
            return;
        }

        $congregacaoId = optional(app('congregacao'))->id;

        try {
            $this->comments = DB::table('biblia_comentario_verse as cv')
                ->join('biblia_comentarios as c', 'c.id', '=', 'cv.comentario_id')
                ->join('membros as m', 'm.id', '=', 'c.membro_id')
                ->select('c.id', 'm.nome as autor', 'c.comentario', 'c.criado_em')
                ->where('cv.verse_id', $this->verseId)
                ->when($congregacaoId, function ($query) use ($congregacaoId) {
                    $query->where(function ($scope) use ($congregacaoId) {
                        $scope->whereNull('c.congregacao_id')
                            ->orWhere('c.congregacao_id', $congregacaoId);
                    });
                })
                ->orderByDesc('c.criado_em')
                ->get()
                ->map(function ($row) {
                    return [
                        'id' => $row->id,
                        'autor' => $row->autor,
                        'comentario' => $row->comentario,
                        'criado_em' => $row->criado_em
                            ? Carbon::parse($row->criado_em)->format('d/m/Y H:i')
                            : null,
                    ];
                })
                ->toArray();
        } catch (\Throwable $e) {
            report($e);
            $this->comments = [];
        }
    }

    public function saveComment(): void
    {
        if (! $this->isAuthorized()) {
            return;
        }

        if (! $this->verseId) {
            throw ValidationException::withMessages([
                'verseId' => 'Selecione um versículo válido.',
            ]);
        }

        $data = $this->validate([
            'newComment' => ['required', 'string', 'min:5'],
        ], [
            'newComment.required' => 'Digite o comentário antes de salvar.',
            'newComment.min' => 'O comentário precisa ter pelo menos :min caracteres.',
        ]);

        $user = Auth::user();

        if (! $user || ! $user->membro_id) {
            throw ValidationException::withMessages([
                'newComment' => 'Você precisa estar vinculado como membro para comentar.',
            ]);
        }

        DB::transaction(function () use ($user, $data) {
            $comentarioId = DB::table('biblia_comentarios')->insertGetId([
                'congregacao_id' => optional(app('congregacao'))->id,
                'membro_id' => $user->membro_id,
                'comentario' => $data['newComment'],
                'metadados' => null,
                'criado_em' => now(),
                'atualizado_em' => now(),
            ]);

            DB::table('biblia_comentario_verse')->insert([
                'comentario_id' => $comentarioId,
                'verse_id' => $this->verseId,
                'criado_em' => now(),
            ]);
        });

        $this->resetForm();
        $this->fetchComments();
        $this->dispatch('commentSaved');
    }

    public function isAuthorized(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return $user->hasAnyRole(['gestor', 'admin', 'kleros']);
    }

    public function render()
    {
        return view('livewire.biblia-verse-comments', [
            'canComment' => $this->isAuthorized(),
        ]);
    }
}
