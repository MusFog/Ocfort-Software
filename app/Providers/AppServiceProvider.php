<?php

namespace App\Providers;

use App\Factories\NotificationFactory;
use App\Factories\UserFactory;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\CategoryServiceInterface;
use App\Interfaces\NotificationFactoryInterface;
use App\Interfaces\UserFactoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserServiceInterface;
use App\Interfaces\NotificationsServiceInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\UserRepository;
use App\Services\CategoryService;
use App\Services\NotificationsService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(NotificationsServiceInterface::class, NotificationsService::class);
        $this->app->bind(UserFactoryInterface::class, UserFactory::class);
        $this->app->bind(NotificationFactoryInterface::class, NotificationFactory::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
