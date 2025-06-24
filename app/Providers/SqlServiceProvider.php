<?php

namespace App\Providers;

use App\Interfaces\QueryBuilderInterface;
use App\Managers\QueryManager;
use Illuminate\Support\ServiceProvider;

class SqlServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(QueryManager::class, function ($app) {
            return new QueryManager($app);
        });

        $this->app->alias(QueryManager::class, 'query');

        $this->app->bind(QueryBuilderInterface::class, function ($app) {
            return $app->make(QueryManager::class)->driver();
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
