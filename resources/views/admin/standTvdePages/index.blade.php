@extends('layouts.admin')
@section('content')
<div class="content">
    @can('stand_tvde_page_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.stand-tvde-pages.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.standTvdePage.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.standTvdePage.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-StandTvdePage">
                            <thead>
                                <tr>
                                    <th width="10">

                                    </th>
                                    <th>
                                        {{ trans('cruds.standTvdePage.fields.id') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.standTvdePage.fields.title') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.standTvdePage.fields.image') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($standTvdePages as $key => $standTvdePage)
                                    <tr data-entry-id="{{ $standTvdePage->id }}">
                                        <td>

                                        </td>
                                        <td>
                                            {{ $standTvdePage->id ?? '' }}
                                        </td>
                                        <td>
                                            {{ $standTvdePage->title ?? '' }}
                                        </td>
                                        <td>
                                            @if($standTvdePage->image)
                                                <a href="{{ $standTvdePage->image->getUrl() }}" target="_blank" style="display: inline-block">
                                                    <img src="{{ $standTvdePage->image->getUrl('thumb') }}">
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            @can('stand_tvde_page_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.stand-tvde-pages.show', $standTvdePage->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                            @can('stand_tvde_page_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('admin.stand-tvde-pages.edit', $standTvdePage->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan

                                            @can('stand_tvde_page_delete')
                                                <form action="{{ route('admin.stand-tvde-pages.destroy', $standTvdePage->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('stand_tvde_page_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.stand-tvde-pages.massDestroy') }}",
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
  let table = $('.datatable-StandTvdePage:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection