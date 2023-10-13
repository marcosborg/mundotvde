@extends('layouts.admin')
@section('content')
<div class="content">
    @can('stand_tvde_pub_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.stand-tvde-pubs.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.standTvdePub.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.standTvdePub.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-StandTvdePub">
                            <thead>
                                <tr>
                                    <th width="10">

                                    </th>
                                    <th>
                                        {{ trans('cruds.standTvdePub.fields.id') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.standTvdePub.fields.title') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.standTvdePub.fields.image') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($standTvdePubs as $key => $standTvdePub)
                                    <tr data-entry-id="{{ $standTvdePub->id }}">
                                        <td>

                                        </td>
                                        <td>
                                            {{ $standTvdePub->id ?? '' }}
                                        </td>
                                        <td>
                                            {{ $standTvdePub->title ?? '' }}
                                        </td>
                                        <td>
                                            @if($standTvdePub->image)
                                                <a href="{{ $standTvdePub->image->getUrl() }}" target="_blank" style="display: inline-block">
                                                    <img src="{{ $standTvdePub->image->getUrl('thumb') }}">
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            @can('stand_tvde_pub_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.stand-tvde-pubs.show', $standTvdePub->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                            @can('stand_tvde_pub_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('admin.stand-tvde-pubs.edit', $standTvdePub->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan

                                            @can('stand_tvde_pub_delete')
                                                <form action="{{ route('admin.stand-tvde-pubs.destroy', $standTvdePub->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('stand_tvde_pub_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.stand-tvde-pubs.massDestroy') }}",
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
  let table = $('.datatable-StandTvdePub:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection