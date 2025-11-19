<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Escolaridade;
use App\Models\EstadoCiv;
use App\Models\Membro;
use App\Models\MembroStatusHistorico;
use App\Models\Ministerio;
use App\Models\User;
use App\Models\Feed;
use App\Models\MensagemPersonalizada;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MembroController extends Controller
{
    private $congregacao;

    public function __construct()
    {
        $this->congregacao = app('congregacao');
    }

    public function adicionar() {
        $escolaridade = Escolaridade::all();
        $ministerios = Ministerio::all();
        $estado_civil = EstadoCiv::all();

        return view('/membros/cadastro', [
            'escolaridade' => $escolaridade, 
            'ministerios' => $ministerios, 
            'estado_civil' => $estado_civil,
            'congregacao' => $this->congregacao
        ]);
    }

    public function store(Request $request) {

        $membro = new Membro;

        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:100'],
            'data_nascimento' => ['required', 'date'],
            'sexo' => ['nullable', 'in:Masculino,Feminino'],
        ], [
            'nome.required' => __('members.validation.name_required'),
            'telefone.required' => __('members.validation.phone_required'),
            'data_nascimento.required' => __('members.validation.birth_required'),
        ]);

        $membro->congregacao_id = $this->congregacao->id;
        $membro->nome = $request->nome;
        $membro->rg = $request->rg;
        $membro->cpf = $request->cpf;
        $membro->data_nascimento = $request->data_nascimento;
        $membro->sexo = $request->sexo;
        $membro->telefone = $request->telefone;
        $membro->email = $request->email;
        $membro->estado_civ_id = $request->estado_civil;
        $membro->escolaridade_id = $request->escolaridade;
        $membro->profissao = $request->profissao;
        $membro->endereco = $request->endereco;
        $membro->numero = $request->numero;
        $membro->bairro = $request->bairro;
        $membro->data_batismo= $request->data_batismo;
        $membro->denominacao_origem= $request->denominacao_origem;
        $membro->ministerio_id = $request->ministerio;
        $membro->nome_paterno = $request->nome_paterno;
        $membro->nome_materno = $request->nome_materno;
        $membro->created_at = date('Y-m-d H:i:s');
        $membro->updated_at = date('Y-m-d H:i:s');

        $msg = __('members.flash.created', ['name' => $request->nome]);
        if($membro->save()){
            $user = new User;

            $partes = explode(' ', trim($request->nome));
            $user->name = strtolower($partes[0] . '.' . end($partes)) . $membro->id;            
            $user->email = $request->email;
            $user->password = bcrypt('1q2w3e4r');
            $user->congregacao_id = $this->congregacao->id;
            $user->membro_id = $membro->id;

            $user->save();

            $responsavel = optional(Auth::user())->membro;

            MembroStatusHistorico::create([
                'congregacao_id' => $membro->congregacao_id ?? $this->congregacao->id,
                'membro_id' => $membro->id,
                'status' => MembroStatusHistorico::STATUS_ATIVO,
                'descricao' => 'Passou a fazer parte desta congregação.',
                'data_status' => now(),
                'membro_responsavel_id' => optional($responsavel)->id,
            ]);
        }

        return redirect()->route('membros.adicionar')->with('msg', $msg);
    }

    public function painel() {

        $congregacao = $this->congregacao;
        $membros = Membro::where('congregacao_id', $congregacao->id)
            ->where('ativo', true)
            ->orderBy('nome')
            ->paginate(10);

        return view('/membros/painel', ['membros' => $membros, 'congregacao' => $congregacao, 'showingInactives' => false]);
    }

    public function aniversariantes(Request $request)
    {
        $congregacao = $this->congregacao;

        $mesSelecionado = (int) $request->input('mes', now()->month);
        if ($mesSelecionado < 1 || $mesSelecionado > 12) {
            $mesSelecionado = now()->month;
        }

        $nomeFiltro = trim((string) $request->input('nome'));

        $membrosQuery = Membro::where('congregacao_id', $congregacao->id)
            ->where('ativo', true)
            ->whereNotNull('data_nascimento')
            ->whereMonth('data_nascimento', $mesSelecionado);

        if ($nomeFiltro !== '') {
            $membrosQuery->where('nome', 'like', '%' . $nomeFiltro . '%');
        }

        $membros = $membrosQuery
            ->orderByRaw('DAY(data_nascimento), nome')
            ->get();

        $meses = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro',
        ];

        return view('membros.aniversariantes', [
            'congregacao' => $congregacao,
            'membros' => $membros,
            'mesSelecionado' => $mesSelecionado,
            'meses' => $meses,
            'nomeFiltro' => $nomeFiltro,
        ]);
    }

    public function configurarMensagemAniversariantes()
    {
        $congregacao = $this->congregacao;

        abort_if(! $congregacao, 404);

        $configMensagem = MensagemPersonalizada::firstOrNew([
            'congregacao_id' => $congregacao->id,
            'tipo' => 'aniversario',
        ]);

        return view('membros.includes.aniversariantes_config', [
            'configMensagem' => $configMensagem,
        ]);
    }

    public function salvarMensagemAniversariantes(Request $request)
    {
        $congregacao = $this->congregacao;

        abort_if(! $congregacao, 404);

        $dados = $request->validate([
            'assunto' => ['required', 'string', 'max:255'],
            'mensagem' => ['required', 'string'],
            'envio_automatico' => ['required', 'in:0,1'],
        ]);

        $configMensagem = MensagemPersonalizada::updateOrCreate(
            [
                'congregacao_id' => $congregacao->id,
                'tipo' => 'aniversario',
            ],
            [
                'assunto' => $dados['assunto'],
                'mensagem' => $dados['mensagem'],
                'envio_automatico' => (bool) $dados['envio_automatico'],
            ]
        );

        return redirect()
            ->route('membros.aniversariantes')
            ->with('msg', 'Mensagem de aniversário salva com sucesso.');
    }

    public function inativos() {

        $congregacao = $this->congregacao;
        $membros = Membro::where('congregacao_id', $congregacao->id)
            ->where('ativo', false)
            ->orderBy('nome')
            ->paginate(10);

        return view('/membros/painel', ['membros' => $membros, 'congregacao' => $congregacao, 'showingInactives' => true]);
    }

    public function search(Request $request) {

        $allowedFilters = ['nome', 'telefone', 'email'];
        $filter = $request->input('filtro', 'nome');
        $keyword = $request->input('chave');
        $showInactives = $request->input('showInactives', false);

        $query = Membro::where('congregacao_id', app('congregacao')->id)
            ->where('ativo', $showInactives ? false : true);

        if ($keyword !== null && $keyword !== '') {
            $column = in_array($filter, $allowedFilters, true) ? $filter : 'nome';
            $query->where($column, 'LIKE', '%' . $keyword . '%');
        }

        $membros = $query->orderBy('nome')->get();

        // Renderiza a view com os resultados
        $view = view('membros/includes/painel_search', ['membros' => $membros])->render();

        // Retorna a view renderizada como parte da resposta JSON
        return response()->json(['view' => $view]);
    }

    public function export(Request $request)
    {
        $allowedFilters = ['nome', 'telefone', 'email'];
        $filter = $request->input('filtro');
        $keyword = $request->input('chave');

        $query = Membro::where('congregacao_id', $this->congregacao->id)
            ->with('ministerio')
            ->orderBy('nome');

        if ($filter && in_array($filter, $allowedFilters, true) && $keyword !== null && $keyword !== '') {
            $query->where($filter, 'LIKE', '%' . $keyword . '%');
        }

        $membros = $query->get();

        $filename = __('members.export.filename_prefix') . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = __('members.export.headers');
        $notInformed = __('members.common.statuses.not_informed');

        $callback = function () use ($membros) {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            // BOM UTF-8 to help spreadsheet tools recognise encoding
            fwrite($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $headers = __('members.export.headers');
            if (!is_array($headers)) {
                $headers = ['Nome', 'Telefone', 'Endereço', 'Número', 'Bairro', 'Ministério'];
            }

            fputcsv($handle, $headers, ';');

            foreach ($membros as $membro) {
                fputcsv($handle, [
                    $membro->nome,
                    $membro->telefone,
                    $membro->endereco,
                    $membro->numero,
                    $membro->bairro,
                    optional($membro->ministerio)->titulo ?? __('members.common.statuses.not_informed'),
                ], ';');
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function show($id) {

        $membro = Membro::findOrFail($id);
        $congregacao = app('congregacao');
        
        return view('/membros/exibir', ['membro' => $membro, 'congregacao' => $congregacao]);
    }

    public function editar($id) {

        $membro = Membro::findOrFail($id);
        $estado_civil = EstadoCiv::all();;
        $escolaridade = Escolaridade::all();
        $ministerio = Ministerio::daDenominacao()->get();
        $ultimoHistoricoDesligado = $membro->statusHistorico()
            ->where('status', MembroStatusHistorico::STATUS_DESLIGADO)
            ->latest('data_status')
            ->first();

        return view('/membros/editar', [
            'membro' => $membro, 
            'estado_civil' => $estado_civil, 
            'escolaridade' => $escolaridade, 
            'ministerios' => $ministerio,
            'congregacao' => $this->congregacao,
            'ultimoMotivoDesligamento' => optional($ultimoHistoricoDesligado)->descricao,
        ]);
    }

    public function form_editar($id) {

        $membro = Membro::findOrFail($id);
        $estado_civil = EstadoCiv::all();;
        $escolaridade = Escolaridade::all();
        $ministerio = Ministerio::daDenominacao()->get();
        $ultimoHistoricoDesligado = $membro->statusHistorico()
            ->where('status', MembroStatusHistorico::STATUS_DESLIGADO)
            ->latest('data_status')
            ->first();

        return view('/membros/includes/form_editar', [
            'membro' => $membro, 
            'estado_civil' => $estado_civil, 
            'escolaridade' => $escolaridade, 
            'ministerios' => $ministerio,
            'congregacao' => $this->congregacao,
            'ultimoMotivoDesligamento' => optional($ultimoHistoricoDesligado)->descricao,
        ]);
    }

    public function update(Request $request, $id) {

        $membro = Membro::findOrFail($id);

        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:100'],
            'data_nascimento' => ['required', 'date'],
            'sexo' => ['nullable', 'in:Masculino,Feminino'],
        ], [
            'nome.required' => __('members.validation.name_required'),
            'telefone.required' => __('members.validation.phone_required'),
            'data_nascimento.required' => __('members.validation.birth_required'),
        ]);

        $statusAnterior = (int) $membro->getOriginal('ativo');
        $novoMotivoDesligamento = trim((string) $request->input('motivo_desligamento', ''));
        if ($novoMotivoDesligamento === '') {
            $novoMotivoDesligamento = null;
        }
        $novoStatusAtivo = $request->has('ativo') ? (int) $request->input('ativo') : 1;

        $membro->fill([
            'nome' => $request->nome,
            'rg' => $request->rg,
            'cpf' => $request->cpf,
            'data_nascimento' => $request->data_nascimento,
            'sexo' => $request->sexo,
            'telefone' => $request->telefone,
            'email' => $request->email,
            'estado_civ_id' => $request->estado_civil,
            'escolaridade_id' => $request->escolaridade,
            'profissao' => $request->profissao,
            'endereco' => $request->endereco,
            'numero' => $request->numero,
            'bairro' => $request->bairro,
            'data_batismo' => $request->data_batismo,
            'denominacao_origem' => $request->denominacao_origem,
            'ministerio_id' => $request->ministerio,
            'data_consagracao' => $request->data_consagracao,
            'nome_paterno' => $request->nome_paterno,
            'nome_materno' => $request->nome_materno,
            'ativo' => $novoStatusAtivo,
        ]);

        // Salva as alterações
        if ($membro->save()) {
            $responsavel = optional(Auth::user())->membro;
            if ($novoMotivoDesligamento && (string) $novoStatusAtivo === '0') {
                MembroStatusHistorico::create([
                    'congregacao_id' => $membro->congregacao_id ?? $this->congregacao->id,
                    'membro_id' => $membro->id,
                    'status' => MembroStatusHistorico::STATUS_DESLIGADO,
                    'descricao' => $novoMotivoDesligamento,
                    'data_status' => now(),
                    'membro_responsavel_id' => optional($responsavel)->id,
                ]);
            }

            if ($statusAnterior === 0 && (string) $novoStatusAtivo === '1') {
                MembroStatusHistorico::create([
                    'congregacao_id' => $membro->congregacao_id ?? $this->congregacao->id,
                    'membro_id' => $membro->id,
                    'status' => MembroStatusHistorico::STATUS_ATIVO,
                    'descricao' => 'Voltou a ser membro desta congregação.',
                    'data_status' => now(),
                    'membro_responsavel_id' => optional($responsavel)->id,
                ]);
            }

            return redirect()->back()->with('msg', __('members.flash.updated'));
        }
    }
    
    public function destroy($id) {

       $membro = Membro::find($id);

        if (!$membro) {
            return redirect()->route('membros.painel')->with('msg-error', __('members.flash.not_found'));
        }

        $membro->delete();

        return redirect()->route('membros.painel')->with('msg', __('members.flash.deleted'));
    }

    public function perfil() {
        $membro = Auth::user()->membro;
        $noticias = Feed::where('categoria', 'noticia')
            ->where('fonte', 'guiame')
            ->limit(9)->get();

        return view('/perfil/edicao', [
            'membro' => $membro, 
            'destaques' => $noticias,
            'congregacao' => $this->congregacao
        ]);
    }

    public function save_perfil($id) {
        $membro = Membro::findOrFail($id);

        $request = request();

        $request->validate([
            'nome' => 'required',
        ], [
            'nome.required' => __('members.validation.name_required'),
        ]);

        $membro->nome = $request->nome;
        $membro->telefone = $request->telefone;
        $membro->email = $request->email;
        $membro->biografia = $request->biografia;
        $membro->foto = $request->file('foto') ? $request->file('foto')->store('fotos', 'public') : $membro->foto;
        // $membro->estado_civ_id = $request->estado_civil;
        // $membro->escolaridade_id = $request->escolaridade;
        // $membro->profissao = $request->profissao;
        // $membro->endereco = $request->endereco;
        // $membro->numero = $request->numero;
        // $membro->bairro = $request->bairro;
        // $membro->data_batismo= $request->data_batismo;
        // $membro->denominacao_origem= $request->denominacao_origem;
        // $membro->ministerio_id = $request->ministerio;
        // $membro->nome_paterno = $request->nome_paterno;
        // $membro->nome_materno = $request->nome_materno;

        // Atualiza os timestamps
        $membro->updated_at = date('Y-m-d H:i:s');

        // Se veio senha atual e nova senha
        if ($request->filled('senha_atual') && $request->filled('nova_senha')) {
            
            $user = $membro->user; // relação membro → user

            if ($user && Hash::check($request->senha_atual, $user->password)) {
                
                $user->password = Hash::make($request->nova_senha);
                $user->save();

            } else {
                return redirect()->back()->with('msg-error', __('members.flash.password_mismatch'));
            }
        }

        // Salva as alterações
        if ($membro->save()) {

            return redirect()->route('perfil')->with('msg', __('members.flash.profile_updated'));

        } else {
            return redirect()->back()->with('msg-error', __('members.flash.profile_error'));
        }
    }

    public function removerFoto($id)
    {
        try {
            $membro = Membro::findOrFail($id);

            // Verifica se o membro tem foto
            if ($membro->foto) {
                // Remove o arquivo físico do storage
                $fotoPath = 'public/' . $membro->foto;
                if (Storage::exists($fotoPath)) {
                    Storage::delete($fotoPath);
                }

                // Atualiza o registro no banco
                $membro->foto = null;
                $membro->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Foto removida com sucesso!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Membro não possui foto.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover foto: ' . $e->getMessage()
            ], 500);
        }
    }
}
