@extends('layouts.admin')
@section('content')
<div class="content">
    @can('doc_company_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.doc-companies.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.docCompany.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.docCompany.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-DocCompany">
                            <thead>
                                <tr>
                                    <th width="10">

                                    </th>
                                    <th>
                                        {{ trans('cruds.docCompany.fields.id') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.docCompany.fields.name') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.docCompany.fields.nipc') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.docCompany.fields.license_number') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.docCompany.fields.address') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.docCompany.fields.location') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.docCompany.fields.zip') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.docCompany.fields.country') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($docCompanies as $key => $docCompany)
                                    <tr data-entry-id="{{ $docCompany->id }}">
                                        <td>

                                        </td>
                                        <td>
                                            {{ $docCompany->id ?? '' }}
                                        </td>
                                        <td>
                                            {{ $docCompany->name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $docCompany->nipc ?? '' }}
                                        </td>
                                        <td>
                                            {{ $docCompany->license_number ?? '' }}
                                        </td>
                                        <td>
                                            {{ $docCompany->address ?? '' }}
                                        </td>
                                        <td>
                                            {{ $docCompany->location ?? '' }}
                                        </td>
                                        <td>
                                            {{ $docCompany->zip ?? '' }}
                                        </td>
                                        <td>
                                            {{ $docCompany->country ?? '' }}
                                        </td>
                                        <td>
                                            @can('doc_company_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.doc-companies.show', $docCompany->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                            @can('doc_company_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('admin.doc-companies.edit', $docCompany->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan

                                            @can('doc_company_delete')
                                                <form action="{{ route('admin.doc-companies.destroy', $docCompany->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('doc_company_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.doc-companies.massDestroy') }}",
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
  let table = $('.datatable-DocCompany:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection