<?php

namespace App\Services\Inspections;

use App\Models\Inspection;
use App\Models\InspectionReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InspectionReportService
{
    private InspectionCompletenessValidator $completenessValidator;

    public function __construct(InspectionCompletenessValidator $completenessValidator)
    {
        $this->completenessValidator = $completenessValidator;
    }

    public function generateAndLock(Inspection $inspection): InspectionReport
    {
        $inspection->load(['vehicle.vehicle_brand', 'vehicle.vehicle_model', 'driver', 'photos', 'damages.photos', 'signatures', 'createdBy', 'responsibleUser']);
        $missingItems = $this->completenessValidator->getMissingItems($inspection);

        $pdf = Pdf::loadView('admin.inspections.pdf', [
            'inspection' => $inspection,
            'missingItems' => $missingItems,
        ]);

        $fileName = sprintf('inspections/reports/inspection_%d_%s.pdf', $inspection->id, now()->format('Ymd_His'));
        $content = $pdf->output();
        Storage::disk('public')->put($fileName, $content);

        $report = InspectionReport::updateOrCreate(
            ['inspection_id' => $inspection->id],
            [
                'pdf_path' => $fileName,
                'pdf_hash' => hash('sha256', $content),
                'snapshot_json' => $inspection->toArray(),
                'generated_at' => now(),
                'immutable' => true,
            ]
        );

        $inspection->update([
            'status' => 'closed',
            'completed_at' => now(),
            'locked_at' => now(),
            'current_step' => 10,
        ]);

        return $report;
    }
}
