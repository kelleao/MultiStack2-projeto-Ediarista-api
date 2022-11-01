<?php

namespace App\Actions\Diarista;

use App\Models\Endereco;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ObterEndereco
{
    /**
     * Obtem o endereço do(a) diarista
     *
     * @return Endereco
     */
    public function executar(): Endereco
    {
        Gate::authorize('tipo-diarista');

        $diarista = Auth::user();

        $endereco = $diarista->enderecoDiarista()->first();

        if(!$endereco) {
            throw new HttpException(404, 'endereço não cadastrado');
        }

        return $endereco;
    }
}
