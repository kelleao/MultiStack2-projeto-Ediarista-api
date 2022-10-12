<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class PrazoInicioDiaria implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $dataHoraInicioDiaria = Carbon::parse($value);
        $dataInicioMinima = Carbon::now()->addHours(48);

        return $dataHoraInicioDiaria > $dataInicioMinima;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'A data de atendimento deve ser maior que 48 horas da data atual';
    }
}
