<?php

namespace App\Services\ConsultaDistancia;

class EnderecoNaoEncontratoException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Endereço não encontrado na hora do calculo', 1);
    }


}
