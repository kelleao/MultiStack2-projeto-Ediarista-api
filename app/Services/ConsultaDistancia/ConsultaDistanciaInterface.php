<?php

namespace App\Services\ConsultaDistancia;

interface ConsultaDistanciaInterface {
    public function distanciaEntreDoisCeps(string $origem, string $destino): DistanciaResponse;
}
