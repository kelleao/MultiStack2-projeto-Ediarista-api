<?php

if (!function_exists('foto_perfil')) {
    /**
     * Cria o caminho para a foto do perfil do usuário
     *
     * @param string $caminhoRelativo
     * @return string|null
     */
    function foto_perfil(string $caminhoRelativo = null): ?string
    {
        return $caminhoRelativo ?
            sprintf('%s/%s', config('app.url'), $caminhoRelativo) :
            null;
    }
}
