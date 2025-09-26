@extends('layouts.admin')
@section('content')
<div class="content">
    @can('crm_form_submission_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.crm-form-submissions.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.crmFormSubmission.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.crmFormSubmission.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-CrmFormSubmission">
                        <thead>
                            <tr>
                                <th width="10">

                                </th>
                                <th>
                                    {{ trans('cruds.crmFormSubmission.fields.id') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormSubmission.fields.form') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormSubmission.fields.category') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormSubmission.fields.submitted_at') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormSubmission.fields.user_agent') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormSubmission.fields.referer') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormSubmission.fields.utm_json') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormSubmission.fields.data_json') }}
                                </th>
                                <th>
                                    {{ trans('cruds.crmFormSubmission.fields.created_card') }}
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
@can('crm_form_submission_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.crm-form-submissions.massDestroy') }}",
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
    ajax: "{{ route('admin.crm-form-submissions.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'form_name', name: 'form.name' },
{ data: 'category_name', name: 'category.name' },
{ data: 'submitted_at', name: 'submitted_at' },
{ data: 'user_agent', name: 'user_agent' },
{ data: 'referer', name: 'referer' },
{ data: 'utm_json', name: 'utm_json' },
{ data: 'data_json', name: 'data_json' },
{ data: 'created_card_title', name: 'created_card.title' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-CrmFormSubmission').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection