<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Resources\Usuario;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Actions\Usuario\CriarUsuario;
use App\Actions\Usuario\AtualizarUsuario;
use App\Http\Requests\UsuarioCadastroRequest;
use App\Http\Requests\UsuarioAlteracaoRequest;

class CadastroController extends Controller
{
    public function __construct(
        private CriarUsuario $criarUsuario,
        private AtualizarUsuario $atualizarUsuario
    ){}

    /**
     * Cria um usuário no sistema
     *
     * @param UsuarioCadastroRequest $request
     * @return Usuario
     */
    public function store(UsuarioCadastroRequest $request): Usuario
    {
        $usuario = $this->criarUsuario->executar(
            $request->except('password_confirmation'),
            $request->foto_documento
        );

        $token = Auth::attempt([
            'email' => $usuario->email,
            'password' => $request->password
        ]);

        return new Usuario($usuario, $token);
    }

    /**
     * Atualiza os dados do usuário
     *
     * @param UsuarioAlteracaoRequest $request
     * @return JsonResponse
     */
    public function update(UsuarioAlteracaoRequest $request): JsonResponse
    {
        $this->atualizarUsuario->executar($request->except('password_confirmation'));

        return resposta_padrao('Usuário Atualizado com sucesso', 'success', 200);
    }

}
