<?php

namespace App\Services\Pagamento\Provedores;

use PagarMe\Client;
use App\Services\Pagamento\TransacaoResponse;
use App\Services\Pagamento\PagamentoInterface;

class Pagarme implements PagamentoInterface
{
    public function __construct(
        private Client $pagarmeSDK
    ) {
    }

    /**
     * Realiza a transação com o gateway de pagamento Pagarme
     *
     * @param array $dados
     * @return TransacaoResponse
     */
    public function pagar(array $dados): TransacaoResponse
    {
        $transaction = $this->pagarmeSDK->transactions()->create($dados);

        return new TransacaoResponse(
            $transaction->id,
            $transaction->status
        );

    }
}
