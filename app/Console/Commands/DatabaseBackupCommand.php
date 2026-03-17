<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DatabaseBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--test : Gerar dump para ambiente de teste}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Gera backup do banco de dados e mantém apenas os 2 últimos arquivos';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔄 Iniciando backup do banco de dados...');

        try {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $date = Carbon::now()->format('Y-m-d');
            
            // Diretórios
            $backupDir = storage_path('backups/database');
            $testDir = '/var/www/klerostest';
            
            // Criar diretório se não existir
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            // Nome do arquivo
            $filename = "kleros_backup_{$timestamp}.sql";
            $filepath = "{$backupDir}/{$filename}";

            // Configurações do banco
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            // Comando mysqldump
            $command = sprintf(
                'mysqldump -h %s -u %s -p%s %s > %s 2>&1',
                escapeshellarg($host),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($filepath)
            );

            // Executar backup
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                $this->error('❌ Erro ao gerar backup: ' . implode("\n", $output));
                Log::error('Erro ao gerar backup do banco de dados', [
                    'output' => $output,
                    'return_var' => $returnVar
                ]);
                return Command::FAILURE;
            }

            // Verificar se o arquivo foi criado
            if (!File::exists($filepath)) {
                $this->error('❌ Arquivo de backup não foi criado');
                return Command::FAILURE;
            }

            $filesize = File::size($filepath);
            $filesizeMB = number_format($filesize / 1024 / 1024, 2);

            $this->info("✅ Backup criado: {$filename} ({$filesizeMB} MB)");

            // Copiar para ambiente de teste
            if (File::exists($testDir)) {
                $testFilename = "kleros_dump_{$date}.sql";
                $testFilepath = "{$testDir}/{$testFilename}";
                
                File::copy($filepath, $testFilepath);
                $this->info("✅ Backup copiado para ambiente de teste: {$testFilename}");
                
                Log::info('Backup do banco copiado para ambiente de teste', [
                    'arquivo' => $testFilename,
                    'tamanho' => $filesizeMB . ' MB'
                ]);
            } else {
                $this->warn("⚠️  Ambiente de teste não encontrado em: {$testDir}");
            }

            // Limpar backups antigos (manter apenas os 2 mais recentes)
            $this->cleanOldBackups($backupDir);

            Log::info('Backup do banco de dados gerado com sucesso', [
                'arquivo' => $filename,
                'tamanho' => $filesizeMB . ' MB'
            ]);

            $this->info('✅ Backup concluído com sucesso!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Erro ao gerar backup: ' . $e->getMessage());
            Log::error('Erro ao gerar backup do banco de dados', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Remove backups antigos, mantendo apenas os 2 mais recentes
     */
    private function cleanOldBackups(string $backupDir): void
    {
        $files = File::glob("{$backupDir}/kleros_backup_*.sql");
        
        if (count($files) <= 2) {
            return;
        }

        // Ordenar por data de modificação (mais recente primeiro)
        usort($files, function ($a, $b) {
            return File::lastModified($b) - File::lastModified($a);
        });

        // Remover arquivos excedentes
        $filesToDelete = array_slice($files, 2);
        
        foreach ($filesToDelete as $file) {
            File::delete($file);
            $this->info("🗑️  Backup antigo removido: " . basename($file));
            Log::info('Backup antigo removido', ['arquivo' => basename($file)]);
        }

        $this->info("✅ Mantidos apenas os 2 backups mais recentes");
    }
}
