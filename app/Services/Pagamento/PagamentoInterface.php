<?php

namespace App\Services\Pagamento;

interface PagamentoInterface
{
    public function pagar(array $dados): TransacaoResponse;
}
