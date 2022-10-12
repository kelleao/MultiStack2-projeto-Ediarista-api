<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


class UsuarioCadastrado extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        private User $user
    )
    {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Bem Vindo(a) ao E-Diaristas')
            ->from('nao-responda@e-diaristas.com.br', "E-Diaristas")
            ->view('email.mensagens.cadastro', [
                'usuario' => $this->user
            ]);
    }

}
