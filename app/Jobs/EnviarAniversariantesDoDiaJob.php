<?php

namespace App\Jobs;

use App\Services\AniversarioMensagemService;
use App\Models\Congregacao;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarAniversariantesDoDiaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function handle(AniversarioMensagemService $service): void
    {
        $totalEnviados = 0;

        Congregacao::query()->chunk(100, function ($congregacoes) use (&$totalEnviados, $service) {
            foreach ($congregacoes as $congregacao) {
                app()->instance('congregacao', $congregacao);
                $enviados = $service->enviarMensagensParaHoje();
                $totalEnviados += $enviados;
            }
        });

        Log::info('Job AniversariantesDoDia executado.', ['total_enviados' => $totalEnviados]);
    }
}
