<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActivityLaunchesSend extends Notification
{
    use Queueable;

    private $activityLaunche;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($activityLaunche)
    {

        $this->activityLaunche = $activityLaunche;
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

        $html = "<table style='width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;'>";
        $html .= "<thead>";
        $html .= "<tr style='background-color: #f0f0f0;'>";
        $html .= "<th colspan='2' style='text-align: left; padding: 10px; font-size: 16px; border: 1px solid #ddd;'>Valor do saldo disponível</th>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td colspan='2' style='padding: 10px; border: 1px solid #ddd; font-size: 16px; background-color: #ffffff;'><strong>" . $this->activityLaunche->balance . " €</strong></td>";
        $html .= "</tr>";
        $html .= "<tr><td colspan='2' style='height: 10px;'></td></tr>"; // espaço
        $html .= "</thead>";
        $html .= "<tbody>";
        $html .= "<tr style='background-color: #f0f0f0;'>";
        $html .= "<th colspan='2' style='text-align: left; padding: 10px; font-size: 15px; border: 1px solid #ddd;'>Totais da semana</th>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td style='text-align: left; padding: 10px; border: 1px solid #ddd;'>Recebimentos</td>";
        $html .= "<td style='padding: 10px; border: 1px solid #ddd;'>" . $this->activityLaunche->total_after_refund . " €</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td style='text-align: left; padding: 10px; border: 1px solid #ddd;'>Descontos</td>";
        $html .= "<td style='padding: 10px; border: 1px solid #ddd;'>" . $this->activityLaunche->total_descount_after_taxes . " €</td>";
        $html .= "</tr>";
        $html .= "<tr style='background-color: #e8f4e5;'>";
        $html .= "<th style='text-align: left; padding: 10px; border: 1px solid #ddd;'>Total</th>";
        $html .= "<th style='padding: 10px; border: 1px solid #ddd;'>" . $this->activityLaunche->total . " €</th>";
        $html .= "</tr>";
        $html .= "</tbody>";
        $html .= "</table>";


        return (new MailMessage)
            ->subject('Extrato Mundo TVDE')
            ->greeting('Olá!')
            ->line('O Mundo TVDE lançou o extrato abaixo.')
            ->line($html)
            ->line('Visite a sua área pessoal para verificar os detalhes e fazer upload do recibo.')
            ->action('Área pessoal', url('https://mundotvde.pt/login'))
            ->line('Ou aceda diretamente pela app:')
            ->line('<a href="https://play.google.com/store/apps/details?id=pt.mundotvde.app&hl=pt" target="_blank">Abrir no Android</a>')
            ->line('<a href="https://apps.apple.com/pt/app/mundotvde/id6743633904" target="_blank">Abrir no iOS</a>')
            ->line('Obrigado pela preferência')
            ->salutation('Equipa Mundo TVDE');
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
