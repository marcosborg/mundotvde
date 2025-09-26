<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyCrmCardRequest;
use App\Http\Requests\StoreCrmCardRequest;
use App\Http\Requests\UpdateCrmCardRequest;
use App\Models\CrmCard;
use App\Models\CrmCategory;
use App\Models\CrmForm;
use App\Models\CrmStage;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CrmCardsController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('crm_card_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = CrmCard::with(['category', 'stage', 'form', 'assigned_to', 'created_by'])->select(sprintf('%s.*', (new CrmCard)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'crm_card_show';
                $editGate      = 'crm_card_edit';
                $deleteGate    = 'crm_card_delete';
                $crudRoutePart = 'crm-cards';

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
            $table->addColumn('category_name', function ($row) {
                return $row->category ? $row->category->name : '';
            });

            $table->addColumn('stage_name', function ($row) {
                return $row->stage ? $row->stage->name : '';
            });

            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->addColumn('form_name', function ($row) {
                return $row->form ? $row->form->name : '';
            });

            $table->editColumn('source', function ($row) {
                return $row->source ? CrmCard::SOURCE_RADIO[$row->source] : '';
            });
            $table->editColumn('priority', function ($row) {
                return $row->priority ? CrmCard::PRIORITY_RADIO[$row->priority] : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? CrmCard::STATUS_RADIO[$row->status] : '';
            });
            $table->editColumn('lost_reason', function ($row) {
                return $row->lost_reason ? $row->lost_reason : '';
            });

            $table->addColumn('assigned_to_name', function ($row) {
                return $row->assigned_to ? $row->assigned_to->name : '';
            });

            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->editColumn('position', function ($row) {
                return $row->position ? $row->position : '';
            });
            $table->editColumn('fields_snapshot_json', function ($row) {
                return $row->fields_snapshot_json ? $row->fields_snapshot_json : '';
            });
            $table->editColumn('crm_card_attachments', function ($row) {
                if (! $row->crm_card_attachments) {
                    return '';
                }
                $links = [];
                foreach ($row->crm_card_attachments as $media) {
                    $links[] = '<a href="' . $media->getUrl() . '" target="_blank">' . trans('global.downloadFile') . '</a>';
                }

                return implode(', ', $links);
            });

            $table->rawColumns(['actions', 'placeholder', 'category', 'stage', 'form', 'assigned_to', 'created_by', 'crm_card_attachments']);

            return $table->make(true);
        }

        return view('admin.crmCards.index');
    }

    public function create()
    {
        abort_if(Gate::denies('crm_card_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = CrmCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $stages = CrmStage::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $forms = CrmForm::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $assigned_tos = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.crmCards.create', compact('assigned_tos', 'categories', 'created_bies', 'forms', 'stages'));
    }

    public function store(StoreCrmCardRequest $request)
    {
        $crmCard = CrmCard::create($request->all());

        foreach ($request->input('crm_card_attachments', []) as $file) {
            $crmCard->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('crm_card_attachments');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $crmCard->id]);
        }

        return redirect()->route('admin.crm-cards.index');
    }

    public function edit(CrmCard $crmCard)
    {
        abort_if(Gate::denies('crm_card_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = CrmCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $stages = CrmStage::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $forms = CrmForm::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $assigned_tos = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $crmCard->load('category', 'stage', 'form', 'assigned_to', 'created_by');

        return view('admin.crmCards.edit', compact('assigned_tos', 'categories', 'created_bies', 'crmCard', 'forms', 'stages'));
    }

    public function update(UpdateCrmCardRequest $request, CrmCard $crmCard)
    {
        $crmCard->update($request->all());

        if (count($crmCard->crm_card_attachments) > 0) {
            foreach ($crmCard->crm_card_attachments as $media) {
                if (! in_array($media->file_name, $request->input('crm_card_attachments', []))) {
                    $media->delete();
                }
            }
        }
        $media = $crmCard->crm_card_attachments->pluck('file_name')->toArray();
        foreach ($request->input('crm_card_attachments', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $crmCard->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('crm_card_attachments');
            }
        }

        return redirect()->route('admin.crm-cards.index');
    }

    public function show(CrmCard $crmCard)
    {
        abort_if(Gate::denies('crm_card_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmCard->load('category', 'stage', 'form', 'assigned_to', 'created_by');

        return view('admin.crmCards.show', compact('crmCard'));
    }

    public function destroy(CrmCard $crmCard)
    {
        abort_if(Gate::denies('crm_card_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmCard->delete();

        return back();
    }

    public function massDestroy(MassDestroyCrmCardRequest $request)
    {
        $crmCards = CrmCard::find(request('ids'));

        foreach ($crmCards as $crmCard) {
            $crmCard->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('crm_card_create') && Gate::denies('crm_card_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new CrmCard();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
