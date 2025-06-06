<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\StandCar;

class standCarContact extends Notification
{
    use Queueable;

    private $TransferForm;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($TransferForm)
    {
        $this->TransferForm = $TransferForm;
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

        $stand_car = StandCar::find($this->TransferForm->car_id)->load('brand', 'car_model');

        return (new MailMessage)
            ->line('Existe um pedido em Stand.')
            ->action('Ir para pedido', url('http://mundotvde.pt/admin/stand-car-forms'))
            ->line('Nome: ' . $this->TransferForm->name)
            ->line('Email: ' . $this->TransferForm->email)
            ->line('Telefone: ' . $this->TransferForm->phone)
            ->line('Viatura: ' . $stand_car->brand->name . ' ' . $stand_car->car_model->name)
            ->line('Mensagem: ' . $this->TransferForm->message);
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
