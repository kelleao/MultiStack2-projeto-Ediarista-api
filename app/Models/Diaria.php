<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Diaria extends Model
{
    use HasFactory;

    /**
     * Campos bloqueados na definação de dados em massa
     */
    protected $guarded = ['motivo_cancelamento', 'created_at', 'updated_at'];

    /**
     * Define a relação com serviço
     *
     * @return BelongsTo
     */
    public function servico(): BelongsTo
    {
        return $this->belongsTo(Servico::class);
    }

    /**
     * Define a relação com cliente
     *
     * @return BelongsTo
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    /**
     * Define a relação com diarista
     *
     * @return BelongsTo
     */
    public function diarista(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diarista_id');
    }

    /**
     * Define a relação com os candidatos a realizar a diária
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function candidatas()
    {
        return $this->hasMany(CandidataDiaria::class);
    }

    /**
     * Define a relação com as avaliações do cliente e diarista
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function avaliacoes(): HasMany
    {
        return $this->hasMany(Avaliacao::class);
    }

    /**
     * Define a relação com os pagamentos
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }

    /**
     * Define o status da diária como pago
     *
     * @return boolean
     */
    public function pagar(): bool
    {
        $this->status = 2;
        return $this->save();
    }

    /**
     * Retornar as diárias do usuário
     *
     * @param User $usuario
     * @return Collection
     */
    static public function todasDoUsuario(User $usuario): Collection
    {
        return self::when($usuario->tipo_usuario === 1, function($q) use($usuario){
            $q->where('cliente_id', $usuario->id);
        })->when($usuario->tipo_usuario === 2, function($q) use($usuario){
            $q->where('diarista_id', $usuario->id);
        })->get();
    }

    /**
     * Define um candidato(a) para a diária
     *
     * @param integer $diaristaId
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function defineCandidato(int $diaristaId)
    {
        return $this->candidatas()->create([
            'diarista_id' => $diaristaId
        ]);
    }

    /**
     * Define o diarista para realizar a diária e muda o status da diária para confirmado
     *
     * @param integer $diaristaId
     * @return boolean
     */
    public function confirmar(int $diaristaId): bool
    {
        $this->diarista_id = $diaristaId;
        $this->status = '3';
        return $this->save();
    }

    /**
     * Retorna a lista de oportunidade para o diarista
     *
     * @param User $diarista
     * @return Collection
     */
    static public function oportunidadesPorCidade(User $diarista): Collection
    {
        $cidadesAtentidasPeloDiarista = $diarista->cidadesAtentidasDiarista();

        return self::where('status', '2')
            ->whereIn('codigo_ibge', $cidadesAtentidasPeloDiarista)
            ->has('candidatas', '<', 3)
            ->whereDoesntHave('candidatas', function (Builder $query) use ($diarista){
                $query->where('diarista_id', $diarista->id);
            })
            ->get();
    }

    /**
     * Retorna a lista de oportunidade para o diarista
     *
     * @return Collection
     */
    static public function pagasComMaisDe24Horas(): Collection
    {
        return self::where('status', 2)
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->with('candidatas', 'candidatas.candidata.enderecoDiarista')
            ->withCount('candidatas')
            ->get();
    }

    /**
     * Verifica se o usuário já avaliou a diária
     *
     * @param integer $usuarioId
     * @return boolean
     */
    public function usuarioJaAvaliou(int $usuarioId): bool
    {
        return !! $this->avaliacoes()->where('avaliador_id', $usuarioId)->first();
    }

}
