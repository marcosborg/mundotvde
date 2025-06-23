<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyAppMessageRequest;
use App\Http\Requests\StoreAppMessageRequest;
use App\Http\Requests\UpdateAppMessageRequest;
use App\Models\AppMessage;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class AppMessagesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('app_message_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = AppMessage::with(['user'])->select(sprintf('%s.*', (new AppMessage)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'app_message_show';
                $editGate      = 'app_message_edit';
                $deleteGate    = 'app_message_delete';
                $crudRoutePart = 'app-messages';

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
            $table->addColumn('user_name', function ($row) {
                return $row->user ? $row->user->name : '';
            });

            $table->editColumn('user.email', function ($row) {
                return $row->user ? (is_string($row->user) ? $row->user : $row->user->email) : '';
            });
            $table->editColumn('messages', function ($row) {
                return $row->messages ? $row->messages : '';
            });

            $table->editColumn('updated_at', function ($row) {
                return $row->updated_at ? $row->updated_at : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'user']);

            return $table->make(true);
        }

        return view('admin.appMessages.index');
    }

    public function create()
    {
        abort_if(Gate::denies('app_message_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.appMessages.create', compact('users'));
    }

    public function store(StoreAppMessageRequest $request)
    {
        $appMessage = AppMessage::create($request->all());

        return redirect()->route('admin.app-messages.index');
    }

    public function edit(AppMessage $appMessage)
    {
        abort_if(Gate::denies('app_message_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $appMessage->load('user');

        return view('admin.appMessages.edit', compact('appMessage', 'users'));
    }

    public function update(UpdateAppMessageRequest $request, AppMessage $appMessage)
    {
        $appMessage->update($request->all());

        return redirect()->route('admin.app-messages.index');
    }

    public function show(AppMessage $appMessage)
    {
        abort_if(Gate::denies('app_message_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $appMessage->load('user');

        return view('admin.appMessages.show', compact('appMessage'));
    }

    public function destroy(AppMessage $appMessage)
    {
        abort_if(Gate::denies('app_message_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $appMessage->delete();

        return back();
    }

    public function massDestroy(MassDestroyAppMessageRequest $request)
    {
        $appMessages = AppMessage::find(request('ids'));

        foreach ($appMessages as $appMessage) {
            $appMessage->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
