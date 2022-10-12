<?php

namespace App\Http\Hateoas;

class HateoasBase
{
    /**
     * Links do hateoas
     *
     * @var array
     */
    protected array $links = [];

    /**
         * Adiciona um link no hateoas
         *
         * @param string $metodo
         * @param string $descricao
         * @param string $nomeRota
         * @param array $parametrosRota
         * @return void
         */
        protected function adicionaLink(
            string $metodo, 
            string $descricao, 
            string $nomeRota, 
            array $parametrosRota = []
        ) {
            $this->links[] = [
                "type" => $metodo,
                "rel" => $descricao,
                "uri" => route($nomeRota, $parametrosRota)
            ];
        }
}