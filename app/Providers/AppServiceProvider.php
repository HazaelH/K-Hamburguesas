<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
<<<<<<< HEAD
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage; 
use Illuminate\Support\Facades\Lang;

class AppServiceProvider extends ServiceProvider
{
=======

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
>>>>>>> 433b97d2585cb68ef73e2952cfbbe7259e4eeed7
    public function register(): void
    {
        //
    }

<<<<<<< HEAD
    public function boot(): void
    {
        // 1. Personalización del Correo de Verificación de Cuenta Nueva
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject(Lang::get('auth/emails.verify_subject'))
                // Le pasamos el nombre dinámicamente a la traducción
                ->greeting(Lang::get('auth/emails.verify_greeting', ['name' => $notifiable->name]))
                ->line(Lang::get('auth/emails.verify_line1'))
                ->line(Lang::get('auth/emails.verify_line2'))
                ->action(Lang::get('auth/emails.verify_action'), $url)
                ->line(Lang::get('auth/emails.verify_line3'))
                ->salutation(Lang::get('auth/emails.salutation_custom'));
        });

        // 2. Personalización del Correo de Recuperación de Contraseña
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            
            // Reconstruimos la URL de reseteo
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            // Calculamos el tiempo de expiración desde la configuración de Laravel (usualmente 60 min)
            $expireTime = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

            return (new MailMessage)
                ->subject(Lang::get('auth/emails.reset_subject'))
                ->greeting(Lang::get('auth/emails.greeting'))
                ->line(Lang::get('auth/emails.reset_line1'))
                ->action(Lang::get('auth/emails.reset_action'), $url)
                // Le pasamos los minutos de expiración dinámicamente
                ->line(Lang::get('auth/emails.reset_line2', ['count' => $expireTime]))
                ->line(Lang::get('auth/emails.reset_line3'))
                ->salutation(Lang::get('auth/emails.salutation_custom'));
        });
    }
}
=======
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
>>>>>>> 433b97d2585cb68ef73e2952cfbbe7259e4eeed7
