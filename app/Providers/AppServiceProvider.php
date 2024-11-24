<?php

namespace App\Providers;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Repository\IUserRepository;
use App\Repository\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->when(UserController::class)
            ->needs(IUserRepository::class)
            ->give(UserRepository::class);
        $this->app->when(AuthController::class)
            ->needs(IUserRepository::class)
            ->give(UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
