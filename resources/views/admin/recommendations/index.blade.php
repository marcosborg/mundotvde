@extends('layouts.admin')
@section('content')
<div class="content">
    @can('recommendation_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.recommendations.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.recommendation.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.recommendation.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-Recommendation">
                            <thead>
                                <tr>
                                    <th width="10">

                                    </th>
                                    <th>
                                        {{ trans('cruds.recommendation.fields.id') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.recommendation.fields.driver') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.recommendation.fields.recommendation_status') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.recommendation.fields.name') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.recommendation.fields.email') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.recommendation.fields.phone') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.recommendation.fields.city') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recommendations as $key => $recommendation)
                                    <tr data-entry-id="{{ $recommendation->id }}">
                                        <td>

                                        </td>
                                        <td>
                                            {{ $recommendation->id ?? '' }}
                                        </td>
                                        <td>
                                            {{ $recommendation->driver->name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $recommendation->recommendation_status->name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $recommendation->name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $recommendation->email ?? '' }}
                                        </td>
                                        <td>
                                            {{ $recommendation->phone ?? '' }}
                                        </td>
                                        <td>
                                            {{ $recommendation->city ?? '' }}
                                        </td>
                                        <td>
                                            @can('recommendation_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.recommendations.show', $recommendation->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                            @can('recommendation_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('admin.recommendations.edit', $recommendation->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan

                                            @can('recommendation_delete')
                                                <form action="{{ route('admin.recommendations.destroy', $recommendation->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('recommendation_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.recommendations.massDestroy') }}",
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
  let table = $('.datatable-Recommendation:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection