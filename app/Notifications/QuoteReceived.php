<?php

namespace App\Notifications;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteReceived extends Notification
{
    use Queueable;

    public function __construct(public Quote $quote)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre demande de devis a bien été reçue — LenuxWood')
            ->greeting('Bonjour '.$this->quote->name.',')
            ->line('Nous avons bien reçu votre demande de devis pour : '.$this->quote->category->name_fr.'.')
            ->line('Description : '.$this->quote->description)
            ->line('Notre équipe vous recontactera sous 48h avec une proposition détaillée.')
            ->salutation("L'équipe LenuxWood");
    }
}