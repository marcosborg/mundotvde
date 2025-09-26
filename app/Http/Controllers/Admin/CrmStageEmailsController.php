<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyCrmStageEmailRequest;
use App\Http\Requests\StoreCrmStageEmailRequest;
use App\Http\Requests\UpdateCrmStageEmailRequest;
use App\Models\CrmStage;
use App\Models\CrmStageEmail;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CrmStageEmailsController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('crm_stage_email_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = CrmStageEmail::with(['stage'])->select(sprintf('%s.*', (new CrmStageEmail)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'crm_stage_email_show';
                $editGate      = 'crm_stage_email_edit';
                $deleteGate    = 'crm_stage_email_delete';
                $crudRoutePart = 'crm-stage-emails';

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
            $table->addColumn('stage_name', function ($row) {
                return $row->stage ? $row->stage->name : '';
            });

            $table->editColumn('to_emails', function ($row) {
                return $row->to_emails ? $row->to_emails : '';
            });
            $table->editColumn('bcc_emails', function ($row) {
                return $row->bcc_emails ? $row->bcc_emails : '';
            });
            $table->editColumn('subject', function ($row) {
                return $row->subject ? $row->subject : '';
            });
            $table->editColumn('send_on_enter', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->send_on_enter ? 'checked' : null) . '>';
            });
            $table->editColumn('send_on_exit', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->send_on_exit ? 'checked' : null) . '>';
            });
            $table->editColumn('delay_minutes', function ($row) {
                return $row->delay_minutes ? $row->delay_minutes : '';
            });
            $table->editColumn('is_active', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->is_active ? 'checked' : null) . '>';
            });

            $table->rawColumns(['actions', 'placeholder', 'stage', 'send_on_enter', 'send_on_exit', 'is_active']);

            return $table->make(true);
        }

        return view('admin.crmStageEmails.index');
    }

    public function create()
    {
        abort_if(Gate::denies('crm_stage_email_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stages = CrmStage::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.crmStageEmails.create', compact('stages'));
    }

    public function store(StoreCrmStageEmailRequest $request)
    {
        $crmStageEmail = CrmStageEmail::create($request->all());

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $crmStageEmail->id]);
        }

        return redirect()->route('admin.crm-stage-emails.index');
    }

    public function edit(CrmStageEmail $crmStageEmail)
    {
        abort_if(Gate::denies('crm_stage_email_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stages = CrmStage::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $crmStageEmail->load('stage');

        return view('admin.crmStageEmails.edit', compact('crmStageEmail', 'stages'));
    }

    public function update(UpdateCrmStageEmailRequest $request, CrmStageEmail $crmStageEmail)
    {
        $crmStageEmail->update($request->all());

        return redirect()->route('admin.crm-stage-emails.index');
    }

    public function show(CrmStageEmail $crmStageEmail)
    {
        abort_if(Gate::denies('crm_stage_email_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmStageEmail->load('stage');

        return view('admin.crmStageEmails.show', compact('crmStageEmail'));
    }

    public function destroy(CrmStageEmail $crmStageEmail)
    {
        abort_if(Gate::denies('crm_stage_email_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmStageEmail->delete();

        return back();
    }

    public function massDestroy(MassDestroyCrmStageEmailRequest $request)
    {
        $crmStageEmails = CrmStageEmail::find(request('ids'));

        foreach ($crmStageEmails as $crmStageEmail) {
            $crmStageEmail->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('crm_stage_email_create') && Gate::denies('crm_stage_email_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new CrmStageEmail();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
