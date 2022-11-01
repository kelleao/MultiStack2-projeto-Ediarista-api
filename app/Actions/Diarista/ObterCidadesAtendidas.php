<?php

namespace App\Actions\Diarista;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Collection;

class ObterCidadesAtendidas
{
    /**
     * Obtem a lista de cidades atendidas pelo(a) diarista
     *
     * @return Collection
     */
    public function executar(): Collection
    {
        Gate::authorize('tipo-diarista');

        $diarista = Auth::user();

        return $diarista->cidadesAtendidas()->get();
    }
}
