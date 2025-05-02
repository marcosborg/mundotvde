<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDocumentSubmited extends Notification
{
    use Queueable;
    private $driver_name;
    private $document_name;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($driver_name, $document_name)
    {
        $this->driver_name = $driver_name;
        $this->document_name = $document_name;
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
                    ->line('O condutor ' . $this->driver_name . ' submeteu o documento ' . $this->document_name . '.')
                    ->line('Pode consultar na sua área de administração!');
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
