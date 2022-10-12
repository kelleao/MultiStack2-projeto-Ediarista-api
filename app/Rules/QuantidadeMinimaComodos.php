<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class QuantidadeMinimaComodos implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(
        private Request $request
    )
    {}

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $totalComodos = 0;

        $totalComodos += $this->request->quantidade_quartos;
        $totalComodos += $this->request->quantidade_salas;
        $totalComodos += $this->request->quantidade_cozinhas;
        $totalComodos += $this->request->quantidade_banheiros;
        $totalComodos += $this->request->quantidade_quintais;
        $totalComodos += $this->request->quantidade_outros;

        return $totalComodos > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'A diária deve ter ao menos 1 cômodo selecionado';
    }
}
