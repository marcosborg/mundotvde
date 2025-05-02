<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyWhatsappMessageRequest;
use App\Http\Requests\StoreWhatsappMessageRequest;
use App\Http\Requests\UpdateWhatsappMessageRequest;
use App\Models\WhatsappMessage;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class WhatsappMessagesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('whatsapp_message_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = WhatsappMessage::query()->select(sprintf('%s.*', (new WhatsappMessage)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'whatsapp_message_show';
                $editGate      = 'whatsapp_message_edit';
                $deleteGate    = 'whatsapp_message_delete';
                $crudRoutePart = 'whatsapp-messages';

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
            $table->editColumn('user', function ($row) {
                return $row->user ? $row->user : '';
            });
            $table->editColumn('messages', function ($row) {
                return $row->messages ? $row->messages : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.whatsappMessages.index');
    }

    public function create()
    {
        abort_if(Gate::denies('whatsapp_message_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.whatsappMessages.create');
    }

    public function store(StoreWhatsappMessageRequest $request)
    {
        $whatsappMessage = WhatsappMessage::create($request->all());

        return redirect()->route('admin.whatsapp-messages.index');
    }

    public function edit(WhatsappMessage $whatsappMessage)
    {
        abort_if(Gate::denies('whatsapp_message_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.whatsappMessages.edit', compact('whatsappMessage'));
    }

    public function update(UpdateWhatsappMessageRequest $request, WhatsappMessage $whatsappMessage)
    {
        $whatsappMessage->update($request->all());

        return redirect()->route('admin.whatsapp-messages.index');
    }

    public function show(WhatsappMessage $whatsappMessage)
    {
        abort_if(Gate::denies('whatsapp_message_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.whatsappMessages.show', compact('whatsappMessage'));
    }

    public function destroy(WhatsappMessage $whatsappMessage)
    {
        abort_if(Gate::denies('whatsapp_message_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $whatsappMessage->delete();

        return back();
    }

    public function massDestroy(MassDestroyWhatsappMessageRequest $request)
    {
        $whatsappMessages = WhatsappMessage::find(request('ids'));

        foreach ($whatsappMessages as $whatsappMessage) {
            $whatsappMessage->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
