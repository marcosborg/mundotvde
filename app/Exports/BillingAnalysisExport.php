<?php

namespace App\Exports;

use App\Models\Driver;
use App\Models\TvdeYear;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BillingAnalysisExport implements FromCollection, WithHeadings
{
    protected $year;

    public function __construct($tvde_year_id)
    {
        $this->year = TvdeYear::findOrFail($tvde_year_id)->name;
    }

    public function collection()
    {
        return Driver::whereHas('receipts', function ($query) {
            $query->whereYear('created_at', $this->year)
                  ->whereIn('company', ['TGA', 'OC']);
        })
        ->with(['receipts' => function ($query) {
            $query->whereYear('created_at', $this->year)
                  ->whereIn('company', ['TGA', 'OC']);
        }])
        ->orderBy('name')
        ->get()
        ->map(function ($driver) {
            $totalTGA = $driver->receipts->where('company', 'TGA')->sum('value');
            $totalOC = $driver->receipts->where('company', 'OC')->sum('value');
            $total = $totalTGA + $totalOC;

            $tgaPercent = $total > 0 ? round(($totalTGA / $total) * 100, 2) : 0;
            $ocPercent = $total > 0 ? round(($totalOC / $total) * 100, 2) : 0;

            return [
                'Motorista' => $driver->name,
                'Total TGA (€)' => $totalTGA,
                'Total OC (€)' => $totalOC,
                '% TGA' => $tgaPercent . '%',
                '% OC' => $ocPercent . '%',
            ];
        });
    }

    public function headings(): array
    {
        return ['Motorista', 'Total TGA (€)', 'Total OC (€)', '% TGA', '% OC'];
    }
}
