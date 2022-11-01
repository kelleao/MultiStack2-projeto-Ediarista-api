<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Auth;
use App\Http\Hateoas\Usuario as HateoasUsuario;
use Illuminate\Http\Resources\Json\JsonResource;

class Usuario extends JsonResource
{
    public function __construct(
        $resource,
        private string $token = ''
    )
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        $formato = [
            "id" => $this->id,
            "nome_completo" => $this->nome_completo,
            "cpf" => $this->cpf,
            "nascimento" => $this->nascimento,
            "email" => $this->email,
            "telefone" => $this->telefone,
            "reputacao" => $this->reputacao,
            "tipo_usuario" => $this->tipo_usuario,
            "foto_usuario" => foto_perfil($this->foto_usuario),
            "chave_pix" => $this->chave_pix,
            "links" => (new HateoasUsuario)->links($this->resource)
        ];

        if($this->token !== ''){
            return $formato + [
                "token" => [
                    "access" => $this->token,
                    "refresh" => $this->token,
                    "token_type" => "bearer",
                    "expires_in" => Auth::factory()->getTTL() * 60
                ]
            ];
        }

        return $formato;
    }
}
