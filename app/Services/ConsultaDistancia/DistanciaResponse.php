<?php

namespace App\Services\ConsultaDistancia;

class DistanciaResponse {

    public function __construct(
        public float $distanciaEmQuilometros
    )
    {}
}
