<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DiaristasPublicoCollection extends ResourceCollection
{
    public static $wrap = 'diaristas';

    /**
     * Guardar a quantidade de diaristas -6
     *
     * @var integer
     */
    private int $quantidadeDiaristas;

    public function __construct($resource, int $quantidadeDiaristas)
    {
        parent::__construct($resource);
        $this->quantidadeDiaristas = $quantidadeDiaristas - 6;        
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'diaristas' => DiaristaPublico::collection($this->collection),
            'quantidade_diaristas' => $this->quantidadeDiaristas > 0 ?
                                                $this->quantidadeDiaristas : 0
        ];
    }
}
