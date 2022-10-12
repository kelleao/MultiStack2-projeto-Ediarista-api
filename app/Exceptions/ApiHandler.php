<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait ApiHandler
{
    /**
     * Trata as exceções da nossa API
     *
     * @param \Throwable $e
     * @return JsonResponse
     */
    protected function getJsonException(\Throwable $e): JsonResponse
    {
        if($e instanceof ModelNotFoundException){
            return $this->notFoundException($e);
        }


        if($e instanceof ValidationException) {
            return $this->validationException($e);
        }

        if($e instanceof AuthenticationException) {
            return $this->authenticationException($e);
        }

        if($e instanceof TokenBlacklistedException) {
            return $this->authenticationException($e);
        }

        if ($e instanceof AuthorizationException) {
            return $this->authorizationException($e);
        }

        if($e instanceof HttpException){
            return $this->httpException($e);
        }

            return $this->genericException($e);
    }

    /**
     * Retorna uma resposta para erro de model não encontrado
     *
     * @return JsonResponse
     */
    protected function notFoundException(): JsonResponse
    {
        return resposta_padrao(
            'Recurso não encontrado',
            'not_found_error',
            404
        );
    }


    /**
     * Retornar uma resposta para erro de validação
     *
     * @param ValidationException $e
     * @return JsonResponse
     */
    protected function validationException(ValidationException $e): JsonResponse
    {
        return resposta_padrao("Erro de validação dos dados enviados",  "validation_error", 400, $e->errors());
    }

    /**
     * Retorna uma resposta para o erro de autenticação
     *
     * @param AuthenticationException $e
     * @return JsonResponse
     */
    protected function authenticationException(AuthenticationException|TokenBlacklistedException $e): JsonResponse
    {
        return resposta_padrao($e->getMessage(), 'token_not_valid', 401);
    }

    /**
     * Retorna uma resposta para o erro de autorização
     *
     * @param AuthorizationException $e
     * @return JsonResponse
     */
    protected function authorizationException(
        AuthorizationException $e
    ): JsonResponse {
        return resposta_padrao(
            $e->getMessage(),
            'authorizarion_error',
            401
        );
    }

    /**
     * Retornar mensagens de erro HTTP
     *
     * @param HttpException $e
     * @return JsonResponse
     */
    protected function httpException(HttpException $e): JsonResponse
    {
        return resposta_padrao(
            $e->getMessage(),
            'http_error',
            $e->getStatusCode()
        );
    }

    /**
     * Retorna uma resposta para erro genérico
     *
     * @param \Throwable $e
     * @return JsonResponse
     */
    protected function genericException(\Throwable $e): JsonResponse
    {
        dd($e);
        return resposta_padrao("erro interno no servidor", "internal_error", 500);

    }
}
