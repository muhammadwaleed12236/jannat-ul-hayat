<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Implicitly grant "Super Admin" role all permissions
        // Handles both 'Super Admin' (space) and 'superAdmin' (camelCase) role names
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user->hasRole('Super Admin') || $user->hasRole('superAdmin') || $user->hasRole('admin')) {
                return true;
            }
            return null;
        });
    }
}
