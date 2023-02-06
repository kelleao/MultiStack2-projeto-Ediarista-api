<?php

namespace App\Http\Controllers\Endereco;

use App\Http\Requests\CepRequest;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Services\ConsultaCEP\ConsultaCEPInterface;

class BuscaCepApiExterna extends Controller
{
    public function __construct(
       private ConsultaCEPInterface $consultaCep
    ){}

    /**
     * Retorna os dados de endereço a partir do CEP
     *
     * @param CepRequest $request
     * @return array
     */
    public function __invoke(CepRequest $request): array
    {
        $request->validate([
            'cep' => ['required', 'numeric']
        ]);

        $dadosEndereco = $this->consultaCep->buscar($request->cep);

        if($dadosEndereco === false) {
         throw ValidationException::withMessages(['cep' => 'CEP não encontrado']);
       }

       return (array) $dadosEndereco;
    }
}
