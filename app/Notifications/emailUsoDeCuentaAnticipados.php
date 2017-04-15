<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class emailUsoDeCuentaAnticipados extends Notification
{
    use Queueable;
    public $nota, $propietario;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($nota, $propietario)
    {
       $this->nota= $nota;
       $this->propietario= $propietario;
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
                    ->subject('Se utilizó su cuenta de pagos por anticipados')
                    ->greeting('Buen dia señor(a) '.$this->propietario)
                    ->line($this->nota.' Para una información detallada haga click en el boton azul.')
                    ->action('Ver estado de cuenta', 'http://ctmaster.net')
                    ->line('Gracias por la atención prestada.');
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
