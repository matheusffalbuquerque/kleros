<?php

namespace App\Mail;

use App\Models\Congregacao;
use App\Models\Membro;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class WelcomeNewUser extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Membro $membro;
    public User $user;
    public ?Congregacao $congregacao;

    /**
     * Create a new message instance.
     */
    public function __construct(Membro $membro, User $user, ?Congregacao $congregacao = null)
    {
        $this->membro = $membro;
        $this->user = $user;
        $this->congregacao = $congregacao;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $shortName = optional($this->congregacao)->nome_curto ?: config('app.name');
        $subject = "{$shortName} - Equipe de Boas-Vindas";

        return $this->subject($subject)
            ->view('emails.welcome-user')
            ->with([
                'membro' => $this->membro,
                'user' => $this->user,
                'congregacao' => $this->congregacao,
                'shortName' => $shortName,
                'loginUrl' => url('/login'),
            ]);
    }
}
