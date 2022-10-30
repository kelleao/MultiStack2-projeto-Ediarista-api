<?php

namespace App\Http\Controllers\Pagamento;

use App\Http\Controllers\Controller;
use App\Http\Resources\PagamentoCollection;
use App\Actions\Pagamento\ObterPagamentosDiarista;

class ObtemPagamentoDiarista extends Controller
{
    public function __construct(
        private ObterPagamentosDiarista $obterPagamentosDiarista
    )
    {}


    /**
     * Retorna a lista de diarias como pagamento do(a) diarista
     *
     * @return PagamentoCollection
     */
    public function __invoke(): PagamentoCollection
    {
        $pagamento = $this->obterPagamentosDiarista->executar();

        return new PagamentoCollection($pagamento);
    }
}
