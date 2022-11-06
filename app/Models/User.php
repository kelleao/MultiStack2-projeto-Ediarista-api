<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Notifications\ResetarSenhaNotification;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome_completo',
        'cpf',
        'nascimento',
        'foto_documento',
        'telefone',
        'tipo_usuario',
        'chave_pix',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'tipo_usuario' => 'int'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Define a relação com as cidades atendidas pelo(a) diarista
     *
     * @return BelongsToMany
     */
    public function cidadesAtendidas(): BelongsToMany
    {
        return $this->belongsToMany(Cidade::class, 'cidade_diarista');
    }

    /**
     * Define a relação do diarista com endereço
     *
     * @return HasOne
     */
    public function enderecoDiarista(): HasOne
    {
        return $this->hasOne(Endereco::class, 'user_id');
    }

    /**
     * Define a relação com a avaliação onde o usuário foi avaliado
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function avaliado(): HasMany
    {
        return $this->hasMany(Avaliacao::class, 'avaliado_id');
    }

    /**
     * Escopo que filtra os(as) diaristas
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeDiarista(Builder $query): Builder
    {
        return $query->where('tipo_usuario', 2);
    }

    /**
     * Escopo que filtra diaristas por código do IBGE
     *
     * @param Builder $query
     * @param integer $codigoIbge
     * @return Builder
     */
    public function scopeDiaristasAtendeCidade(Builder $query, int $codigoIbge): Builder
    {
        return $query->diarista()
            ->whereHas('cidadesAtendidas', function ($q) use ($codigoIbge) {
                $q->where('codigo_ibge', $codigoIbge);
            });
    }

    /**
     * Busca 6 diaristas por código do ibge
     *
     * @param integer $codigoIbge
     * @return Collection
     */
    static public function diaristasDisponivelCidade(int $codigoIbge): Collection
    {
        return User::diaristasAtendeCidade($codigoIbge)->limit(6)->get();
    }

    /**
     * Returna a quantidade de diaristas por código do ibge
     *
     * @param integer $codigoIbge
     * @return int
     */
    static public function diaristasDisponivelCidadeTotal(int $codigoIbge): int
    {
        return User::diaristasAtendeCidade($codigoIbge)->count();
    }

    /**
     * Retorna as cidades atentidas pelo(a) diarista
     *
     * @return array
     */
    public function cidadesAtentidasDiarista(): array
    {
        return $this->cidadesAtendidas()->pluck('codigo_ibge')->toArray();
    }

    /**
     * Chama uma notificação personalizada para o reset de senha
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $url = config('app.client_web_url') . '/recuperar-senha?token=' . $token;

        $this->notify(new ResetarSenhaNotification($url));
    }
}
