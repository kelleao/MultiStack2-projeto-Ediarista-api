<?php

namespace App\Actions\Diarista;

use App\Models\Cidade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\ConsultaCidade\ConsultaCidadeInterface;


class DefinirCidadesAtendidas{

    public function __construct(
        private ConsultaCidadeInterface $consultaCidade
    ){}

    /**
     * Define as cidades que o(a) diarista atende
     *
     * @param array $cidades
     * @return array
     */
    public function executar(array $cidades): array
    {
        Gate::authorize('tipo-diarista');

        foreach ($cidades as $cidade){
            //validar o código do ibge na api
            $cidadeValida = $this->consultaCidade->codigoIBGE($cidade['codigo_ibge']);

            $cidadeModel = Cidade::firstOrCreate(
                ['codigo_ibge' => $cidade['codigo_ibge']],
                [
                    'cidade' => $cidadeValida->nome,
                    'codigo_ibge' => $cidade['codigo_ibge'],
                    'estado' => $cidadeValida->estado
                ]
            );

            $cidadesIds[] = $cidadeModel->id;


        }

        $diarista = Auth::user();

        return $diarista->cidadesAtendidas()->sync($cidadesIds);
    }
}
