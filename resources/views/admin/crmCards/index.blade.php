@extends('layouts.admin')
@section('content')
<div class="content">
    @can('crm_card_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.crm-cards.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.crmCard.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.crmCard.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-CrmCard">
                        <thead>
                            <tr>
                                <th width="10">

                                </th>
                                <th>
                                    {{ trans('cruds.crmCard.fields.id') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmCard.fields.category') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmCard.fields.stage') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmCard.fields.title') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmCard.fields.form') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmCard.fields.source') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmCard.fields.priority') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmCard.fields.status') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmCard.fields.lost_reason') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmCard.fields.won_at') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmCard.fields.closed_at') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmCard.fields.due_at') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmCard.fields.assigned_to') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmCard.fields.created_by') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmCard.fields.position') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmCard.fields.fields_snapshot_json') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmCard.fields.crm_card_attachments') }}
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
@can('crm_card_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.crm-cards.massDestroy') }}",
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
    ajax: "{{ route('admin.crm-cards.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'category_name', name: 'category.name' },
{ data: 'stage_name', name: 'stage.name' },
{ data: 'title', name: 'title' },
{ data: 'form_name', name: 'form.name' },
{ data: 'source', name: 'source' },
{ data: 'priority', name: 'priority' },
{ data: 'status', name: 'status' },
{ data: 'lost_reason', name: 'lost_reason' },
{ data: 'won_at', name: 'won_at' },
{ data: 'closed_at', name: 'closed_at' },
{ data: 'due_at', name: 'due_at' },
{ data: 'assigned_to_name', name: 'assigned_to.name' },
{ data: 'created_by_name', name: 'created_by.name' },
{ data: 'position', name: 'position' },
{ data: 'fields_snapshot_json', name: 'fields_snapshot_json' },
{ data: 'crm_card_attachments', name: 'crm_card_attachments', sortable: false, searchable: false },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-CrmCard').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection