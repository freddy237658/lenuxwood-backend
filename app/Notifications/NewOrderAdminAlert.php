<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderAdminAlert extends Notification
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvelle commande #'.$this->order->id.' — LenuxWood')
            ->greeting('Nouvelle commande reçue')
            ->line('Client : '.$this->order->user->name)
            ->line('Montant : '.number_format($this->order->amount, 0, ',', ' ').' FCFA')
            ->line('Paiement : '.($this->order->payments->first()?->method ?? '—'))
            ->action('Voir dans le panel admin', config('app.frontend_url').'/admin/commandes')
            ->salutation('LenuxWood — Notification automatique');
    }
}