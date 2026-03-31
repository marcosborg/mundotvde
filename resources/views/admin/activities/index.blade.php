@extends('layouts.admin')
@section('content')
<div class="content">
    @can('activity_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.activities.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.activity.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.activity.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <p class="text-muted">Arraste as linhas para alterar a ordem dos serviços no site.</p>
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-Activity">
                            <thead>
                                <tr>
                                    <th width="10">

                                    </th>
                                    <th width="40">

                                    </th>
                                    <th>
                                        {{ trans('cruds.activity.fields.title') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.activity.fields.description') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.activity.fields.button') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.activity.fields.link') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.activity.fields.icon') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activities as $key => $activity)
                                    <tr data-entry-id="{{ $activity->id }}">
                                        <td>

                                        </td>
                                        <td>
                                            <span class="activity-drag-handle" title="Arrastar para ordenar" style="cursor: move; font-size: 18px; line-height: 1;">&#9776;</span>
                                        </td>
                                        <td>
                                            {{ $activity->title ?? '' }}
                                        </td>
                                        <td>
                                            {{ $activity->description ?? '' }}
                                        </td>
                                        <td>
                                            {{ $activity->button ?? '' }}
                                        </td>
                                        <td>
                                            {{ $activity->link ?? '' }}
                                        </td>
                                        <td>
                                            {{ App\Models\Activity::ICON_SELECT[$activity->icon] ?? '' }}
                                        </td>
                                        <td>
                                            @can('activity_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.activities.show', $activity->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                            @can('activity_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('admin.activities.edit', $activity->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan

                                            @can('activity_delete')
                                                <form action="{{ route('admin.activities.destroy', $activity->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@endsection
@section('scripts')
@parent
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('activity_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.activities.massDestroy') }}",
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
    ordering: false,
    pageLength: 100,
  });
  let table = $('.datatable-Activity:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });

  const tbody = document.querySelector('.datatable-Activity tbody');
  if (tbody) {
    new Sortable(tbody, {
      animation: 150,
      handle: '.activity-drag-handle',
      onEnd: function () {
        const orderedIds = Array.from(tbody.querySelectorAll('tr[data-entry-id]'))
          .map((row) => parseInt(row.getAttribute('data-entry-id'), 10))
          .filter((value) => !Number.isNaN(value));

        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'PATCH',
          url: "{{ route('admin.activities.reorder') }}",
          data: { ordered_ids: orderedIds }
        });
      }
    });
  }
  
})

</script>
@endsection
