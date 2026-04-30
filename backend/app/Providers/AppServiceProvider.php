<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Domain\Poll\Contracts\PollRepositoryInterface::class,
            \App\Domain\Poll\Repositories\PollRepository::class
        );
        $this->app->bind(
            \App\Domain\Poll\Contracts\VoteRepositoryInterface::class,
            \App\Domain\Poll\Repositories\VoteRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
