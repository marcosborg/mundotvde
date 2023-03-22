@extends('layouts.admin')
@section('content')
<div class="content">
    @can('activity_launch_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.activity-launches.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.activityLaunch.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.activityLaunch.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ActivityLaunch">
                        <thead>
                            <tr>
                                <th width="10">

                                </th>
                                <th>
                                    {{ trans('cruds.activityLaunch.fields.id') }}
                                </th>
                                <th>
                                    {{ trans('cruds.activityLaunch.fields.driver') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.name') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.email') }}
                                </th>
                                <th>
                                    {{ trans('cruds.activityLaunch.fields.week') }}
                                </th>
                                <th>
                                    {{ trans('cruds.tvdeWeek.fields.end_date') }}
                                </th>
                                <th>
                                    {{ trans('cruds.activityLaunch.fields.rent') }}
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
                                    {{ trans('cruds.activityLaunch.fields.others') }}
                                </th>
                                <th>
                                    {{ trans('cruds.activityLaunch.fields.refund') }}
                                </th>
                                <th>
                                    {{ trans('cruds.activityLaunch.fields.initial_kilometers') }}
                                </th>
                                <th>
                                    {{ trans('cruds.activityLaunch.fields.final_kilometers') }}
                                </th>
                                <th>
                                    {{ trans('cruds.activityLaunch.fields.send') }}
                                </th>
                                <th>
                                    {{ trans('cruds.activityLaunch.fields.paid') }}
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
@can('activity_launch_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.activity-launches.massDestroy') }}",
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
    ajax: "{{ route('admin.activity-launches.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'driver_code', name: 'driver.code' },
{ data: 'driver.name', name: 'driver.name' },
{ data: 'driver.email', name: 'driver.email' },
{ data: 'week_start_date', name: 'week.start_date' },
{ data: 'week.end_date', name: 'week.end_date' },
{ data: 'rent', name: 'rent' },
{ data: 'management', name: 'management' },
{ data: 'insurance', name: 'insurance' },
{ data: 'fuel', name: 'fuel' },
{ data: 'tolls', name: 'tolls' },
{ data: 'others', name: 'others' },
{ data: 'refund', name: 'refund' },
{ data: 'initial_kilometers', name: 'initial_kilometers' },
{ data: 'final_kilometers', name: 'final_kilometers' },
{ data: 'send', name: 'send' },
{ data: 'paid', name: 'paid' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-ActivityLaunch').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection