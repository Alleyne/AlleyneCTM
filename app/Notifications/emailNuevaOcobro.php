<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class emailNuevaOcobro extends Notification
{
    use Queueable;
    public $pdo;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($pdo)
    {
       $this->pdo= $pdo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('Estimado propietario, para notificarle que se ha emitido la orden de cobro del mes de '. $this->pdo)
                    ->action('Notification Action', 'https://ctmaster.net')
                    ->line('Gracias por su tiempo!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
