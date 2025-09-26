<?php 

namespace App\Jobs;

use App\Models\CrmEmailsQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendQueuedCrmEmail implements ShouldQueue
{
    public function __construct(public int $queueId) {}

    public function handle(): void
    {
        $q = CrmEmailsQueue::find($this->queueId);
        if (!$q || $q->status !== 'queued') return;

        Mail::html($q->body_html, function ($m) use ($q) {
            $m->to(array_map('trim', explode(',', $q->to ?? '')))
              ->subject($q->subject ?? '');
            if ($q->cc)  $m->cc(array_map('trim', explode(',', $q->cc)));
            if ($q->bcc) $m->bcc(array_map('trim', explode(',', $q->bcc)));
        });

        $q->update(['status' => 'sent', 'sent_at' => now()]);
    }
}