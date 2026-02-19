<?php

namespace App\Jobs;

use App\Models\InspectionAssignment;
use App\Models\InspectionSetting;
use App\Models\User;
use App\Notifications\InspectionAssignmentNotification;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendInspectionReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $assignmentId, public string $reason = 'due')
    {
    }

    public function handle(PushNotificationService $push): void
    {
        $assignment = InspectionAssignment::with(['assignedUser', 'vehicle'])->find($this->assignmentId);
        if (!$assignment || !$assignment->assignedUser) {
            return;
        }

        $settings = InspectionSetting::current();
        $isPushEnabled = (bool) $settings->push_enabled;

        $title = 'Inspeção de viatura';
        $body = match ($this->reason) {
            'created' => 'Nova inspeção atribuída. Verifique e submeta até ao prazo.',
            'overdue' => 'Inspeção em atraso. É necessário submeter com urgência.',
            'rejected_resubmit' => 'A inspeção foi rejeitada. Submeta novamente após correção.',
            default => 'Tem uma inspeção de viatura por concluir.',
        };

        $assignment->assignedUser->notify(new InspectionAssignmentNotification(
            $assignment,
            'inspection_' . $this->reason,
            $title,
            $body
        ));

        if ($isPushEnabled) {
            $push->sendToUser((int) $assignment->assigned_user_id, $title, $body, [
                'type' => 'inspection_due',
                'assignment_id' => $assignment->id,
                'vehicle_id' => $assignment->vehicle_id,
            ]);
        }

        if ($this->reason === 'overdue') {
            $admins = User::whereHas('roles', fn($q) => $q->where('id', 1))->get();
            foreach ($admins as $admin) {
                $admin->notify(new InspectionAssignmentNotification(
                    $assignment,
                    'inspection_overdue_admin',
                    'Inspeção em atraso',
                    'Existe uma inspeção em atraso para acompanhamento da equipa.'
                ));
            }
        }
    }
}

