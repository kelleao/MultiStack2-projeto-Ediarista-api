<?php

namespace App\Actions\Diaria\EscolheDiarista;

use Carbon\Carbon;
use App\Models\Diaria;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Checkers\Diaria\ValidaStatusDiaria;
use App\Tasks\Diarista\SelecionaDiaristaIndice;
use Illuminate\Validation\ValidationException;

class CandidatarDiarista
{

    public function __construct(
        private ValidaStatusDiaria $validaStatusDiaria,
        private SelecionaDiaristaIndice $selecionaDiarista
    ) {
    }

    /**
     * Difine um candidato(a) para diárias com criação menor que 24 horas
     * E define diretamente o(a) diarista para a diária caso criação maior que 24 horas
     *
     * @param Diaria $diaria
     * @return boolean|Model
     */
    public function executar(Diaria $diaria): bool|Model
    {
        Gate::authorize('tipo-diarista');

        $this->validaStatusDiaria->executar($diaria, 2);
        $this->verificaEnderecoDiarista();

        $diaristaId = Auth::user()->id;

        if ($this->criadaAMenosDe24Horas($diaria)) {
            $this->verificaDuplicidadeDeCandidato($diaria);

            $diaria->defineCandidato($diaristaId);

            return $this->selecionarDiaristaInstantaneamente($diaria);
        }

        return $diaria->confirmar($diaristaId);
    }

    /**
     * Verifica se o diarista tem endereço cadastrado
     *
     * @return void
     */
    private function verificaEnderecoDiarista(): void
    {
        $quantidadeEndereco = Auth::user()->enderecoDiarista()->count();

        if ($quantidadeEndereco === 0) {
            throw ValidationException::withMessages([
                'endereco_diarista' => 'O diarista deve ter o endereço cadastrado'
            ]);
        }
    }

    /**
     * Verifica se o usuário já está candidatado para a diária
     *
     * @param Diaria $diaria
     * @return void
     */
    private function verificaDuplicidadeDeCandidato(Diaria $diaria): void
    {
        $diaristaCandidato = $diaria->candidatas()
            ->where('diarista_id', Auth::user()->id)
            ->first();
        if ($diaristaCandidato) {
            throw ValidationException::withMessages([
                'data_criacao' => 'O/A diarista já é candidato(a) dessa diária'
            ]);
        }
    }

    /**
     * Verifica se a diária foi criada a menos de 24 horas
     *
     * @param Diaria $diaria
     * @return bool
     */
    private function criadaAMenosDe24Horas(Diaria $diaria): bool
    {
        $dataCriacaoDiaria = new Carbon($diaria->created_at);
        $quantidadeDehorasDesdeACriacao = $dataCriacaoDiaria->diffInHours(Carbon::now(), false);
        return $quantidadeDehorasDesdeACriacao < 24;
    }

    /**
     * Seleciona diarista automaticamente quando for o terceiro candidato
     *
     * @param Diaria $diaria
     * @return boolean|null
     */
    public function selecionarDiaristaInstantaneamente(Diaria $diaria): bool
    {
        $quantidadeCandidatas = $diaria->candidatas()->count();

        if ($quantidadeCandidatas === 3) {
            return $diaria->confirmar(
                $this->selecionaDiarista->executar($diaria)
            );
        }

        return false;
    }
}
