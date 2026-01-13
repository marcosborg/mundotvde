@extends('layouts.admin')
@section('content')
<div class="content">
    @can('driver_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.drivers.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.driver.title_singular') }}
                </a>
                <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                    {{ trans('global.app_csvImport') }}
                </button>
                @include('csvImport.modal', ['model' => 'Driver', 'route' => 'admin.drivers.parseCsvImport'])
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.driver.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <div class="datatable-columns-panel" data-table-key="drivers" data-table-selector=".datatable-Driver" style="margin-bottom: 10px;">
                        <button type="button" class="btn btn-default btn-xs" data-toggle="datatable-columns">Colunas visiveis</button>
                        <div class="datatable-columns-menu" style="display: none; margin-top: 10px;"></div>
                    </div>
                    <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Driver">
                        <thead>
                            <tr>
                                <th width="10">

                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.id') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.user') }}
                                </th>
                                <th>
                                    {{ trans('cruds.user.fields.email') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.code') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.name') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.tvde_operator') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.card') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.operation') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.local') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.start_date') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.end_date') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.reason') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.phone') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.payment_vat') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.citizen_card') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.citizen_card_expiry_date') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.birth_date') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.drivers_certificate') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.drivers_certificate_expiration_date') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.email') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.iban') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.address') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.zip') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.city') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.state') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.driver_license') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.driver_license_expiration_date') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.driver_vat') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.uber_uuid') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.bolt_name') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.license_plate') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.vehicle_date') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.brand') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.model') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.notes') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.created_at') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.updated_at') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.deleted_at') }}
                                </th>
                                <th>
                                    &nbsp;
                                </th>
                            </tr>
                        </thead>
                    </table>
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
@can('driver_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.drivers.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
          return entry.id
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

  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: "{{ route('admin.drivers.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'user_name', name: 'user.name' },
{ data: 'user.email', name: 'user.email' },
{ data: 'code', name: 'code' },
{ data: 'name', name: 'name' },
{ data: 'tvde_operator', name: 'tvde_operators.name' },
{ data: 'card_code', name: 'card.code' },
{ data: 'operation_name', name: 'operation.name' },
{ data: 'local_name', name: 'local.name' },
{ data: 'start_date', name: 'start_date' },
{ data: 'end_date', name: 'end_date' },
{ data: 'reason', name: 'reason' },
{ data: 'phone', name: 'phone' },
{ data: 'payment_vat', name: 'payment_vat' },
{ data: 'citizen_card', name: 'citizen_card' },
{ data: 'citizen_card_expiry_date', name: 'citizen_card_expiry_date' },
{ data: 'birth_date', name: 'birth_date' },
{ data: 'drivers_certificate', name: 'drivers_certificate' },
{ data: 'drivers_certificate_expiration_date', name: 'drivers_certificate_expiration_date' },
{ data: 'email', name: 'email' },
{ data: 'iban', name: 'iban' },
{ data: 'address', name: 'address' },
{ data: 'zip', name: 'zip' },
{ data: 'city', name: 'city' },
{ data: 'state_name', name: 'state.name' },
{ data: 'driver_license', name: 'driver_license' },
{ data: 'driver_license_expiration_date', name: 'driver_license_expiration_date' },
{ data: 'driver_vat', name: 'driver_vat' },
{ data: 'uber_uuid', name: 'uber_uuid' },
{ data: 'bolt_name', name: 'bolt_name' },
{ data: 'license_plate', name: 'license_plate' },
{ data: 'vehicle_date', name: 'vehicle_date' },
{ data: 'brand', name: 'brand' },
{ data: 'model', name: 'model' },
{ data: 'notes', name: 'notes' },
{ data: 'created_at', name: 'created_at' },
{ data: 'updated_at', name: 'updated_at' },
{ data: 'deleted_at', name: 'deleted_at' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-Driver').DataTable(dtOverrideGlobals);
  initDataTableColumnPreferences(table, $('.datatable-columns-panel'));
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection
