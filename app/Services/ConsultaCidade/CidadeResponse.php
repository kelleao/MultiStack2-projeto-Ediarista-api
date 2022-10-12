<?php

namespace App\Services\ConsultaCidade;

class CidadeResponse{

    /**
     * Define as propriedades os dados da classe
     *
     * @param integer $codigoIbge
     * @param string $nome
     * @param string $estado
     */
    public function __construct(
        public int $codigoIbge,
        public string $nome,
        public string $estado
    ){}
}
