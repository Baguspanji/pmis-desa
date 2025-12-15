<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('admin', fn ($user) => $user->role == 'admin');
        Gate::define('operator', fn ($user) => $user->role == 'operator');
        Gate::define('kepala_desa', fn ($user) => $user->role == 'kepala_desa');
        Gate::define('staff', fn ($user) => $user->role == 'staff');
        Gate::define('kasun', fn ($user) => $user->role == 'kasun');
    }
}
