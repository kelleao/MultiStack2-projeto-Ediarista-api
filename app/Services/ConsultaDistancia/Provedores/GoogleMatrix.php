<?php

namespace App\Services\ConsultaDistancia\Provedores;

use App\Services\ConsultaDistancia\DistanciaResponse;
use TeamPickr\DistanceMatrix\Licenses\StandardLicense;
use TeamPickr\DistanceMatrix\Response\DistanceMatrixResponse;
use App\Services\ConsultaDistancia\ConsultaDistanciaInterface;
use TeamPickr\DistanceMatrix\Frameworks\Laravel\DistanceMatrix;
use App\Services\ConsultaDistancia\EnderecoNaoEncontratoException;

class GoogleMatrix implements ConsultaDistanciaInterface
{
    public function __construct(
        private StandardLicense $license
    )
    {}

    /**
     * Retorna a distancia entre dois ceps
     *
     * @param string $origem
     * @param string $destino
     * @return DistanciaResponse
     */
    public function distanciaEntreDoisCeps(string $origem, string $destino): DistanciaResponse
    {
        $response = DistanceMatrix::license($this->license)
            ->addOrigin($this->formataCep($origem))
            ->addDestination($this->formataCep($destino))
            ->request();

            $this->verificaResposta($response);

            return new DistanciaResponse(
                $response->json['rows'][0]['elements'][0]['distance']['value'] / 1000
            );
    }

    /**
     * Valida e formata o cep
     *
     * @param string $cep
     * @return string
     */
    private function formataCep(string $cep): string
    {
        $this->verificaPadraoCep($cep);

        return substr_replace($cep, '-', 5, 0);
    }

    /**
     * Verifica o tamanho e padrão do cep
     *
     * @param string $cep
     * @return void
     */
    private function verificaPadraoCep(string $cep): void
    {
        if(strlen($cep) !== 8){
            throw new \Exception('O cep deve ter 8 digitos', 1);
        }

        if(!preg_match('/^[0-9]+$/', $cep)){
            throw new \Exception('O cep deve ter apenas números', 1);
        }
    }

    /**
     * Verifica se o calculo de distancia foi realizado corretamente
     *
     * @param DistanceMatrixResponse $response
     * @return void
     */
    private function verificaResposta(DistanceMatrixResponse $response): void
    {
        $status = $response->json['rows'][0]['elements'][0]['status'];

        if($status !== 'OK') {
            throw new EnderecoNaoEncontratoException;

        }

    }
}
