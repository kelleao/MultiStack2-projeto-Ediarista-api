<?php

namespace App\Http\Controllers\Usuario;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;

class ResetarSenhaController extends Controller
{
    /**
     * Envia o email com o link de reset de senha
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function solicitarToken(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        Password::sendResetLink(
            $request->only('email')
        );

        return resposta_padrao(
            'Verifique na sua caixa de entrada ou de spam a mensagem',
            'success',
            200
        );
    }

    /**
     * Reseta a senha do usuário no banco de dados
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetarSenha(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
            'token' => ['required']
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'status_reset' => 'Não foi possível alterar a senha'
            ]);
        }

        return resposta_padrao('Senha alterada com sucesso', 'success', 200);
    }
}
