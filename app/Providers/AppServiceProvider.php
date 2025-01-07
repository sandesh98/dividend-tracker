<?php

namespace App\Providers;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ResultDecoder;
use Scheb\YahooFinanceApi\ValueMapper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(ApiClient::class, function (Application $app) {
            return new ApiClient(new Client(),  new ResultDecoder(new ValueMapper()));
        });
    }
}
