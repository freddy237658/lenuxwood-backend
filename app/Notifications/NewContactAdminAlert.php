<?php

namespace App\Notifications;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactAdminAlert extends Notification
{
    use Queueable;

    public function __construct(public Contact $contact)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouveau message de contact — LenuxWood')
            ->greeting('Nouveau message reçu')
            ->line('De : '.$this->contact->name)
            ->line('Email : '.($this->contact->email ?? 'non renseigné'))
            ->line('Téléphone : '.($this->contact->phone ?? 'non renseigné'))
            ->line('Sujet : '.($this->contact->subject ?? 'Non précisé'))
            ->line('Message : '.$this->contact->message)
            ->action('Voir dans le panel admin', config('app.frontend_url').'/admin/messages-contact')
            ->salutation('LenuxWood — Notification automatique');
    }
}