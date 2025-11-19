<?php

namespace App\Services;

use App\Mail\AniversarioParabensMail;
use App\Models\Membro;
use App\Models\MensagemPersonalizada;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AniversarioMensagemService
{
    private const TIPO_ANIVERSARIO = 'aniversario';

    public function enviarMensagensParaHoje(?string $assunto = null, ?string $mensagem = null): int
    {
        $congregacao = app('congregacao');

        if (! $congregacao || ! Schema::hasTable('mensagens_personalizadas')) {
            return 0;
        }

        $config = MensagemPersonalizada::query()
            ->where('congregacao_id', $congregacao->id)
            ->where('tipo', self::TIPO_ANIVERSARIO)
            ->first();

        if (! $config || ! $config->envio_automatico || blank($config->mensagem)) {
            Log::info('Envio de aniversário não realizado: mensagem não configurada ou envio automático desativado.', [
                'congregacao_id' => $congregacao->id,
            ]);
            return 0;
        }

        $assunto = $config->assunto ?: 'Feliz aniversário!';
        $mensagem = $config->mensagem;

        $membros = Membro::query()
            ->where('congregacao_id', $congregacao->id)
            ->where('ativo', true)
            ->whereNotNull('data_nascimento')
            ->whereMonth('data_nascimento', now()->month)
            ->whereDay('data_nascimento', now()->day)
            ->get();

        foreach ($membros as $membro) {
            $this->enviarParaMembro($membro, $assunto, $mensagem);
        }

        return $membros->count();
    }

    public function enviarParaMembro(Membro $membro, string $assunto, string $mensagem): void
    {
        if (! $membro->data_nascimento) {
            return;
        }

        $mensagemFormatada = $this->formatarMensagem($mensagem, $membro);

        if (filled($membro->email)) {
            try {
                Mail::to($membro->email)->send(new AniversarioParabensMail($membro, $assunto, $mensagemFormatada));
            } catch (\Throwable $exception) {
                Log::error('Falha ao enviar e-mail de aniversário.', [
                    'membro_id' => $membro->id,
                    'email' => $membro->email,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        $this->enviarMensagemInterna($membro, $assunto, $mensagemFormatada);
    }

    protected function formatarMensagem(string $mensagem, Membro $membro): string
    {
        $primeiroNome = Str::of($membro->nome)->explode(' ')->first();

        return str_replace(
            [':nome', ':primeiro_nome', ':congregacao'],
            [$membro->nome, $primeiroNome, optional($membro->congregacao)->nome ?? 'sua igreja'],
            $mensagem
        );
    }

    protected function enviarMensagemInterna(Membro $membro, string $assunto, string $mensagem): void
    {
        $autorId = $this->resolveAutorId($membro);

        if (! $autorId) {
            Log::warning('Não foi possível identificar autor para enviar mensagem interna de aniversário.', [
                'membro_id' => $membro->id,
            ]);
            return;
        }

        try {
            AvisoService::enviar([
                'titulo' => $assunto,
                'mensagem' => $mensagem,
                'membros' => [$membro->id],
                'para_todos' => false,
                'criado_por' => $autorId,
            ]);
        } catch (\Throwable $exception) {
            Log::error('Falha ao enviar mensagem interna de aniversário.', [
                'membro_id' => $membro->id,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    protected function resolveAutorId(Membro $membro): ?int
    {
        if (auth()->check()) {
            return auth()->id();
        }

        return User::query()
            ->where('congregacao_id', $membro->congregacao_id)
            ->role('gestor')
            ->value('id')
            ?? User::query()->role('admin')->value('id');
    }
}
