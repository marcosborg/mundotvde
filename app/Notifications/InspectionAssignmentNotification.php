<?php

namespace App\Notifications;

use App\Models\InspectionAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InspectionAssignmentNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected InspectionAssignment $assignment,
        protected string $type,
        protected string $title,
        protected string $message,
        protected array $extra = []
    ) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'assignment_id' => $this->assignment->id,
            'vehicle_id' => $this->assignment->vehicle_id,
            ...$this->extra,
        ];
    }
}

