@extends('layouts.admin')
@section('content')
<div class="content">
    @can('crm_emails_queue_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.crm-emails-queues.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.crmEmailsQueue.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.crmEmailsQueue.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-CrmEmailsQueue">
                        <thead>
                            <tr>
                                <th width="10">

                                </th>
                                <th>
                                    {{ trans('cruds.crmEmailsQueue.fields.id') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmEmailsQueue.fields.stage_email') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmStageEmail.fields.subject') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmEmailsQueue.fields.card') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmEmailsQueue.fields.to') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmEmailsQueue.fields.cc') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmEmailsQueue.fields.subject') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmEmailsQueue.fields.status') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmEmailsQueue.fields.error') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmEmailsQueue.fields.scheduled_at') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmEmailsQueue.fields.sent_at') }}
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
@can('crm_emails_queue_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.crm-emails-queues.massDestroy') }}",
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
    ajax: "{{ route('admin.crm-emails-queues.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'stage_email_to_emails', name: 'stage_email.to_emails' },
{ data: 'stage_email.subject', name: 'stage_email.subject' },
{ data: 'card_title', name: 'card.title' },
{ data: 'to', name: 'to' },
{ data: 'cc', name: 'cc' },
{ data: 'subject', name: 'subject' },
{ data: 'status', name: 'status' },
{ data: 'error', name: 'error' },
{ data: 'scheduled_at', name: 'scheduled_at' },
{ data: 'sent_at', name: 'sent_at' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-CrmEmailsQueue').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection