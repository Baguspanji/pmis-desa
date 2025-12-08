<?php

namespace App\Providers;

use App\Models\Resident;
use App\Observers\ResidentObserver;
use Illuminate\Support\ServiceProvider;

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
        Resident::observe(ResidentObserver::class);
    }
}
