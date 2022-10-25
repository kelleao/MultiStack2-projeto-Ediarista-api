<?php

namespace App\Providers;

use PagarMe\Client;
use Illuminate\Support\ServiceProvider;
use App\Services\Pagamento\Provedores\Pagarme;
use App\Services\Pagamento\PagamentoInterface;
use App\Services\ConsultaCEP\Providers\ViaCEP;
use App\Services\ConsultaCidade\Provedores\Ibge;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\ConsultaCEP\ConsultaCEPInterface;
use TeamPickr\DistanceMatrix\Licenses\StandardLicense;
use App\Services\ConsultaCidade\ConsultaCidadeInterface;
use App\Services\ConsultaDistancia\Provedores\GoogleMatrix;
use App\Services\ConsultaDistancia\ConsultaDistanciaInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ConsultaCEPInterface::class, function($app) {
            return new ViaCEP;
        });

        $this->app->singleton(ConsultaCidadeInterface::class, function($app) {
            return new Ibge;
        });

        $this->app->bind(ConsultaDistanciaInterface::class, function ($app) {
            $license = new StandardLicense(config('google.key'));
            return new GoogleMatrix($license);
        });

        $this->app->singleton(PagamentoInterface::class, function ($app) {
            $pagarmeSDK = new Client(config('services.pagarme.key'));

            return new Pagarme($pagarmeSDK);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();
    }
}
