<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Single source of truth for "who can see /admin/*" — used by the
        // `can:access-admin` middleware in routes/web.php.
        Gate::define('access-admin', fn (User $user) => $user->isAdmin());
    }
}
