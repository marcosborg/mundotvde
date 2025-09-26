@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.crmEmailsQueue.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.crm-emails-queues.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmEmailsQueue.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $crmEmailsQueue->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmEmailsQueue.fields.stage_email') }}
                                    </th>
                                    <td>
                                        {{ $crmEmailsQueue->stage_email->to_emails ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmEmailsQueue.fields.card') }}
                                    </th>
                                    <td>
                                        {{ $crmEmailsQueue->card->title ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmEmailsQueue.fields.to') }}
                                    </th>
                                    <td>
                                        {{ $crmEmailsQueue->to }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmEmailsQueue.fields.cc') }}
                                    </th>
                                    <td>
                                        {{ $crmEmailsQueue->cc }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmEmailsQueue.fields.subject') }}
                                    </th>
                                    <td>
                                        {{ $crmEmailsQueue->subject }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmEmailsQueue.fields.body_html') }}
                                    </th>
                                    <td>
                                        {!! $crmEmailsQueue->body_html !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmEmailsQueue.fields.status') }}
                                    </th>
                                    <td>
                                        {{ App\Models\CrmEmailsQueue::STATUS_RADIO[$crmEmailsQueue->status] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmEmailsQueue.fields.error') }}
                                    </th>
                                    <td>
                                        {{ $crmEmailsQueue->error }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmEmailsQueue.fields.scheduled_at') }}
                                    </th>
                                    <td>
                                        {{ $crmEmailsQueue->scheduled_at }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmEmailsQueue.fields.sent_at') }}
                                    </th>
                                    <td>
                                        {{ $crmEmailsQueue->sent_at }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.crm-emails-queues.index') }}">
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