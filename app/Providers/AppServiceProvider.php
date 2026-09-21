<?php

namespace App\Providers;

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Route::aliasMiddleware('role', RoleMiddleware::class);

        Password::defaults(function () {
            return Password::min(8)
                            ->letters()
                            ->mixedCase()
                            ->numbers()
                            ->symbols();
        });

        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            return (new MailMessage)
                ->subject('Reset your NutriSight password')
                ->view('emails.password-reset', [
                    'url' => url(route('password.reset', [
                        'token' => $token,
                        'email' => $notifiable->getEmailForPasswordReset(),
                    ], false)),
                    'expireMinutes' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire'),
                ])
                ->withSymfonyMessage(function ($message) {
                    $message->addPart(
                        (new DataPart(new File(public_path('images/nutrisight-logo.png')), 'nutrisight-logo.png', 'image/png'))
                            ->asInline()
                            ->setContentId('nutrisight-logo@nutrisight')
                    );
                });
        });
    }
}
