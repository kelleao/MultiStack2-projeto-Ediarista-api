<?php

namespace App\Tasks\Pagamento;

use App\Models\Diaria;
use App\Services\Pagamento\TransacaoResponse;
use App\Services\Pagamento\PagamentoInterface;
use Illuminate\Validation\ValidationException;

class EstornarPagamentoCliente
{
    public function __construct(
        private PagamentoInterface $pagamento
    )
    {}

    /**
     * Realiza o estorno no gateway de pagamento
     *
     * @param Diaria $diaria
     * @param boolean $estornoCompleto
     * @return void
     */
    public function executar(Diaria $diaria, bool $estornoCompleto = true): void
    {
        $valor = $this->valor($diaria, $estornoCompleto);

        $pagamento = $diaria->pagamentoValido();

        $transacao = $this->realizaEstornoGateway($pagamento->transacao_id, $valor);

        $this->guardaTransacaoBancoDeDados($diaria, $pagamento->transacao_id, $valor);

        $this->validaStatusEstorno($transacao, $valor);
    }

    /**
     * Chama o serviço para realizar o estorno no gateway
     *
     * @param integer $transacaoId
     * @param float $valorEstorno
     * @return TransacaoResponse
     */
    private function realizaEstornoGateway(int $transacaoId, float $valorEstorno): TransacaoResponse
    {
        try {
            $valorEstorno = intval($valorEstorno * 100);

            $transacao = $this->pagamento->estornar([
                'id' => $transacaoId,
                'amount' => $valorEstorno
        ]);
        } catch (\Throwable $exception) {
            throw ValidationException::withMessages([
                'pagamento' => $exception->getMessage()
            ]);
        }

        return $transacao;
    }

    /**
     * Guarda a transação no banco de dados local
     *
     * @param Diaria $diaria
     * @param integer $transacaoId
     * @param float $valor
     * @return void
     */
    private function guardaTransacaoBancoDeDados(Diaria $diaria, int $transacaoId, float $valor): void
    {
        $diaria->pagamentos()->create([
            'status' => 'estornado',
            'transacao_id' => $transacaoId,
            'valor' => $valor
        ]);
    }

    /**
     * Valida se o status da trasanção está correto para o estorno
     *
     * @param TransacaoResponse $transacao
     * @param float $valorEstorno
     * @return void
     */
    public function validaStatusEstorno(TransacaoResponse $transacao, float $valorEstorno): void
    {
        $valor = intval($valorEstorno * 100);

        if($transacao->valorEstornado !== $valor){
            throw ValidationException::withMessages([
                'pagamento' => 'Não foi possível extornar o pagamento'
            ]);
        }
    }

    /**
     * Retorna o valor do estorno
     *
     * @param Diaria $diaria
     * @param boolean $estornoCompleto
     * @return float
     */
    private function valor(Diaria $diaria, bool $estornoCompleto): float
    {
        return $estornoCompleto ? $diaria->preco : $diaria->preco / 2;
    }
}
