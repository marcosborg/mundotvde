@extends('layouts.admin')
@section('content')
<div class="content">
    @can('document_management_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.document-managements.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.documentManagement.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.documentManagement.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-DocumentManagement">
                            <thead>
                                <tr>
                                    <th width="10">

                                    </th>
                                    <th>
                                        {{ trans('cruds.documentManagement.fields.id') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.documentManagement.fields.title') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.documentManagement.fields.doc_company') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.documentManagement.fields.signature') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documentManagements as $key => $documentManagement)
                                    <tr data-entry-id="{{ $documentManagement->id }}">
                                        <td>

                                        </td>
                                        <td>
                                            {{ $documentManagement->id ?? '' }}
                                        </td>
                                        <td>
                                            {{ $documentManagement->title ?? '' }}
                                        </td>
                                        <td>
                                            {{ $documentManagement->doc_company->name ?? '' }}
                                        </td>
                                        <td>
                                            @foreach($documentManagement->signatures as $key => $item)
                                                <span class="label label-info label-many">{{ $item->title }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @can('document_management_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.document-managements.show', $documentManagement->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                            @can('document_management_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('admin.document-managements.edit', $documentManagement->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan

                                            @can('document_management_delete')
                                                <form action="{{ route('admin.document-managements.destroy', $documentManagement->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('document_management_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.document-managements.massDestroy') }}",
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
  let table = $('.datatable-DocumentManagement:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection