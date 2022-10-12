<?php

namespace App\Rules;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class HoraFinalDiaria implements Rule
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
        $inicioAtendimento = CarbonImmutable::parse(
            $this->request->data_atendimento
        );

        $finalAtendimento = $inicioAtendimento->addHours($value);

        $limiteHorarioParaAtendimento = $inicioAtendimento
                                        ->setHour(22)
                                        ->setMinute(0);

        return $finalAtendimento <= $limiteHorarioParaAtendimento;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'A hora de fim da diária deve ser antes das 22:00.';
    }
}
