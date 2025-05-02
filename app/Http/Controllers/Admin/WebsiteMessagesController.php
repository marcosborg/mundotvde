<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyWebsiteMessageRequest;
use App\Http\Requests\StoreWebsiteMessageRequest;
use App\Http\Requests\UpdateWebsiteMessageRequest;
use App\Models\WebsiteMessage;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class WebsiteMessagesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('website_message_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = WebsiteMessage::query()->select(sprintf('%s.*', (new WebsiteMessage)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'website_message_show';
                $editGate      = 'website_message_edit';
                $deleteGate    = 'website_message_delete';
                $crudRoutePart = 'website-messages';

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
            $table->editColumn('email', function ($row) {
                return $row->email ? $row->email : '';
            });
            $table->editColumn('messages', function ($row) {
                return $row->messages ? $row->messages : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.websiteMessages.index');
    }

    public function create()
    {
        abort_if(Gate::denies('website_message_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.websiteMessages.create');
    }

    public function store(StoreWebsiteMessageRequest $request)
    {
        $websiteMessage = WebsiteMessage::create($request->all());

        return redirect()->route('admin.website-messages.index');
    }

    public function edit(WebsiteMessage $websiteMessage)
    {
        abort_if(Gate::denies('website_message_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.websiteMessages.edit', compact('websiteMessage'));
    }

    public function update(UpdateWebsiteMessageRequest $request, WebsiteMessage $websiteMessage)
    {
        $websiteMessage->update($request->all());

        return redirect()->route('admin.website-messages.index');
    }

    public function show(WebsiteMessage $websiteMessage)
    {
        abort_if(Gate::denies('website_message_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.websiteMessages.show', compact('websiteMessage'));
    }

    public function destroy(WebsiteMessage $websiteMessage)
    {
        abort_if(Gate::denies('website_message_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $websiteMessage->delete();

        return back();
    }

    public function massDestroy(MassDestroyWebsiteMessageRequest $request)
    {
        $websiteMessages = WebsiteMessage::find(request('ids'));

        foreach ($websiteMessages as $websiteMessage) {
            $websiteMessage->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
