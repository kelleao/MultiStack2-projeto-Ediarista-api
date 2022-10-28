<?php

namespace App\Services\Pagamento;

class TransacaoResponse
{
    public function __construct(
        public int $transacaoId,
        public string $status,
        public int $valorEstornado = 0
    ) {
    }
}
