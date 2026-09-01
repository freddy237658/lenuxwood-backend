<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bienvenue chez LenuxWood !')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Votre compte LenuxWood a bien été créé.')
            ->line('Vous pouvez dès maintenant parcourir notre catalogue, demander un devis et suivre vos commandes depuis votre espace client.')
            ->action('Découvrir le catalogue', config('app.frontend_url').'/catalogue')
            ->salutation("L'équipe LenuxWood");
    }
}