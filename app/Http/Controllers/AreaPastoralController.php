<?php

namespace App\Http\Controllers;

use App\Models\AreaPastoralPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AreaPastoralController extends Controller
{
    public function index(): View
    {
        $congregacao = app('congregacao');

        $posts = AreaPastoralPost::with('autor')
            ->where('congregacao_id', $congregacao->id)
            ->where('status', 'publicado')
            ->orderByDesc('publicado_em')
            ->orderByDesc('created_at')
            ->get();

        $postSelecionado = $posts->first();

        return view('areapastoral.index', [
            'congregacao' => $congregacao,
            'posts' => $posts,
            'postSelecionado' => $postSelecionado,
        ]);
    }

    public function painel(Request $request): View
    {
        $this->authorizePrincipalGestor();

        $congregacao = app('congregacao');

        $query = AreaPastoralPost::with('autor')
            ->where('congregacao_id', $congregacao->id)
            ->orderByDesc('publicado_em')
            ->orderByDesc('created_at');

        if ($request->filled('tipo')) {
            $query->where('tipo_conteudo', $request->input('tipo'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $posts = $query->paginate(8)->withQueryString();

        return view('areapastoral.painel', [
            'congregacao' => $congregacao,
            'posts' => $posts,
            'filtros' => $request->only(['tipo', 'status']),
        ]);
    }

    public function formCriar(): View
    {
        $this->authorizePrincipalGestor();

        $congregacao = app('congregacao');

        return view('areapastoral.includes.form_criar', [
            'congregacao' => $congregacao,
            'tiposConteudo' => $this->tiposConteudo(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizePrincipalGestor();

        $congregacao = app('congregacao');

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'tipo_conteudo' => ['required', 'in:' . implode(',', array_keys($this->tiposConteudo()))],
            'status' => ['required', 'in:rascunho,publicado'],
            'publicado_em' => ['nullable', 'date'],
            'resumo' => ['nullable', 'string', 'max:280'],
            'conteudo' => ['nullable', 'string'],
            'link_externo' => ['nullable', 'url'],
            'video_url' => ['nullable', 'url'],
        ], [
            'titulo.required' => 'Informe um título para o conteúdo.',
            'tipo_conteudo.required' => 'Selecione o tipo do conteúdo.',
            'status.required' => 'Informe o status do conteúdo.',
        ]);

        $slug = Str::slug($validated['titulo']);
        $slugOriginal = $slug;
        $contador = 1;

        while (AreaPastoralPost::where('slug', $slug)->exists()) {
            $slug = $slugOriginal . '-' . $contador;
            $contador++;
        }

        $usuario = auth()->user();
        $membroId = optional(optional($usuario)->membro)->id ?? null;

        $dados = array_merge($validated, [
            'slug' => $slug,
            'congregacao_id' => $congregacao->id,
            'autor_id' => $membroId,
            'publicado_em' => $validated['status'] === 'publicado'
                ? ($validated['publicado_em'] ?? now())
                : null,
        ]);

        DB::transaction(function () use ($dados) {
            AreaPastoralPost::create($dados);
        });

        return redirect()
            ->route('areapastoral.painel')
            ->with('msg', 'Conteúdo pastoral cadastrado com sucesso!');
    }

    protected function authorizePrincipalGestor(): void
    {
        $user = auth()->user();

        if (! ($user && $user->hasRole('principal') && $user->hasAnyRole(['gestor', 'admin', 'kleros']))) {
            abort(403, 'Você não tem permissão para acessar o painel pastoral.');
        }
    }

    /**
     * @return array<string, string>
     */
    protected function tiposConteudo(): array
    {
        return [
            'texto' => 'Texto',
            'link' => 'Link externo',
            'ebook' => 'E-book',
            'apostila' => 'Apostila',
            'imagem' => 'Imagem',
            'video' => 'Vídeo',
            'geral' => 'Geral',
        ];
    }
}
