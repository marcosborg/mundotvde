<?php

namespace App\Console\Commands;

use App\Services\Inspections\InspectionRoutineSchedulerService;
use Illuminate\Console\Command;

class GenerateRoutineInspections extends Command
{
    protected $signature = 'inspections:generate-routines {--dry-run : Simular sem gravar}';

    protected $description = 'Gera inspeções de rotina a partir dos agendamentos ativos';

    public function handle(InspectionRoutineSchedulerService $service): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $result = $service->run($dryRun);

        foreach ($result['messages'] as $line) {
            $this->line($line);
        }

        $this->info('created=' . $result['created'] . ' skipped=' . $result['skipped'] . ($dryRun ? ' [dry-run]' : ''));

        return self::SUCCESS;
    }
}
