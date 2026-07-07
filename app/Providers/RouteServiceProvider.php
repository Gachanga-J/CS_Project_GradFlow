<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
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
        //
    }
    public static function homeForRole(string $role): string
    {
        return match($role) {
            'admin'      => '/dashboard/admin',
            'supervisor' => '/dashboard/supervisor',
            default      => '/dashboard/student',
        };
    }
}
