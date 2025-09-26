<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CrmEmailsQueue;
use App\Jobs\SendQueuedCrmEmail;

class ProcessCrmEmailQueue extends Command
{
    protected $signature = 'crm:process-email-queue {--limit=50}';
    protected $description = 'Dispatch jobs to send queued CRM emails (scheduled_at <= now).';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $ids = CrmEmailsQueue::query()
            ->where('status', 'queued')
            ->where(function ($q) {
                $q->whereNull('scheduled_at')
                  ->orWhere('scheduled_at', '<=', now());
            })
            ->orderBy('id')
            ->limit($limit)
            ->pluck('id');

        foreach ($ids as $id) {
            dispatch(new SendQueuedCrmEmail($id));
        }

        $this->info("Dispatched {$ids->count()} email(s).");

        return Command::SUCCESS;
    }
}
