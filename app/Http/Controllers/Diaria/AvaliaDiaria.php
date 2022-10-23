<?php

namespace App\Http\Controllers\Diaria;

use App\Models\Diaria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Actions\Diaria\AvaliarDiaria;

class AvaliaDiaria extends Controller
{
    public function __construct(
        private AvaliarDiaria $avaliarDiaria
    )
    {}

    /**
     * Define a avaliação do usuário logado para a diária
     *
     * @param Diaria $diaria
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Diaria $diaria, Request $request): JsonResponse
    {
        $request->validate([
            'nota' => ['required', 'integer', 'min:0', 'max:5'],
            'descricao' => ['required', 'string']
        ]);

        $this->avaliarDiaria->executar($diaria, $request->all());

        return resposta_padrao(
            'diaria avaliada com sucesso',
            'success',
            200
        );
    }
}
