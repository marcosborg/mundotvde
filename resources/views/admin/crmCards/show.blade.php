@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.crmCard.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.crm-cards.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmCard.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $crmCard->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmCard.fields.category') }}
                                    </th>
                                    <td>
                                        {{ $crmCard->category->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmCard.fields.stage') }}
                                    </th>
                                    <td>
                                        {{ $crmCard->stage->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmCard.fields.title') }}
                                    </th>
                                    <td>
                                        {{ $crmCard->title }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmCard.fields.form') }}
                                    </th>
                                    <td>
                                        {{ $crmCard->form->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmCard.fields.source') }}
                                    </th>
                                    <td>
                                        {{ App\Models\CrmCard::SOURCE_RADIO[$crmCard->source] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmCard.fields.priority') }}
                                    </th>
                                    <td>
                                        {{ App\Models\CrmCard::PRIORITY_RADIO[$crmCard->priority] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmCard.fields.status') }}
                                    </th>
                                    <td>
                                        {{ App\Models\CrmCard::STATUS_RADIO[$crmCard->status] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmCard.fields.lost_reason') }}
                                    </th>
                                    <td>
                                        {{ $crmCard->lost_reason }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmCard.fields.won_at') }}
                                    </th>
                                    <td>
                                        {{ $crmCard->won_at }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmCard.fields.closed_at') }}
                                    </th>
                                    <td>
                                        {{ $crmCard->closed_at }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmCard.fields.due_at') }}
                                    </th>
                                    <td>
                                        {{ $crmCard->due_at }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmCard.fields.assigned_to') }}
                                    </th>
                                    <td>
                                        {{ $crmCard->assigned_to->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmCard.fields.created_by') }}
                                    </th>
                                    <td>
                                        {{ $crmCard->created_by->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmCard.fields.position') }}
                                    </th>
                                    <td>
                                        {{ $crmCard->position }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmCard.fields.fields_snapshot_json') }}
                                    </th>
                                    <td>
                                        {{ $crmCard->fields_snapshot_json }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmCard.fields.crm_card_attachments') }}
                                    </th>
                                    <td>
                                        @foreach($crmCard->crm_card_attachments as $key => $media)
                                            <a href="{{ $media->getUrl() }}" target="_blank">
                                                {{ trans('global.view_file') }}
                                            </a>
                                        @endforeach
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.crm-cards.index') }}">
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