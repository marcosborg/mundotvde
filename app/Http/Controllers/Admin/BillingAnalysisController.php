<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\TvdeYear;
use App\Models\Receipt;
use App\Models\Driver;

class BillingAnalysisController extends Controller
{
    public function index($tvde_year_id = null)
    {
        abort_if(Gate::denies('billing_analysi_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tvde_years = TvdeYear::all();
        $tvde_year = null;
        $drivers = collect();

        if ($tvde_year_id) {
            $tvde_year = TvdeYear::findOrFail($tvde_year_id);
            $year = $tvde_year->name;

            $drivers = Driver::whereHas('receipts', function ($query) use ($year) {
                $query->whereYear('created_at', $year);
            })
                ->with(['receipts' => function ($query) use ($year) {
                    $query->whereYear('created_at', $year);
                }])
                ->orderBy('name')
                ->get();
        }

        return view('admin.billingAnalysis.index', compact([
            'tvde_years',
            'tvde_year_id',
            'tvde_year',
            'drivers',
        ]));
    }
}
