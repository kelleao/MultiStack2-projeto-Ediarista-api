<?php

namespace App\Actions\Usuario;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class DefinirFotoPerfil
{
    /**
     * Define a foto do perfil do usuário
     *
     * @param UploadedFile $fotoUsuario
     * @return integer
     */
    public function executar(UploadedFile $fotoUsuario): int
    {
        $usuario = Auth::user();

        $usuario->foto_usuario = $fotoUsuario->store('public');

        return $usuario->update();
    }
}
