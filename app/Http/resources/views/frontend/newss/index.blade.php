@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @can('news_create')
                <div style="margin-bottom: 10px;" class="row">
                    <div class="col-lg-12">
                        <a class="btn btn-success" href="{{ route('frontend.newss.create') }}">
                            {{ trans('global.add') }} {{ trans('cruds.news.title_singular') }}
                        </a>
                    </div>
                </div>
            @endcan
            <div class="card">
                <div class="card-header">
                    {{ trans('cruds.news.title_singular') }} {{ trans('global.list') }}
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-News">
                            <thead>
                                <tr>
                                    <th>
                                        {{ trans('cruds.news.fields.id') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.news.fields.title') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.news.fields.resume') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.news.fields.photo') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.news.fields.active') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($newss as $key => $news)
                                    <tr data-entry-id="{{ $news->id }}">
                                        <td>
                                            {{ $news->id ?? '' }}
                                        </td>
                                        <td>
                                            {{ $news->title ?? '' }}
                                        </td>
                                        <td>
                                            {{ $news->resume ?? '' }}
                                        </td>
                                        <td>
                                            @if($news->photo)
                                                <a href="{{ $news->photo->getUrl() }}" target="_blank" style="display: inline-block">
                                                    <img src="{{ $news->photo->getUrl('thumb') }}">
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            <span style="display:none">{{ $news->active ?? '' }}</span>
                                            <input type="checkbox" disabled="disabled" {{ $news->active ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            @can('news_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('frontend.newss.show', $news->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                            @can('news_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('frontend.newss.edit', $news->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan

                                            @can('news_delete')
                                                <form action="{{ route('frontend.newss.destroy', $news->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('news_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('frontend.newss.massDestroy') }}",
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
  let table = $('.datatable-News:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection