<?php

namespace App\Actions\Diaria;

use App\Models\Diaria;
use Illuminate\Support\Facades\Gate;
use App\Checkers\Diaria\ValidaStatusDiaria;
use App\Services\Pagamento\TransacaoResponse;
use App\Services\Pagamento\PagamentoInterface;
use Illuminate\Validation\ValidationException;

class PagarDiaria{

    public function __construct(
        private ValidaStatusDiaria $validaStatusDiaria,
        private PagamentoInterface $pagamento
    ){}

    /**
     * Executa o pagamento da diária
     *
     * @param Diaria $diaria
     * @param string $cardHash
     * @return boolean
     */
    public function executar(Diaria $diaria, string $cardHash): bool
    {
        $this->realizaValidacoes($diaria);

        $transacao = $this->realizaTransacaoComGateway($diaria, $cardHash);
        $this->guardaTransacaoBancoDeDados($diaria, $transacao);
        $this->validaStatusPagamento($transacao);

        return $diaria->pagar();
    }

    /**
     * Realiza as validações antes do pagamento
     *
     * @param Diaria $diaria
     * @return void
     */
    private function realizaValidacoes(Diaria $diaria): void
    {
        $this->validaStatusDiaria->executar($diaria, 1);
        Gate::authorize('tipo-cliente');
        Gate::authorize('dono-diaria', $diaria);
    }

    /**
     * Chama o serviço de pagamento para realizar a transação
     *
     * @param Diaria $diaria
     * @param string $cardHash
     * @return TransacaoResponse
     */
    private function realizaTransacaoComGateway(Diaria $diaria, string $cardHash): TransacaoResponse
    {
        try {
            $transacao = $this->pagamento->pagar([
                'amount' => intval($diaria->preco * 100),
                'card_hash' => $cardHash,
                'async' => false
            ]);
        } catch (\Throwable $exception) {
            throw ValidationException::withMessages([
                'pagamento' => $exception->getMessage()
            ]);
        }

        return $transacao;
    }

    /**
     * Salva o resultado da transação do gateway no banco de dados
     *
     * @param Diaria $diaria
     * @param TransacaoResponse $transacao
     * @return void
     */
    private function guardaTransacaoBancoDeDados(Diaria $diaria, TransacaoResponse $transacao): void
    {
        $diaria->pagamentos()->create([
            'status' => $transacao->status === 'paid' ? 'pago' : 'reprovado',
            'transacao_id' => $transacao->transacaoId,
            'valor' => $diaria->preco
        ]);
    }

    /**
     * valida se o status da transação é pago
     * se não for, gera uma exceção
     *
     * @param TransacaoResponse $transacao
     * @return void
     */
    public function validaStatusPagamento(TransacaoResponse $transacao): void
    {
        if($transacao->status !== 'paid'){
            throw ValidationException::withMessages([
                'pagamento' => 'Pagamento Reprovado'
            ]);
        }
    }
}
