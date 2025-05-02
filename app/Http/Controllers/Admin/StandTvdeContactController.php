<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyStandTvdeContactRequest;
use App\Http\Requests\StoreStandTvdeContactRequest;
use App\Http\Requests\UpdateStandTvdeContactRequest;
use App\Models\StandTvdeContact;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StandTvdeContactController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('stand_tvde_contact_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $standTvdeContacts = StandTvdeContact::all();

        return view('admin.standTvdeContacts.index', compact('standTvdeContacts'));
    }

    public function create()
    {
        abort_if(Gate::denies('stand_tvde_contact_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.standTvdeContacts.create');
    }

    public function store(StoreStandTvdeContactRequest $request)
    {
        $standTvdeContact = StandTvdeContact::create($request->all());

        return redirect()->route('admin.stand-tvde-contacts.index');
    }

    public function edit(StandTvdeContact $standTvdeContact)
    {
        abort_if(Gate::denies('stand_tvde_contact_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.standTvdeContacts.edit', compact('standTvdeContact'));
    }

    public function update(UpdateStandTvdeContactRequest $request, StandTvdeContact $standTvdeContact)
    {
        $standTvdeContact->update($request->all());

        return redirect()->route('admin.stand-tvde-contacts.index');
    }

    public function show(StandTvdeContact $standTvdeContact)
    {
        abort_if(Gate::denies('stand_tvde_contact_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.standTvdeContacts.show', compact('standTvdeContact'));
    }

    public function destroy(StandTvdeContact $standTvdeContact)
    {
        abort_if(Gate::denies('stand_tvde_contact_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $standTvdeContact->delete();

        return back();
    }

    public function massDestroy(MassDestroyStandTvdeContactRequest $request)
    {
        $standTvdeContacts = StandTvdeContact::find(request('ids'));

        foreach ($standTvdeContacts as $standTvdeContact) {
            $standTvdeContact->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}