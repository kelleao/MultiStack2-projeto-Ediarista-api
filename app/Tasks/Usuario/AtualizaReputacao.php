<?php

namespace App\Tasks\Usuario;

use App\Models\User;

class AtualizaReputacao
{
    /**
     * Calcula a reputação do usuário e salva na tabela de usuário
     *
     * @param integer $usuarioAvaliadoId
     * @return void
     */
    public function executar(int $usuarioAvaliadoId): void
    {
        $usuarioAvaliado = User::find($usuarioAvaliadoId);

        $novaReputacaoUsuario = $usuarioAvaliado->avaliado()->avg('nota');

        $usuarioAvaliado->reputacao = $novaReputacaoUsuario;
        $usuarioAvaliado->save();
    }
}
