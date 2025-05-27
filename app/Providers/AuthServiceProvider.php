<?php

namespace App\Providers;

use App\Models\AssignedEmail;
use App\Policies\EmailPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        AssignedEmail::class => EmailPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->is_admin) {
                return true;
            }
        });

        Gate::define('view-reports', function ($user) {
            return $user->is_admin || $user->is_supervisor;
        });

        Gate::define('manage-settings', function ($user) {
            return $user->is_admin;
        });
    }
}
