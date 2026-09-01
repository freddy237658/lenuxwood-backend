<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
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
        $labels = [
            'quote_validated' => 'devis validé',
            'in_production' => 'en fabrication',
            'delivered' => 'livrée',
        ];

        return (new MailMessage)
            ->subject('Mise à jour de votre commande #'.$this->order->id.' — LenuxWood')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Votre commande est maintenant : '.($labels[$this->order->status] ?? $this->order->status).'.')
            ->action('Suivre ma commande', config('app.frontend_url').'/compte')
            ->salutation("L'équipe LenuxWood");
    }
}