<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Models\Diaria;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('tipo-cliente', function (User $user) {
            return $user->tipo_usuario == 1;
        });

        Gate::define('tipo-diarista', function (User $user) {
            return $user->tipo_usuario == 2;
        });

        Gate::define('dono-diaria', function (User $user, Diaria $diaria){
            if($user->tipo_usuario == 1){
                return $diaria->cliente_id === $user->id;
            }

            return $diaria->diarista_id === $user->id;
        });
    }
}
