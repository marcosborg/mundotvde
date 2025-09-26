@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.crmForm.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.crm-forms.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmForm.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $crmForm->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmForm.fields.category') }}
                                    </th>
                                    <td>
                                        {{ $crmForm->category->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmForm.fields.name') }}
                                    </th>
                                    <td>
                                        {{ $crmForm->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmForm.fields.slug') }}
                                    </th>
                                    <td>
                                        {{ $crmForm->slug }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmForm.fields.status') }}
                                    </th>
                                    <td>
                                        {{ App\Models\CrmForm::STATUS_RADIO[$crmForm->status] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmForm.fields.confirmation_message') }}
                                    </th>
                                    <td>
                                        {{ $crmForm->confirmation_message }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmForm.fields.redirect_url') }}
                                    </th>
                                    <td>
                                        {{ $crmForm->redirect_url }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmForm.fields.notify_emails') }}
                                    </th>
                                    <td>
                                        {{ $crmForm->notify_emails }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmForm.fields.create_card_on_submit') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $crmForm->create_card_on_submit ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.crm-forms.index') }}">
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