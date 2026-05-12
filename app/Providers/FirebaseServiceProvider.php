<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;


class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->singleton('firebase', function () {
            $factory = (new Factory)
                ->withServiceAccount(config('services.firebase.credentials'))
                ->withDatabaseUri(config('services.firebase.database_url'));

            return $factory;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
