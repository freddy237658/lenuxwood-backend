<?php

namespace App\Notifications;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewQuoteAdminAlert extends Notification
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
            ->subject('Nouvelle demande de devis — LenuxWood')
            ->greeting('Nouvelle demande de devis reçue')
            ->line('Client : '.$this->quote->name.' ('.$this->quote->phone.')')
            ->line('Module : '.$this->quote->category->name_fr)
            ->line('Description : '.$this->quote->description)
            ->line('Ville : '.($this->quote->city ?? 'Non renseignée'))
            ->action('Voir dans le panel admin', config('app.frontend_url').'/admin/devis')
            ->salutation('LenuxWood — Notification automatique');
    }
}