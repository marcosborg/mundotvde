<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendRecomendation extends Notification
{
    use Queueable;

    private $recommendation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($recommendation)
    {
        $this->recommendation = $recommendation;

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
            ->line('O motorista ' . $this->recommendation->driver->name . ' ' . 'recomendou ' . $this->recommendation->name)
            ->line('<strong>Nome: </strong>' . $this->recommendation->name)
            ->line('<strong>Email: </strong>' . $this->recommendation->email)
            ->line('<strong>Telefone: </strong>' . $this->recommendation->phone)
            ->line('<strong>Cidade: </strong>' . $this->recommendation->city)
            ->line('<strong>Observações: </strong>' . $this->recommendation->comments)
            ->line('<strong>Estado: </strong>' . $this->recommendation->recommendation_status->name);
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
