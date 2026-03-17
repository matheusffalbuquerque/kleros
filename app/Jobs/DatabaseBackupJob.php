<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DatabaseBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de tentativas
     */
    public int $tries = 3;

    /**
     * Timeout em segundos
     */
    public int $timeout = 300; // 5 minutos

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Iniciando job de backup do banco de dados');

        try {
            Artisan::call('db:backup');
            $output = Artisan::output();
            
            Log::info('Job de backup do banco de dados executado com sucesso', [
                'output' => $output
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao executar job de backup do banco de dados', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de backup do banco de dados falhou', [
            'erro' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
