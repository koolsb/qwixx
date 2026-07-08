<?php

namespace App\Providers;

use App\Services\LayoutLibrary;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LayoutLibrary::class, fn (): LayoutLibrary => new LayoutLibrary(
            config: config('qwixx', []),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
