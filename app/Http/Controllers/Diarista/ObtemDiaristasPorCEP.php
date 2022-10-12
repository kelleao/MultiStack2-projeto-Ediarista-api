<?php

namespace App\Http\Controllers\Diarista;

use App\Http\Requests\CepRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Actions\Diarista\ObterDiaristasPorCEP;
use App\Http\Resources\DiaristasPublicoCollection;
use App\Services\ConsultaCEP\ConsultaCEPInterface;


class ObtemDiaristasPorCEP extends Controller
{
    public function __construct(
        private ObterDiaristasPorCEP $obterDiaristasPorCEP
    ){}

    /**
     * Busca diaristas pelo CEP
     *
     * @param CepRequest $request
     * @param ConsultaCEPInterface $servicoCEP
     * @return DiaristasPublicoCollection|JsonResponse
     */
    public function __invoke(CepRequest $request): DiaristasPublicoCollection|JsonResponse    
    {
       [ $diaristasCollection, $quantidadeDiaristas] = $this->obterDiaristasPorCEP->executar($request->cep);   
               

        return new DiaristasPublicoCollection(
            $diaristasCollection,
            $quantidadeDiaristas
        );
    }
}
