<?php

namespace App\Mail;

use App\Models\Congregacao;
use App\Models\Membro;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CongregacaoGestorBoasVindas extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Congregacao $congregacao,
        public User $gestor,
        public ?Membro $membro,
        public string $senhaTemporaria
    ) {
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $congregacao = $this->congregacao->fresh([
            'dominio',
            'denominacao',
            'cidade',
            'estado',
            'pais',
        ]) ?? $this->congregacao;

        $gestor = $this->gestor->fresh(['membro']) ?? $this->gestor;

        $scheme = parse_url(config('app.url'), PHP_URL_SCHEME) ?: 'https';
        $port = parse_url(config('app.url'), PHP_URL_PORT);
        $portSuffix = $port ? ':' . $port : '';
        $loginUrl = optional($congregacao->dominio)->dominio
            ? "{$scheme}://" . $congregacao->dominio->dominio . $portSuffix . '/login'
            : route('login', [], true);

        return $this
            ->subject(__('congregations.emails.gestor_welcome.subject'))
            ->view('emails.congregacoes.gestor-boas-vindas')
            ->with([
                'congregacao' => $congregacao,
                'gestor' => $gestor,
                'membro' => $gestor->membro ?? $this->membro,
                'senhaTemporaria' => $this->senhaTemporaria,
                'loginUrl' => $loginUrl,
            ]);
    }
}
