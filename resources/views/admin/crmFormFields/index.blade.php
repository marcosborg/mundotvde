@extends('layouts.admin')
@section('content')
<div class="content">
    @can('crm_form_field_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.crm-form-fields.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.crmFormField.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.crmFormField.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-CrmFormField">
                        <thead>
                            <tr>
                                <th width="10">

                                </th>
                                <th>
                                    {{ trans('cruds.crmFormField.fields.id') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormField.fields.form') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormField.fields.label') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormField.fields.type') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormField.fields.required') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormField.fields.help_text') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormField.fields.placeholder') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormField.fields.default_value') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormField.fields.is_unique') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormField.fields.min_value') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormField.fields.max_value') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormField.fields.options_json') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormField.fields.position') }}
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
@can('crm_form_field_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.crm-form-fields.massDestroy') }}",
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
    ajax: "{{ route('admin.crm-form-fields.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'form_name', name: 'form.name' },
{ data: 'label', name: 'label' },
{ data: 'type', name: 'type' },
{ data: 'required', name: 'required' },
{ data: 'help_text', name: 'help_text' },
{ data: 'placeholder', name: 'placeholder' },
{ data: 'default_value', name: 'default_value' },
{ data: 'is_unique', name: 'is_unique' },
{ data: 'min_value', name: 'min_value' },
{ data: 'max_value', name: 'max_value' },
{ data: 'options_json', name: 'options_json' },
{ data: 'position', name: 'position' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-CrmFormField').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection