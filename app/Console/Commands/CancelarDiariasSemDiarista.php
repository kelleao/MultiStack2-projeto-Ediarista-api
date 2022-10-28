<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\Diaria\Cancelamento\CancelarAutomaticamente;

class CancelarDiariasSemDiarista extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diarias:cancelar:sem-diarista';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancela as diárias com menos de 24 horas para a data de atendimento e que não possuem diaristas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        private CancelarAutomaticamente $cancelarAutomaticamente
    ) {
        parent::__construct();
    }

    /**
     * Cancela automaticamente as diárias com menos de 24 horas para o atendimento
     * e que não possui diarista para realizar o atendimento
     *
     * @return int
     */
    public function handle()
    {
        $this->cancelarAutomaticamente->executar();

        return 0;
    }
}
