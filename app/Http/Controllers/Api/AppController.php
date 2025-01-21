<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\ActivityLaunch;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\CompanyDocument;
use App\Models\User;
use App\Notifications\NewReceipt;
use App\Models\Document;

class AppController extends Controller
{

    use MediaUploadingTrait;

    public function admin(Request $request)
    {
        $driver = Driver::where('user_id', $request->user()->id)->first();

        $activityLaunches = ActivityLaunch::where([
            'driver_id' => $driver->id,
            'send' => 1
        ])
            ->with([
                'week',
                'activityPerOperators.tvde_operator',
            ])
            ->limit(10)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($activityLaunches as $activityLaunch) {
            $sub = [
                $activityLaunch->rent,
                $activityLaunch->management,
                $activityLaunch->insurance,
                $activityLaunch->fuel,
                $activityLaunch->tolls,
                $activityLaunch->others
            ];
            $sub = array_sum($sub);
            $sum = [];
            foreach ($activityLaunch->activityPerOperators as $activityPerOperator) {
                $sum[] = $activityPerOperator->net - $activityPerOperator->taxes;
            }
            $sum = array_sum($sum);
            $activityLaunch->total = $sum - $sub + $activityLaunch->refund;
            $activityLaunch->sum = $sum;
            $activityLaunch->sub = $sub;
        }

        // Impedir dois recibos no mesmo dia

        $last_receipt = Receipt::where([
            'driver_id' => $driver->id
        ])
            ->orderBy('id', 'desc')
            ->first();

        $can_create_receipt = true;
        if ($last_receipt && $last_receipt->created_at->gt(now()->subDays(1))) {
            $can_create_receipt = false;
        }

        return [
            'activityLaunches' => $activityLaunches,
            'last_receipt' => $last_receipt,
            'can_create_receipt' => $can_create_receipt
        ];
    }

    public function myReceipts(Request $request)
    {

        $driver = Driver::where('user_id', $request->user()->id)->first();

        $receipts = Receipt::where([
            'driver_id' => $driver->id
        ])
            ->limit(20)
            ->orderBy('created_at', 'desc')
            ->get();

        return $receipts;
    }

    public function reports(Request $request)
    {

        $driver = Driver::where('user_id', $request->user()->id)->first();

        $activityLaunches = ActivityLaunch::where([
            'driver_id' => $driver->id,
            'send' => 1
        ])
            ->with([
                'week.tvde_month.year',
                'activityPerOperators.tvde_operator',
            ])
            ->limit(20)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($activityLaunches as $activityLaunch) {
            $sub = [
                $activityLaunch->rent,
                $activityLaunch->management,
                $activityLaunch->insurance,
                $activityLaunch->fuel,
                $activityLaunch->tolls,
                $activityLaunch->others
            ];
            $sub = array_sum($sub);
            $sum = [];
            foreach ($activityLaunch->activityPerOperators as $activityPerOperator) {
                $sum[] = $activityPerOperator->net - $activityPerOperator->taxes;
            }
            $sum = array_sum($sum);
            $activityLaunch->total = $sum - $sub + $activityLaunch->refund;
            $activityLaunch->sum = $sum;
            $activityLaunch->sub = $sub;
        }

        return $activityLaunches;
    }

    public function pdf(Request $request)
    {

        $activityLaunch = ActivityLaunch::where([
            'id' => $request->activity_launch_id,
        ])
            ->with([
                'driver.operation',
                'week.tvde_month.year',
                'activityPerOperators.tvde_operator',
            ])
            ->first();

        $driver_id = $activityLaunch->driver_id;

        $sub = [
            $activityLaunch->rent,
            $activityLaunch->management,
            $activityLaunch->insurance,
            $activityLaunch->fuel,
            $activityLaunch->tolls,
            $activityLaunch->others
        ];
        $sub = array_sum($sub);
        $net = [];
        $taxes = [];
        $totals = [];
        foreach ($activityLaunch->activityPerOperators as $activityPerOperator) {
            $sum[] = $activityPerOperator->net - $activityPerOperator->taxes;
            $net[] = $activityPerOperator->net;
            $taxes[] = $activityPerOperator->taxes;
        }
        $sum = array_sum($sum);
        $net = array_sum($net);
        $taxes = array_sum($taxes);
        $activityLaunch->total = $sum - $sub + $activityLaunch->refund;
        $activityLaunch->sum = $sum;
        $activityLaunch->sub = $sub;
        $activityLaunch->net = $net;
        $activityLaunch->taxes = $taxes;

        //LAST 60 DAYS
        $activityLaunches60 = ActivityLaunch::where([
            'driver_id' => $driver_id,
            'send' => 1
        ])
            ->whereDate('created_at', '>=', now()->subDays(60)->format('Y-m-d H:i:s'))
            ->with([
                'week.tvde_month.year',
                'activityPerOperators.tvde_operator',
            ])
            ->orderBy('id', 'asc')
            ->get();

        foreach ($activityLaunches60 as $activityLaunch60) {
            $sub = [
                $activityLaunch60->rent,
                $activityLaunch60->management,
                $activityLaunch60->insurance,
                $activityLaunch60->fuel,
                $activityLaunch60->tolls,
                $activityLaunch60->others
            ];
            $sub = array_sum($sub);
            $sum = [];
            foreach ($activityLaunch60->activityPerOperators as $activityPerOperator) {
                $sum[] = $activityPerOperator->net - $activityPerOperator->taxes;
            }
            $sum = array_sum($sum);
            $activityLaunch60->total = $sum - $sub + $activityLaunch60->refund;
            $activityLaunch60->sum = $sum;
            $activityLaunch60->sub = $sub;
        }

        $pdf = Pdf::loadView('admin.financialStatements.pdf', [
            'activityLaunch' => $activityLaunch,
            'activityLaunches60' => $activityLaunches60,
        ])->setOption([
            'isRemoteEnabled' => true,
        ]);

        return $pdf->stream();
    }

    public function documents()
    {
        return CompanyDocument::with(['media'])->get();
    }

    public function sendReceipt(Request $request)
    {
        $request->validate([
            'value' => 'required',
            'file' => 'required|file',
        ]);

        // CREATE RECEIPT
        $driver = Driver::where('user_id', $request->user()->id)->first();

        $receipt = new Receipt;
        $receipt->driver_id = $driver->id;
        $receipt->value = $request->value;
        $receipt->save();

        // GRAVAR DOCUMENTO
        if ($request->hasFile('file')) {
            $receipt->addMedia($request->file('file'))->toMediaCollection('file');
        }

        // SEND EMAIL TO ADMIN
        User::find(2)->notify(new NewReceipt($driver));
        User::find($driver->user_id)->notify(new NewReceipt($driver));

        return response()->json([
            'message' => 'Receipt created successfully',
            'receipt' => $receipt
        ], 201);
    }

    public function myDocuments(Request $request)
    {
        $driver_id = Driver::where('user_id', $request->user()->id)->first()->id;
        $my_documents = Document::where('driver_id', $driver_id)->first();
        return $my_documents;
    }

    public function sendDocument(Request $request)
    {

        $driver_id = Driver::where('user_id', $request->user()->id)->first()->id;
        $document = Document::where('driver_id', $driver_id)->first();

        if ($request->collection_name) {
            $document->addMedia($request->file('file'))->toMediaCollection($request->collection_name);
        }
    }
}
