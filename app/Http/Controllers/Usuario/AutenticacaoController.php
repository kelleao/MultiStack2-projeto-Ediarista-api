<?php

namespace App\Http\Controllers\Usuario;

use Illuminate\Http\Request;
use App\Http\Resources\Usuario;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AutenticacaoController extends Controller
{
    /**
     * Realiza o login a partir do email e senha
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $credenciais = $request->only(['email', 'password']);

        if(!$token = Auth::attempt($credenciais)){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return resposta_token($token);
    }

    /**
     * Retorna os dados do usuário logado atualmente
     *
     * @return Usuario
     */
    public function me(): Usuario
    {
        return new Usuario(Auth::user());
    }

    /**
     * Invalida o token passado no header
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::logout();

        return response()->json([
            'message' => "Successfully logged out"
        ]);
    }

    /**
     * Renova o token enviado no header
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return resposta_token(Auth::refresh());
    }
}
