@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.websiteMessage.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.website-messages.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.websiteMessage.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $websiteMessage->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.websiteMessage.fields.email') }}
                                    </th>
                                    <td>
                                        {{ $websiteMessage->email }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.whatsappMessage.fields.messages') }}
                                    </th>
                                    <td>
                                        @php
                                        $messages = json_decode($websiteMessage->messages, true);
                                        @endphp

                                        @if(is_array($messages))
                                        <div class="chat-full">
                                            @foreach($messages as $message)
                                            @php
                                            $role = $message['role'] ?? 'user';
                                            $content = $message['content'] ?? '';
                                            $class = $role === 'assistant' ? 'assistant-bubble' : 'user-bubble';
                                            @endphp
                                            <div class="{{ $class }}">{{ $content }}</div>
                                            @endforeach
                                        </div>
                                        @else
                                        <em>Sem mensagens válidas</em>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.website-messages.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
</div>
@endsection
@section('styles')
<style>
    .chat-full {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 600px;
    }

    .user-bubble {
        align-self: flex-end;
        background-color: #d1e7dd;
        color: #0f5132;
        padding: 10px 15px;
        border-radius: 15px 15px 0 15px;
        font-size: 14px;
        max-width: 80%;
        word-wrap: break-word;
    }

    .assistant-bubble {
        align-self: flex-start;
        background-color: #f8d7da;
        color: #842029;
        padding: 10px 15px;
        border-radius: 15px 15px 15px 0;
        font-size: 14px;
        max-width: 80%;
        word-wrap: break-word;
    }

</style>

@endsection