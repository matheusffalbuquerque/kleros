<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Congregacao; 
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function congregationsReport()
    {
        // 1. Buscar os dados necessários para o relatório
        $congregacoes = Congregacao::with(['cidade', 'estado', 'dominio'])
            ->withCount('membros')
            ->orderBy('identificacao')
            ->get();

        $data = [
            'titulo' => 'Relatório Geral de Congregações',
            'dataEmissao' => now()->format('d/m/Y H:i'),
            'congregacoes' => $congregacoes,
        ];

        // 2. Carregar a view do relatório e passar os dados
        $pdf = Pdf::loadView('admin.reports.congregations', $data);

        // 3. Gerar o PDF para download
        // O nome do arquivo será 'relatorio-congregacoes-DATA.pdf'
        return $pdf->download('relatorio-congregacoes-' . now()->format('Y-m-d') . '.pdf');
    }

    public function congregationReport($id)
    {
        // Buscar a congregação
        $congregacaoReport = Congregacao::with([
            'membros' => function($query) {
                $query->orderBy('nome');
            },
            'cultos' => function($query) {
                $query->orderBy('data_culto', 'desc');
            },
            'cidade',
            'estado', 
            'dominio'
        ])
        ->withCount(['membros', 'cultos'])
        ->find($id);
        
        if (!$congregacaoReport) {
            return redirect()->back()->with('error', 'Congregação não encontrada.');
        }
        
        // Calcular estatísticas
        $estatisticas = [
            'total_membros' => $congregacaoReport->membros_count ?? 0,
            'membros_ativos' => $congregacaoReport->membros()->where('ativo', true)->count(),
            'cultos_ano' => $congregacaoReport->cultos_count ?? 0,
            'ultima_atividade' => optional($congregacaoReport->cultos()->latest('data_culto')->first())->data_culto,
            'frequencia_media' => $congregacaoReport->cultos->avg(function($culto) {
                return ($culto->quant_adultos ?? 0) + ($culto->quant_criancas ?? 0) + ($culto->quant_visitantes ?? 0);
            }) ?? 0
        ];
        
        $data = [
            'titulo' => 'Relatório da Congregação: ' . $congregacaoReport->identificacao,
            'dataEmissao' => now()->format('d/m/Y H:i'),
            'congregacaoReport' => $congregacaoReport, // Nome diferente para evitar conflito
            'estatisticas' => $estatisticas,
        ];
        
        // Gerar PDF
        $pdf = Pdf::loadView('admin.reports.congregation-detail', $data);
        $filename = 'relatorio-' . Str::slug($congregacaoReport->identificacao) . '-' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    public function debugCongregation($id)
    {
        $congregacao = Congregacao::with(['cidade', 'estado', 'dominio', 'membros', 'cultos'])
            ->withCount(['membros', 'cultos'])
            ->findOrFail($id);
            
        return response()->json([
            'congregacao' => $congregacao,
            'titulo' => 'Debug - Congregação: ' . $congregacao->identificacao,
            'tem_membros' => $congregacao->membros->isNotEmpty(),
            'tem_cultos' => $congregacao->cultos->isNotEmpty(),
            'total_membros' => $congregacao->membros->count(),
            'total_cultos' => $congregacao->cultos->count()
        ]);
    }
}
