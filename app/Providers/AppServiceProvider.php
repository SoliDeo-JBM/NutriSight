<?php

namespace App\Providers;

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Route::aliasMiddleware('role', RoleMiddleware::class);

        Password::defaults(function () {
            return Password::min(8)
                            ->letters()
                            ->mixedCase()
                            ->numbers()
                            ->symbols();
        });
    }
}
