<?php

namespace App\Actions\Diaria\Cancelamento;

use Carbon\Carbon;
use App\Models\Diaria;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Tasks\Usuario\AtualizaReputacao;
use App\Checkers\Diaria\ValidaStatusDiaria;
use Illuminate\Validation\ValidationException;
use App\Tasks\Pagamento\EstornarPagamentoCliente;

class Cancelar
{
    public function __construct(
        private ValidaStatusDiaria $validaStatusDiaria,
        private AtualizaReputacao $atualizaReputacao,
        private EstornarPagamentoCliente $estornarPagamentoCliente
    )
    {}

    /**
     * Realiza o cancelamento de uma diária
     *
     * @param Diaria $diaria
     * @param string $motivoCancelamento
     * @return void
     */
    public function executar(Diaria $diaria, string $motivoCancelamento): void
    {
        $this->validaStatusDiaria->executar($diaria, [2, 3]);
        $this->verificaDataAtendimento($diaria->data_atendimento);

        Gate::authorize('dono-diaria', $diaria);

        $diaria->cancelar($motivoCancelamento);

        $this->penalizacao($diaria);

    }

    /**
     * Verifica se já não passou da data de atendimento no momento do cancelamento
     *
     * @param string $dataAtendimento
     * @return void
     */
    private function verificaDataAtendimento(string $dataAtendimento): void
    {
        $dataAtendimento = new Carbon($dataAtendimento);
        $agora = Carbon::now();

        if($agora > $dataAtendimento){
            throw ValidationException::withMessages([
                'data_atendimento' => 'Não é mais possível cancelar a diária.' .
                    'Entre em contato com nosso suporte'
            ]);
        }
    }

    /**
     * Define a penalização para os usuários dos tipos cliente e diarista
     *
     * @param Diaria $diaria
     * @return void
     */
    private function penalizacao(Diaria $diaria): void
    {
        $naoTemPenalidade = $this->verificaSeNaoTemPenalizacao($diaria->data_atendimento);
        $tipoUsuario = Auth::user()->tipo_usuario;

        if($tipoUsuario == '2') {
            $this->penalizacaoDiarista($diaria, $naoTemPenalidade);
            $naoTemPenalidade = true;
        }

        $this->estornarPagamentoCliente->executar($diaria, $naoTemPenalidade);
    }

    /**
     * Verifica pela data de atendimento se tem ou não penalização
     *
     * @param string $dataAtendimento
     * @return boolean
     */
    private function verificaSeNaoTemPenalizacao(string $dataAtendimento): bool
    {
        $dataAtendimento = new Carbon($dataAtendimento);

        $diferencaEmHoras = Carbon::now()->diffInHours($dataAtendimento, false);

        return $diferencaEmHoras > 24;
    }

    /**
     * Verifica se tem penalização para o diarista, se tiver penaliza
     *
     * @param Diaria $diaria
     * @param boolean $naoTemPenalidade
     * @return void
     */
    private function penalizacaoDiarista(Diaria $diaria, bool $naoTemPenalidade): void
    {
        if($naoTemPenalidade) {
            return;
        }

        $usuarioLogadoId = Auth::user()->id;

        $diaria->avaliacoes()->create([
            'nota' => 1,
            'descricao' => 'penalização diária cancelada',
            'avaliado_id' => $usuarioLogadoId,
            'visibilidade' => 0,
            'diaria_id' => $diaria->id
        ]);

        $this->atualizaReputacao->executar($usuarioLogadoId);
    }
}
