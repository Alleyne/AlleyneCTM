<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class emailNuevaOcobro extends Notification
{
    use Queueable;
    public $pdo, $propietario;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($pdo, $propietario)
    {
       $this->pdo= $pdo;
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
                    ->subject('Estado de cuenta de '. $this->pdo)
                    ->greeting('Buen dia señor(a) '.$this->propietario)
                    ->line('Le notificamos que se ha emitido la orden de cobro de la cuota de mantenimiento correspondiente al mes de '. $this->pdo. '. Para una información detallada haga click en el boton azul.')
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
