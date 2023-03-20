<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyReceiptRequest;
use App\Http\Requests\StoreReceiptRequest;
use App\Http\Requests\UpdateReceiptRequest;
use App\Models\ActivityLaunch;
use App\Models\Driver;
use App\Models\Receipt;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ReceiptsController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('receipt_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Receipt::with(['activity_launch'])->select(sprintf('%s.*', (new Receipt)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'receipt_show';
                $editGate = 'receipt_edit';
                $deleteGate = 'receipt_delete';
                $crudRoutePart = 'receipts';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                )
                );
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('reference', function ($row) {
                return $row->reference ? $row->reference : '';
            });
            $table->addColumn('activity_launch_rent', function ($row) {
                return $row->activity_launch ? $row->activity_launch->rent : '';
            });

            $table->editColumn('receipt', function ($row) {
                if (!$row->receipt) {
                    return '';
                }
                $links = [];
                foreach ($row->receipt as $media) {
                    $links[] = '<a href="' . $media->getUrl() . '" target="_blank">' . trans('global.downloadFile') . '</a>';
                }

                return implode(', ', $links);
            });

            $table->rawColumns(['actions', 'placeholder', 'activity_launch', 'receipt']);

            return $table->make(true);
        }

        return view('admin.receipts.index');
    }

    public function create()
    {
        abort_if(Gate::denies('receipt_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activity_launches = ActivityLaunch::pluck('rent', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.receipts.create', compact('activity_launches'));
    }

    public function create2(Request $request)
    {
        abort_if(Gate::denies('receipt_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $driver = Driver::where('user_id', auth()->user()->id)->first();
        $activityLaunch = ActivityLaunch::find($request->activity_launch_id);

        abort_if($driver->id != $activityLaunch->driver_id, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activity_launch_id = $request->activity_launch_id;

        return view('admin.receipts.create2')->with([
            'activity_launch_id' => $activity_launch_id,
        ]);
    }

    public function store(StoreReceiptRequest $request)
    {
        $receipt = Receipt::create($request->all());

        foreach ($request->input('receipt', []) as $file) {
            $receipt->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('receipt');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $receipt->id]);
        }

        return redirect()->route('admin.receipts.index');
    }

    public function store2(StoreReceiptRequest $request)
    {

        $receipt = Receipt::create($request->all());

        foreach ($request->input('receipt', []) as $file) {
            $receipt->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('receipt');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $receipt->id]);
        }

        return redirect('/admin');
    }

    public function edit(Receipt $receipt)
    {
        abort_if(Gate::denies('receipt_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activity_launches = ActivityLaunch::pluck('rent', 'id')->prepend(trans('global.pleaseSelect'), '');

        $receipt->load('activity_launch');

        return view('admin.receipts.edit', compact('activity_launches', 'receipt'));
    }

    public function update(UpdateReceiptRequest $request, Receipt $receipt)
    {
        $receipt->update($request->all());

        if (count($receipt->receipt) > 0) {
            foreach ($receipt->receipt as $media) {
                if (!in_array($media->file_name, $request->input('receipt', []))) {
                    $media->delete();
                }
            }
        }
        $media = $receipt->receipt->pluck('file_name')->toArray();
        foreach ($request->input('receipt', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $receipt->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('receipt');
            }
        }

        return redirect()->route('admin.receipts.index');
    }

    public function show(Receipt $receipt)
    {
        abort_if(Gate::denies('receipt_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $receipt->load('activity_launch');

        return view('admin.receipts.show', compact('receipt'));
    }

    public function destroy(Receipt $receipt)
    {
        abort_if(Gate::denies('receipt_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $receipt->delete();

        return back();
    }

    public function massDestroy(MassDestroyReceiptRequest $request)
    {
        $receipts = Receipt::find(request('ids'));

        foreach ($receipts as $receipt) {
            $receipt->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('receipt_create') && Gate::denies('receipt_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new Receipt();
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}