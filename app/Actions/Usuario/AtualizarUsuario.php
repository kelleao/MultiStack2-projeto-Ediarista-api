<?php

namespace App\Actions\Usuario;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AtualizarUsuario
{
    /**
     * Atualiza as informações do usuário
     *
     * @param array $dados
     * @return void
     */
    public function executar(array $dados): void
    {
        if(isset($dados['new_password'])) {
            $dados['password'] = Hash::make($dados['new_password']);
        }

        $user = Auth::user();

        $user->update($dados);
    }
}
