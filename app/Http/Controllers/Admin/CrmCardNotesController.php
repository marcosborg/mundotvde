<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCrmCardNoteRequest;
use App\Http\Requests\StoreCrmCardNoteRequest;
use App\Http\Requests\UpdateCrmCardNoteRequest;
use App\Models\CrmCard;
use App\Models\CrmCardNote;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CrmCardNotesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('crm_card_note_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = CrmCardNote::with(['card', 'user'])->select(sprintf('%s.*', (new CrmCardNote)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'crm_card_note_show';
                $editGate      = 'crm_card_note_edit';
                $deleteGate    = 'crm_card_note_delete';
                $crudRoutePart = 'crm-card-notes';

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
            $table->addColumn('card_title', function ($row) {
                return $row->card ? $row->card->title : '';
            });

            $table->addColumn('user_name', function ($row) {
                return $row->user ? $row->user->name : '';
            });

            $table->editColumn('content', function ($row) {
                return $row->content ? $row->content : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'card', 'user']);

            return $table->make(true);
        }

        return view('admin.crmCardNotes.index');
    }

    public function create()
    {
        abort_if(Gate::denies('crm_card_note_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cards = CrmCard::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.crmCardNotes.create', compact('cards', 'users'));
    }

    public function store(StoreCrmCardNoteRequest $request)
    {
        $crmCardNote = CrmCardNote::create($request->all());

        return redirect()->route('admin.crm-card-notes.index');
    }

    public function edit(CrmCardNote $crmCardNote)
    {
        abort_if(Gate::denies('crm_card_note_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cards = CrmCard::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $crmCardNote->load('card', 'user');

        return view('admin.crmCardNotes.edit', compact('cards', 'crmCardNote', 'users'));
    }

    public function update(UpdateCrmCardNoteRequest $request, CrmCardNote $crmCardNote)
    {
        $crmCardNote->update($request->all());

        return redirect()->route('admin.crm-card-notes.index');
    }

    public function show(CrmCardNote $crmCardNote)
    {
        abort_if(Gate::denies('crm_card_note_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmCardNote->load('card', 'user');

        return view('admin.crmCardNotes.show', compact('crmCardNote'));
    }

    public function destroy(CrmCardNote $crmCardNote)
    {
        abort_if(Gate::denies('crm_card_note_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmCardNote->delete();

        return back();
    }

    public function massDestroy(MassDestroyCrmCardNoteRequest $request)
    {
        $crmCardNotes = CrmCardNote::find(request('ids'));

        foreach ($crmCardNotes as $crmCardNote) {
            $crmCardNote->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
