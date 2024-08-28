<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Arr;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('no_spaces', function ($attribute, $value, $parameters, $validator) {
            return strpos($value, ' ') === false;
        });

        if (str_ends_with(Arr::get($_SERVER,'HTTP_HOST'), 'pipernigrum.my.id')) {
            URL::forceScheme('https');
        }

        Gate::define('admin', function (User $user) {
            return (bool)$user->admin;
        });
    }
}
