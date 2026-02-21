<?php

namespace App\Providers;

use App\Models\PassportClient;
use Carbon\CarbonInterval;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

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
        Passport::useClientModel(PassportClient::class);

        Passport::tokensExpireIn(CarbonInterval::minutes(30));
        Passport::refreshTokensExpireIn(CarbonInterval::days(7));

        Passport::tokensCan([
            'access-client-app' => 'Access client applications via SSO server',
        ]);

        Passport::authorizationView('auth.oauth.authorize');
    }
}
