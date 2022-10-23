<?php

namespace App\Actions\Diaria\EscolheDiarista;

use App\Models\Diaria;
use App\Tasks\Diarista\SelecionaDiaristaIndice;

class SelecionaAutomaticamente
{
    public function __construct(
        private SelecionaDiaristaIndice $selecionaDiaristaIndice
    ) {
    }

    /**
     * Busca as diárias pagas com mais de 24 horas de criação
     * e escolhe o(a) diarista mais apropriado(a)
     *
     * @return void
     */
    public function executar(): void
    {
        $diarias = Diaria::pagasComMaisDe24Horas();

        foreach ($diarias as $diaria){

            if($diaria->candidatas_count === 1){
                $diaria->confirmar($diaria->candidatas[0]->diarista_id);
            }

            if($diaria->candidatas_count > 1){
                $diaria->confirmar(
                    $this->selecionaDiaristaIndice->executar($diaria)
                );
            }
        }


    }
}
