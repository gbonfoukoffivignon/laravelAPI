<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewEventNotifications extends Notification
{
    use Queueable;

    public $message;
    public $action;

    /**
     * Create a new notification instance.
     */
   public function __construct($message, $action)
    {
        $this->message = $message;
        $this->action = $action;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        // Retirer 'database' si tu ne veux pas sauvegarder dans la base de données
        return ['mail', 'broadcast', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->subject("Notification d'événement")
        ->line("{$this->message}\" a été {$this->action}.")
        //->action('Voir l\'événement', url('/events/' . $this->event->id))
        ->line('Merci de rester à jour avec nos événements !');
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->message,
            'time' => now()->toDateTimeString(),
        ]);
    }
    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'time' => now()->toDateTimeString(),
        ];
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'message' => $this->message,
            'time' => now()->toDateTimeString(),
        ];
    }
}
