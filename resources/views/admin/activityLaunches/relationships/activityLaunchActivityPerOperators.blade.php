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

                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-activityLaunchActivityPerOperators">
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
                            <tbody>
                                @foreach($activityPerOperators as $key => $activityPerOperator)
                                    <tr data-entry-id="{{ $activityPerOperator->id }}">
                                        <td>

                                        </td>
                                        <td>
                                            {{ $activityPerOperator->id ?? '' }}
                                        </td>
                                        <td>
                                            {{ $activityPerOperator->activity_launch->rent ?? '' }}
                                        </td>
                                        <td>
                                            {{ $activityPerOperator->activity_launch->management ?? '' }}
                                        </td>
                                        <td>
                                            {{ $activityPerOperator->activity_launch->insurance ?? '' }}
                                        </td>
                                        <td>
                                            {{ $activityPerOperator->activity_launch->fuel ?? '' }}
                                        </td>
                                        <td>
                                            {{ $activityPerOperator->activity_launch->tolls ?? '' }}
                                        </td>
                                        <td>
                                            {{ $activityPerOperator->net ?? '' }}
                                        </td>
                                        <td>
                                            {{ $activityPerOperator->taxes ?? '' }}
                                        </td>
                                        <td>
                                            {{ $activityPerOperator->tvde_operator->name ?? '' }}
                                        </td>
                                        <td>
                                            @can('activity_per_operator_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.activity-per-operators.show', $activityPerOperator->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                            @can('activity_per_operator_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('admin.activity-per-operators.edit', $activityPerOperator->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan

                                            @can('activity_per_operator_delete')
                                                <form action="{{ route('admin.activity-per-operators.destroy', $activityPerOperator->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                                </form>
                                            @endcan

                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('activity_per_operator_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.activity-per-operators.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
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

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-activityLaunchActivityPerOperators:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection