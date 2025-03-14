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
use App\Models\TimeLog;

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

        $total = [];

        foreach ($activityLaunches as $activityLaunch) {
            $sub = [
                $activityLaunch->rent,
                $activityLaunch->management,
                $activityLaunch->insurance,
                $activityLaunch->fuel,
                $activityLaunch->tolls,
                $activityLaunch->others,
            ];
            $taxes = [];
            $sub = array_sum($sub);
            $sum = [];
            $sum_net = [];
            $taxes = [];
            foreach ($activityLaunch->activityPerOperators as $activityPerOperator) {
                $sum[] = $activityPerOperator->net - $activityPerOperator->taxes;
                $sum_net[] = $activityPerOperator->net;
                $taxes[] = $activityPerOperator->taxes;
            }
            $sum = array_sum($sum);
            $activityLaunch->total = $sum - $sub + $activityLaunch->refund;
            $activityLaunch->sum = $sum;
            $activityLaunch->sub = $sub;
            $activityLaunch->sum_net = array_sum($sum_net);
            $activityLaunch->total_after_refund = $activityLaunch->sum_net + $activityLaunch->refund;
            $activityLaunch->taxes = array_sum($taxes);
            $activityLaunch->total_descount_after_taxes = $activityLaunch->sub + $activityLaunch->taxes;

            if ($activityLaunch->paid == 0) {
                $total[] = $activityLaunch->total;
            }
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

        $total = number_format(array_sum($total), 2);

        return [
            'activityLaunches' => $activityLaunches,
            'last_receipt' => $last_receipt,
            'can_create_receipt' => $can_create_receipt,
            'total' => $total
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

    public function lastTimeLog(Request $request)
    {
        $user = $request->user();
        $driver = Driver::where('user_id', $user->id)->first();
        $lastTimeLog = TimeLog::where('driver_id', $driver->id)->orderBy('id', 'desc')->first();
        return $lastTimeLog;
    }

    public function newTimeLog(Request $request)
    {
        $user = $request->user();
        $driver = Driver::where('user_id', $user->id)->first();
        $newTimeLog = new TimeLog;
        $newTimeLog->driver_id = $driver->id;
        $newTimeLog->status = $request->status;
        $newTimeLog->save();
        return $newTimeLog;
    }

    public function getTimeLogs(Request $request)
    {
        $user = $request->user();
        $driver = Driver::where('user_id', $user->id)->first();

        if (!$driver) {
            return response()->json(['error' => 'Driver not found'], 404);
        }

        // Filtra os logs dos últimos 60 dias
        $sixtyDaysAgo = now()->subDays(60)->format('Y-m-d H:i:s');
        $timeLogs = TimeLog::where('driver_id', $driver->id)
            ->where('created_at', '>=', $sixtyDaysAgo)
            ->orderBy('created_at', 'asc') // Ordena os logs por ordem cronológica
            ->get();

        $groupedLogs = [];
        $drivingTimePerDay = [];

        $openStart = null; // Armazena o início do período de condução
        $isPaused = false; // Indica se o motorista está em pausa

        foreach ($timeLogs as $log) {
            $date = (new \DateTime($log->created_at))->format('Y-m-d');

            if (!isset($groupedLogs[$date])) {
                $groupedLogs[$date] = ['time_periods' => []];
                $drivingTimePerDay[$date] = 0;
            }

            // Adiciona o log ao dia correspondente
            $groupedLogs[$date]['time_periods'][] = $log;

            // Lógica para calcular o tempo de condução
            if ($log->status === 'start') {
                $openStart = new \DateTime($log->created_at); // Inicia o período de condução
                $isPaused = false;
            } elseif ($log->status === 'pause') {
                if ($openStart && !$isPaused) {
                    $pauseTime = new \DateTime($log->created_at);
                    $drivingTimePerDay[$date] += $pauseTime->getTimestamp() - $openStart->getTimestamp();
                    $isPaused = true;
                }
            } elseif ($log->status === 'continue') {
                if ($isPaused) {
                    $openStart = new \DateTime($log->created_at); // Retoma o período de condução
                    $isPaused = false;
                }
            } elseif ($log->status === 'end') {
                if ($openStart && !$isPaused) {
                    $endTime = new \DateTime($log->created_at);
                    $drivingTimePerDay[$date] += $endTime->getTimestamp() - $openStart->getTimestamp();
                    $openStart = null; // Finaliza o período de condução
                }
            }
        }

        // Ordena os dias por data decrescente
        krsort($groupedLogs);

        // Formata o tempo de condução para HH:MM:SS
        foreach ($groupedLogs as $date => &$data) {
            $data['driving_time'] = gmdate("H:i:s", $drivingTimePerDay[$date]);
        }

        return response()->json($groupedLogs);
    }
}
