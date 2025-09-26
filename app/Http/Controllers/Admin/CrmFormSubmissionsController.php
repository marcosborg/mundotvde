<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCrmFormSubmissionRequest;
use App\Http\Requests\StoreCrmFormSubmissionRequest;
use App\Http\Requests\UpdateCrmFormSubmissionRequest;
use App\Models\CrmCard;
use App\Models\CrmCategory;
use App\Models\CrmForm;
use App\Models\CrmFormSubmission;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CrmFormSubmissionsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('crm_form_submission_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = CrmFormSubmission::with(['form', 'category', 'created_card'])->select(sprintf('%s.*', (new CrmFormSubmission)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'crm_form_submission_show';
                $editGate      = 'crm_form_submission_edit';
                $deleteGate    = 'crm_form_submission_delete';
                $crudRoutePart = 'crm-form-submissions';

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
            $table->addColumn('form_name', function ($row) {
                return $row->form ? $row->form->name : '';
            });

            $table->addColumn('category_name', function ($row) {
                return $row->category ? $row->category->name : '';
            });

            $table->editColumn('user_agent', function ($row) {
                return $row->user_agent ? $row->user_agent : '';
            });
            $table->editColumn('referer', function ($row) {
                return $row->referer ? $row->referer : '';
            });
            $table->editColumn('utm_json', function ($row) {
                return $row->utm_json ? $row->utm_json : '';
            });
            $table->editColumn('data_json', function ($row) {
                return $row->data_json ? $row->data_json : '';
            });
            $table->addColumn('created_card_title', function ($row) {
                return $row->created_card ? $row->created_card->title : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'form', 'category', 'created_card']);

            return $table->make(true);
        }

        return view('admin.crmFormSubmissions.index');
    }

    public function create()
    {
        abort_if(Gate::denies('crm_form_submission_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $forms = CrmForm::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $categories = CrmCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_cards = CrmCard::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.crmFormSubmissions.create', compact('categories', 'created_cards', 'forms'));
    }

    public function store(StoreCrmFormSubmissionRequest $request)
    {
        $crmFormSubmission = CrmFormSubmission::create($request->all());

        return redirect()->route('admin.crm-form-submissions.index');
    }

    public function edit(CrmFormSubmission $crmFormSubmission)
    {
        abort_if(Gate::denies('crm_form_submission_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $forms = CrmForm::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $categories = CrmCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_cards = CrmCard::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $crmFormSubmission->load('form', 'category', 'created_card');

        return view('admin.crmFormSubmissions.edit', compact('categories', 'created_cards', 'crmFormSubmission', 'forms'));
    }

    public function update(UpdateCrmFormSubmissionRequest $request, CrmFormSubmission $crmFormSubmission)
    {
        $crmFormSubmission->update($request->all());

        return redirect()->route('admin.crm-form-submissions.index');
    }

    public function show(CrmFormSubmission $crmFormSubmission)
    {
        abort_if(Gate::denies('crm_form_submission_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmFormSubmission->load('form', 'category', 'created_card');

        return view('admin.crmFormSubmissions.show', compact('crmFormSubmission'));
    }

    public function destroy(CrmFormSubmission $crmFormSubmission)
    {
        abort_if(Gate::denies('crm_form_submission_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmFormSubmission->delete();

        return back();
    }

    public function massDestroy(MassDestroyCrmFormSubmissionRequest $request)
    {
        $crmFormSubmissions = CrmFormSubmission::find(request('ids'));

        foreach ($crmFormSubmissions as $crmFormSubmission) {
            $crmFormSubmission->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
