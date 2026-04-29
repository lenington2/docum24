<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
        \Illuminate\Auth\Notifications\VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Verifica la tua email — Docum24')
                ->greeting('Ciao ' . $notifiable->name . '!')
                ->line('Grazie per esserti registrato su Docum24.')
                ->line('Clicca il pulsante per verificare il tuo indirizzo email.')
                ->action('Verifica Email', $url)
                ->line('Il link scadrà tra 60 minuti.')
                ->line('Se non hai creato un account, ignora questa email.');
        });
    }
}
