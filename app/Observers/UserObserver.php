<?php

namespace App\Observers;

use App\Models\User;
use App\Mail\UsuarioCadastrado;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    /**
     * Definir a reputação antes de criar o usuário
     *
     * @param User $user
     * @return void
     */
    public function creating(User $user): void
    {
        if(User::count() === 0){
            $user->reputacao = 5;
            return;
        }

        $user->reputacao = User::avg('reputacao');
    }

    /**
     * Envio do email de boas vindas para o usuário
     *
     * @param User $user
     * @return void
     */
    public function created(User $user): void
    {
        Mail::to($user->email)->send(new UsuarioCadastrado($user));
    }
}

