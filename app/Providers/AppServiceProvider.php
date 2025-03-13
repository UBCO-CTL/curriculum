<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

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
        //
        Schema::defaultStringLength(191);

        Validator::extend('allowed_domain', function ($attribute, $value, $parameters, $validator) {
            return in_array(explode('@', $value)[1], ['ubc.ca', 'mail.ubc.ca', 'alumni.ubc.ca', 'student.ubc.ca']);
        }, 'Please Use A Valid UBC Domain Address When Entering Your Email');

        $this->bootAuth();
        $this->bootRoute();
    }

    public function bootAuth(): void
    {
        //
        // Gate::define('manage-users', function($user){
        //     return $user->hasAnyRole(['administrator','user']);
        // });

        // Gate::define('edit-users', function($user){
        //     return $user->hasRole('administrator');
        // });

        // Gate::define('isAdmin', function($user){
        //     return $user->hasRole('administrator');
        // });

        Gate::define('admin-privilege', function ($user) {
            return $user->hasRole('administrator');
        });
    }

    public function bootRoute(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60);
        });

    }
}
