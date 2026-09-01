<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmation extends Notification
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
        $mail = (new MailMessage)
            ->subject('Confirmation de votre commande #'.$this->order->id.' — LenuxWood')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Votre commande a bien été enregistrée.');

        foreach ($this->order->items as $item) {
            $mail->line('- '.$item->product->name.' × '.$item->quantity);
        }

        return $mail
            ->line('Montant total : '.number_format($this->order->amount, 0, ',', ' ').' FCFA')
            ->line('Mode de paiement : '.($this->order->payments->first()?->method ?? '—'))
            ->action('Suivre ma commande', config('app.frontend_url').'/compte')
            ->salutation("L'équipe LenuxWood");
    }
}