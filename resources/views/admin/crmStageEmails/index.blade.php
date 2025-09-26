@extends('layouts.admin')
@section('content')
<div class="content">
    @can('crm_stage_email_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.crm-stage-emails.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.crmStageEmail.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.crmStageEmail.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-CrmStageEmail">
                        <thead>
                            <tr>
                                <th width="10">

                                </th>
                                <th>
                                    {{ trans('cruds.crmStageEmail.fields.id') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmStageEmail.fields.stage') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmStageEmail.fields.to_emails') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmStageEmail.fields.bcc_emails') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmStageEmail.fields.subject') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmStageEmail.fields.send_on_enter') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmStageEmail.fields.send_on_exit') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmStageEmail.fields.delay_minutes') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmStageEmail.fields.is_active') }}
                                </th>
                                <th>
                                    &nbsp;
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>



        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('crm_stage_email_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.crm-stage-emails.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
          return entry.id
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: "{{ route('admin.crm-stage-emails.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'stage_name', name: 'stage.name' },
{ data: 'to_emails', name: 'to_emails' },
{ data: 'bcc_emails', name: 'bcc_emails' },
{ data: 'subject', name: 'subject' },
{ data: 'send_on_enter', name: 'send_on_enter' },
{ data: 'send_on_exit', name: 'send_on_exit' },
{ data: 'delay_minutes', name: 'delay_minutes' },
{ data: 'is_active', name: 'is_active' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-CrmStageEmail').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection