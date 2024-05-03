<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Repositories\Eloquent\FilmRepository;
use App\Repositories\FilmRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\BaseRepositoryInterface;
use App\Repositories\Eloquent\ActorRepository;
use App\Repositories\ActorRepositoryInterface;
use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\AuthRepositoryInterface;
use App\Repositories\Eloquent\CriticRepository;
use App\Repositories\CriticRepositoryInterface;
use App\Repositories\Eloquent\LanguageRepository;
use App\Repositories\LanguageRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\UserRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(RepositoryInterface::class, BaseRepository::class);
        $this->app->bind(FilmRepositoryInterface::class, FilmRepository::class);
        $this->app->bind(ActorRepositoryInterface::class, ActorRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(CriticRepositoryInterface::class, CriticRepository::class);
        $this->app->bind(LanguageRepositoryInterface::class, LanguageRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
