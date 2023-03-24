@extends('layouts.admin')
@section('content')
<div class="content">
    @can('activity_per_operator_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.activity-per-operators.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.activityPerOperator.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.activityPerOperator.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ActivityPerOperator">
                        <thead>
                            <tr>
                                <th width="10">

                                </th>
                                <th>
                                    {{ trans('cruds.activityPerOperator.fields.id') }}
                                </th>
                                <th>
                                    {{ trans('cruds.activityPerOperator.fields.activity_launch') }}
                                </th>
                                <th>
                                    {{ trans('cruds.activityLaunch.fields.management') }}
                                </th>
                                <th>
                                    {{ trans('cruds.activityLaunch.fields.insurance') }}
                                </th>
                                <th>
                                    {{ trans('cruds.activityLaunch.fields.fuel') }}
                                </th>
                                <th>
                                    {{ trans('cruds.activityLaunch.fields.tolls') }}
                                </th>
                                <th>
                                    {{ trans('cruds.activityPerOperator.fields.gross') }}
                                </th>
                                <th>
                                    {{ trans('cruds.activityPerOperator.fields.net') }}
                                </th>
                                <th>
                                    {{ trans('cruds.activityPerOperator.fields.taxes') }}
                                </th>
                                <th>
                                    {{ trans('cruds.activityPerOperator.fields.tvde_operator') }}
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
@can('activity_per_operator_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.activity-per-operators.massDestroy') }}",
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
    ajax: "{{ route('admin.activity-per-operators.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'activity_launch_rent', name: 'activity_launch.rent' },
{ data: 'activity_launch.management', name: 'activity_launch.management' },
{ data: 'activity_launch.insurance', name: 'activity_launch.insurance' },
{ data: 'activity_launch.fuel', name: 'activity_launch.fuel' },
{ data: 'activity_launch.tolls', name: 'activity_launch.tolls' },
{ data: 'gross', name: 'gross' },
{ data: 'net', name: 'net' },
{ data: 'taxes', name: 'taxes' },
{ data: 'tvde_operator_name', name: 'tvde_operator.name' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-ActivityPerOperator').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection