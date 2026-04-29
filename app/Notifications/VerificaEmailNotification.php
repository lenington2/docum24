<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerificaEmailNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );

        return (new MailMessage)
            ->subject('Verifica la tua email — Docum24')
            ->greeting('Ciao ' . $notifiable->name . '!')
            ->line('Grazie per esserti registrato su Docum24.')
            ->line('Clicca il pulsante qui sotto per verificare il tuo indirizzo email.')
            ->action('Verifica Email', $verificationUrl)
            ->line('Il link scadrà tra 60 minuti.')
            ->line('Se non hai creato un account, ignora questa email.')
            ->salutation('Il team di Docum24');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
