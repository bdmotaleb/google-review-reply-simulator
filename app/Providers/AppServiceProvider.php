<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * 
     * Future: Bind ReviewReplyServiceInterface to GoogleReviewApiService
     * when Google API integration is ready:
     * 
     * $this->app->bind(
     *     \App\Contracts\ReviewReplyServiceInterface::class,
     *     \App\Services\GoogleReviewApiService::class
     * );
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
        //
    }
}
