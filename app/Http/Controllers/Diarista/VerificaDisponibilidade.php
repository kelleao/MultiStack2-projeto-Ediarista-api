<?php

namespace App\Http\Controllers\Diarista;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\CepRequest;
use App\Http\Controllers\Controller;
use App\Actions\Diarista\ObterDiaristasPorCEP;

class VerificaDisponibilidade extends Controller
{
    public function __construct(
        private ObterDiaristasPorCEP $obterDiaristasPorCEP
    ){}

    /**
     * Retorna a disponibilidade de diaristas para um CEP
     *
     * @param CepRequest $request
     * @return JsonResponse
     */
    public function __invoke(CepRequest $request): JsonResponse
    {
        [$diaristasCollection] = $this->obterDiaristasPorCEP->executar($request->cep);

        return resposta_padrao(
            "Disponibilidade verificada com sucesso",
            "success",
            200,
           [ "diponibilidade" => $diaristasCollection->isNotEmpty() ? true : false]
        );
    }
}
