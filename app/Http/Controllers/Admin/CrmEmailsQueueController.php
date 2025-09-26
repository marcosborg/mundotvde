<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyCrmEmailsQueueRequest;
use App\Http\Requests\StoreCrmEmailsQueueRequest;
use App\Http\Requests\UpdateCrmEmailsQueueRequest;
use App\Models\CrmCard;
use App\Models\CrmEmailsQueue;
use App\Models\CrmStageEmail;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CrmEmailsQueueController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('crm_emails_queue_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = CrmEmailsQueue::with(['stage_email', 'card'])->select(sprintf('%s.*', (new CrmEmailsQueue)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'crm_emails_queue_show';
                $editGate      = 'crm_emails_queue_edit';
                $deleteGate    = 'crm_emails_queue_delete';
                $crudRoutePart = 'crm-emails-queues';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->addColumn('stage_email_to_emails', function ($row) {
                return $row->stage_email ? $row->stage_email->to_emails : '';
            });

            $table->editColumn('stage_email.subject', function ($row) {
                return $row->stage_email ? (is_string($row->stage_email) ? $row->stage_email : $row->stage_email->subject) : '';
            });
            $table->addColumn('card_title', function ($row) {
                return $row->card ? $row->card->title : '';
            });

            $table->editColumn('to', function ($row) {
                return $row->to ? $row->to : '';
            });
            $table->editColumn('cc', function ($row) {
                return $row->cc ? $row->cc : '';
            });
            $table->editColumn('subject', function ($row) {
                return $row->subject ? $row->subject : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? CrmEmailsQueue::STATUS_RADIO[$row->status] : '';
            });
            $table->editColumn('error', function ($row) {
                return $row->error ? $row->error : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'stage_email', 'card']);

            return $table->make(true);
        }

        return view('admin.crmEmailsQueues.index');
    }

    public function create()
    {
        abort_if(Gate::denies('crm_emails_queue_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stage_emails = CrmStageEmail::pluck('to_emails', 'id')->prepend(trans('global.pleaseSelect'), '');

        $cards = CrmCard::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.crmEmailsQueues.create', compact('cards', 'stage_emails'));
    }

    public function store(StoreCrmEmailsQueueRequest $request)
    {
        $crmEmailsQueue = CrmEmailsQueue::create($request->all());

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $crmEmailsQueue->id]);
        }

        return redirect()->route('admin.crm-emails-queues.index');
    }

    public function edit(CrmEmailsQueue $crmEmailsQueue)
    {
        abort_if(Gate::denies('crm_emails_queue_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stage_emails = CrmStageEmail::pluck('to_emails', 'id')->prepend(trans('global.pleaseSelect'), '');

        $cards = CrmCard::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $crmEmailsQueue->load('stage_email', 'card');

        return view('admin.crmEmailsQueues.edit', compact('cards', 'crmEmailsQueue', 'stage_emails'));
    }

    public function update(UpdateCrmEmailsQueueRequest $request, CrmEmailsQueue $crmEmailsQueue)
    {
        $crmEmailsQueue->update($request->all());

        return redirect()->route('admin.crm-emails-queues.index');
    }

    public function show(CrmEmailsQueue $crmEmailsQueue)
    {
        abort_if(Gate::denies('crm_emails_queue_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmEmailsQueue->load('stage_email', 'card');

        return view('admin.crmEmailsQueues.show', compact('crmEmailsQueue'));
    }

    public function destroy(CrmEmailsQueue $crmEmailsQueue)
    {
        abort_if(Gate::denies('crm_emails_queue_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmEmailsQueue->delete();

        return back();
    }

    public function massDestroy(MassDestroyCrmEmailsQueueRequest $request)
    {
        $crmEmailsQueues = CrmEmailsQueue::find(request('ids'));

        foreach ($crmEmailsQueues as $crmEmailsQueue) {
            $crmEmailsQueue->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('crm_emails_queue_create') && Gate::denies('crm_emails_queue_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new CrmEmailsQueue();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
