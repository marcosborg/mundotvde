<?php

namespace App\Listeners;

use App\Events\CardStageChanged;
use App\Models\{CrmCard, CrmStageEmail, CrmEmailsQueue};
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Carbon;

class QueueStageEmails
{
    public function handle(CardStageChanged $e): void
    {
        $emails = CrmStageEmail::where('stage_id', $e->toStageId)
            ->where('is_active', true)
            ->where('send_on_enter', true)
            ->get();

        if ($emails->isEmpty()) return;

        $card = CrmCard::with(['stage','category'])->find($e->cardId);
        $ctx = [
            'card'     => $card,
            'stage'    => $card->stage,
            'category' => $card->category,
            'field'    => $card->fields_snapshot_json ?? [],
        ];

        foreach ($emails as $se) {
            $body = Blade::render($se->body_template ?? '', $ctx);
            CrmEmailsQueue::create([
                'stage_email_id' => $se->id,
                'card_id'        => $card->id,
                'to'             => $se->to_emails,
                'cc'             => $se->cc_emails,
                'bcc'            => $se->bcc_emails,
                'subject'        => $se->subject ?? ('Card #' . $card->id),
                'body_html'      => $body,
                'status'         => 'queued',
                'scheduled_at'   => $se->delay_minutes ? Carbon::now()->addMinutes($se->delay_minutes) : now(),
            ]);
        }
    }
}