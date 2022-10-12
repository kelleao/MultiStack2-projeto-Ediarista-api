<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CandidataDiaria extends Model
{
    use HasFactory;

    /**
     * Define campos permitidos na definição de dados em massa
     *
     * @var array
     */
    protected $fillable = ['diaria_id', 'diarista_id'];

    /**
     * Define o nome da tabela do model
     *
     * @var string
     */
    protected $table = 'candidatas_diaria';

    /**
     * Define a relação com o candidato para realizar a diária
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function candidata()
    {
        return $this->belongsTo(User::class, 'diarista_id');
    }


}
