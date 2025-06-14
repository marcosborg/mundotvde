@extends('layouts.admin')
@section('content')
<div class="content">
    @can('courier_form_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.courier-forms.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.courierForm.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.courierForm.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-CourierForm">
                            <thead>
                                <tr>
                                    <th width="10">

                                    </th>
                                    <th>
                                        {{ trans('cruds.courierForm.fields.id') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.courierForm.fields.name') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.courierForm.fields.phone') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.courierForm.fields.email') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.courierForm.fields.city') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.courierForm.fields.courier') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.courierForm.fields.account') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.courierForm.fields.rgpd') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.courierForm.fields.obs') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.courierForm.fields.created_at') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courierForms as $key => $courierForm)
                                    <tr data-entry-id="{{ $courierForm->id }}">
                                        <td>

                                        </td>
                                        <td>
                                            {{ $courierForm->id ?? '' }}
                                        </td>
                                        <td>
                                            {{ $courierForm->name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $courierForm->phone ?? '' }}
                                        </td>
                                        <td>
                                            {{ $courierForm->email ?? '' }}
                                        </td>
                                        <td>
                                            {{ $courierForm->city ?? '' }}
                                        </td>
                                        <td>
                                            <span style="display:none">{{ $courierForm->courier ?? '' }}</span>
                                            <input type="checkbox" disabled="disabled" {{ $courierForm->courier ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            {{ $courierForm->account ?? '' }}
                                        </td>
                                        <td>
                                            <span style="display:none">{{ $courierForm->rgpd ?? '' }}</span>
                                            <input type="checkbox" disabled="disabled" {{ $courierForm->rgpd ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            {!! $courierForm->obs ? '<span class="badge">Sim</span>' : '' !!}
                                        </td>
                                        <td>
                                            {{ $courierForm->created_at ?? '' }}
                                        </td>
                                        <td>
                                            @can('courier_form_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.courier-forms.show', $courierForm->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                            @can('courier_form_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('admin.courier-forms.edit', $courierForm->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan

                                            @can('courier_form_delete')
                                                <form action="{{ route('admin.courier-forms.destroy', $courierForm->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('courier_form_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.courier-forms.massDestroy') }}",
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
  let table = $('.datatable-CourierForm:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection