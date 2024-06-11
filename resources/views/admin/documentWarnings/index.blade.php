@extends('layouts.admin')
@section('content')
<div class="content">
    @can('document_warning_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.document-warnings.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.documentWarning.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.documentWarning.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-DocumentWarning">
                            <thead>
                                <tr>
                                    <th width="10">

                                    </th>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.id') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.citizen_card') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.tvde_driver_certificate') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.criminal_record') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.profile_picture') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.driving_license') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.iban') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.dua_vehicle') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.car_insurance') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.ipo_vehicle') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documentWarnings as $key => $documentWarning)
                                    <tr data-entry-id="{{ $documentWarning->id }}">
                                        <td>

                                        </td>
                                        <td>
                                            {{ $documentWarning->id ?? '' }}
                                        </td>
                                        <td>
                                            <span style="display:none">{{ $documentWarning->citizen_card ?? '' }}</span>
                                            <input type="checkbox" disabled="disabled" {{ $documentWarning->citizen_card ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <span style="display:none">{{ $documentWarning->tvde_driver_certificate ?? '' }}</span>
                                            <input type="checkbox" disabled="disabled" {{ $documentWarning->tvde_driver_certificate ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <span style="display:none">{{ $documentWarning->criminal_record ?? '' }}</span>
                                            <input type="checkbox" disabled="disabled" {{ $documentWarning->criminal_record ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <span style="display:none">{{ $documentWarning->profile_picture ?? '' }}</span>
                                            <input type="checkbox" disabled="disabled" {{ $documentWarning->profile_picture ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <span style="display:none">{{ $documentWarning->driving_license ?? '' }}</span>
                                            <input type="checkbox" disabled="disabled" {{ $documentWarning->driving_license ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <span style="display:none">{{ $documentWarning->iban ?? '' }}</span>
                                            <input type="checkbox" disabled="disabled" {{ $documentWarning->iban ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <span style="display:none">{{ $documentWarning->dua_vehicle ?? '' }}</span>
                                            <input type="checkbox" disabled="disabled" {{ $documentWarning->dua_vehicle ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <span style="display:none">{{ $documentWarning->car_insurance ?? '' }}</span>
                                            <input type="checkbox" disabled="disabled" {{ $documentWarning->car_insurance ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <span style="display:none">{{ $documentWarning->ipo_vehicle ?? '' }}</span>
                                            <input type="checkbox" disabled="disabled" {{ $documentWarning->ipo_vehicle ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            @can('document_warning_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.document-warnings.show', $documentWarning->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                            @can('document_warning_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('admin.document-warnings.edit', $documentWarning->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan

                                            @can('document_warning_delete')
                                                <form action="{{ route('admin.document-warnings.destroy', $documentWarning->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('document_warning_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.document-warnings.massDestroy') }}",
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
  let table = $('.datatable-DocumentWarning:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection