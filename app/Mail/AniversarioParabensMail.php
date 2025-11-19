<?php

namespace App\Mail;

use App\Models\Membro;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AniversarioParabensMail extends Mailable
{
    use Queueable, SerializesModels;

    public Membro $membro;
    public string $mensagem;

    public function __construct(Membro $membro, public string $assuntoEmail, string $mensagem)
    {
        $this->membro = $membro;
        $this->mensagem = $mensagem;
    }

    public function build(): self
    {
        return $this->subject($this->assuntoEmail)
            ->view('emails.aniversario')
            ->with([
                'membro' => $this->membro,
                'mensagem' => $this->mensagem,
            ]);
    }
}
