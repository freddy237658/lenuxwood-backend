<?php

namespace App\Notifications;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteStatusUpdated extends Notification
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
        $labels = [
            'pending' => 'en attente',
            'processed' => 'traité',
            'refused' => 'refusé',
        ];

        return (new MailMessage)
            ->subject('Mise à jour de votre devis — LenuxWood')
            ->greeting('Bonjour '.$this->quote->name.',')
            ->line('Le statut de votre demande de devis a été mis à jour : '.($labels[$this->quote->status] ?? $this->quote->status).'.')
            ->action('Voir mes devis', config('app.frontend_url').'/compte/devis')
            ->salutation("L'équipe LenuxWood");
    }
}