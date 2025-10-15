<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Denominacao;
use App\Models\Congregacao;
use App\Models\Dominio;
use App\Models\Membro;

class AdminController extends Controller
{
    public function login()
    {
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('name', 'password');

        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // kleros é um ambiente distinto (acesso ao dashboard global)
            if ($user->hasRole('kleros')) {
                return redirect()->route('admin.dashboard');
            }

            // Apenas usuários com role 'admin' devem acessar a página de gerenciamento
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.manage');
            }

            // Padrão: voltar para login com erro de permissão
            Auth::logout();
            return back()->withErrors(['login' => 'Conta sem permissão para acessar o painel administrativo.']);
        }

        return back()->withErrors(['login' => 'Credenciais inválidas.']);
    }

    public function dashboard()
    {

        $denominacoes = Denominacao::withCount('congregacoes')->orderBy('nome')->get();
        $congregacoes = Congregacao::with(['denominacao', 'cidade', 'estado'])
            ->withCount('membros')
            ->orderByDesc('created_at')
            ->get();
        $dominios = Dominio::all();

        $membersByDenomination = Denominacao::leftJoin('congregacoes', 'congregacoes.denominacao_id', '=', 'denominacoes.id')
            ->leftJoin('membros', 'membros.congregacao_id', '=', 'congregacoes.id')
            ->select('denominacoes.id', 'denominacoes.nome')
            ->selectRaw('COUNT(membros.id) as total_membros')
            ->groupBy('denominacoes.id', 'denominacoes.nome')
            ->orderByDesc('total_membros')
            ->get();

        $stats = [
            'denominacoes' => $denominacoes->count(),
            'congregacoes' => $congregacoes->count(),
            'dominios' => $dominios->count(),
            'membros' => $membersByDenomination->sum('total_membros'),
            'usuarios' => Membro::count(),
        ];

        return view('admin.dashboard', [
            'denominacoes' => $denominacoes,
            'congregacoes' => $congregacoes,
            'dominios' => $dominios,
            'membersByDenomination' => $membersByDenomination,
            'stats' => $stats,
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }

    /**
     * Página de gerenciamento (apenas para administradores)
     */
    public function manage()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Verificação de role 'admin' de forma simplificada
        if (!$user || !$user->hasRole('admin')) {
            Auth::logout();
            return redirect()->route('admin.login')->with('error', 'Você não tem permissão para acessar o painel administrativo.');
        }

        // Carregar todos os dados para admins
        // Determinar denominação do usuário admin
        $denomId = null;
        
        // Primeiro: verificar se o usuário tem denominacao_id diretamente
        if ($user->denominacao_id) {
            $denomId = $user->denominacao_id;
        }
        // Segundo: tentar obter via membro->congregacao->denominacao
        elseif ($user->membro && $user->membro->congregacao && $user->membro->congregacao->denominacao) {
            $denomId = $user->membro->congregacao->denominacao->id;
        }

        // Se não encontrou denominação, mostrar dados globais (para super admin)
        if (!$denomId) {
            // Carregar todos os dados para super admins
            $denominacoes = Denominacao::withCount('congregacoes')->orderBy('nome')->get();
            $congregacoes = Congregacao::with(['denominacao', 'cidade', 'estado'])
                ->withCount('membros')
                ->orderByDesc('created_at')
                ->get();
            $dominios = Dominio::all();
            
            $membersByDenomination = Denominacao::leftJoin('congregacoes', 'congregacoes.denominacao_id', '=', 'denominacoes.id')
                ->leftJoin('membros', 'membros.congregacao_id', '=', 'congregacoes.id')
                ->select('denominacoes.id', 'denominacoes.nome')
                ->selectRaw('COUNT(membros.id) as total_membros')
                ->groupBy('denominacoes.id', 'denominacoes.nome')
                ->orderByDesc('total_membros')
                ->get();

            $stats = [
                'denominacoes' => $denominacoes->count(),
                'congregacoes' => $congregacoes->count(),
                'dominios' => $dominios->count(),
                'membros' => $membersByDenomination->sum('total_membros'),
                'usuarios' => Membro::count(),
            ];
        } else {
            // Filtrar dados apenas da denominação específica
            $denominacoes = Denominacao::where('id', $denomId)
                ->withCount('congregacoes')
                ->get();
            
            $congregacoes = Congregacao::with(['denominacao', 'cidade', 'estado'])
                ->withCount('membros')
                ->where('denominacao_id', $denomId)
                ->orderByDesc('created_at')
                ->get();

            // Domínios das congregações desta denominação
            $dominios = Dominio::whereHas('congregacao', function ($query) use ($denomId) {
                $query->where('denominacao_id', $denomId);
            })->get();

            // Estatísticas apenas desta denominação
            $membersByDenomination = Denominacao::where('denominacoes.id', $denomId)
                ->leftJoin('congregacoes', 'congregacoes.denominacao_id', '=', 'denominacoes.id')
                ->leftJoin('membros', 'membros.congregacao_id', '=', 'congregacoes.id')
                ->select('denominacoes.id', 'denominacoes.nome')
                ->selectRaw('COUNT(membros.id) as total_membros')
                ->groupBy('denominacoes.id', 'denominacoes.nome')
                ->get();

            // Contadores específicos da denominação
            $totalMembros = Membro::whereHas('congregacao', function ($query) use ($denomId) {
                $query->where('denominacao_id', $denomId);
            })->count();

            $stats = [
                'denominacoes' => 1, // Sempre 1 pois estamos vendo apenas uma denominação
                'congregacoes' => $congregacoes->count(),
                'dominios' => $dominios->count(),
                'membros' => $totalMembros,
                'usuarios' => $totalMembros, // Assumindo que usuários = membros neste contexto
            ];
        }

        return view('admin.manage', [
            'denominacoes' => $denominacoes,
            'congregacoes' => $congregacoes,
            'dominios' => $dominios,
            'membersByDenomination' => $membersByDenomination,
            'stats' => $stats,
        ]);
    }
}
?>
