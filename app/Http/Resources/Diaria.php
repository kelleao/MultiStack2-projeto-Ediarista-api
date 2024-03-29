<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Hateoas\Diaria as HateoasDiaria;
use Illuminate\Http\Resources\Json\JsonResource;

class Diaria extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "status" => $this->resource->usuarioJaAvaliou(Auth::user()->id) ? 6 : $this->status,

            "valor_comissao" => $this->valor_comissao,
            "nome_servico" => $this->servico->nome,

            "cliente" => new UsuarioSimplificado($this->cliente),

            "data_atendimento" => Carbon::parse($this->data_atendimento)->toIso8601ZuluString(),
            "tempo_atendimento" => $this->tempo_atendimento,
            "preco" => $this->preco,

            "logradouro" => $this->logradouro,
            "numero" => $this->numero,
            "complemento" => $this->complemento,
            "bairro" => $this->bairro,
            "cidade" => $this->cidade,
            "estado" => $this->estado,
            "cep" => $this->cep,
            "codigo_ibge" => $this->codigo_ibge,

            "quantidade_quartos" => $this->quantidade_quartos,
            "quantidade_salas" => $this->quantidade_salas,
            "quantidade_cozinhas" => $this->quantidade_cozinhas,
            "quantidade_banheiros" => $this->quantidade_banheiros,
            "quantidade_quintais" => $this->quantidade_quintais,
            "quantidade_outros" => $this->quantidade_outros,

            "observacoes" => $this->observacoes,
            "motivo_cancelamento" => $this->motivo_cancelamento,

            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,

            "servico" => $this->servico_id,
            "diarista" => new UsuarioSimplificado($this->diarista),

            "links" => (new HateoasDiaria)->links($this->resource)

        ];
    }
}
