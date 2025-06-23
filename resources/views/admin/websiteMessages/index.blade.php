@extends('layouts.admin')
@section('content')
<div class="content">
    @can('website_message_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.website-messages.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.websiteMessage.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.websiteMessage.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-WebsiteMessage">
                        <thead>
                            <tr>
                                <th width="10">

                                </th>
                                <th>
                                    {{ trans('cruds.websiteMessage.fields.id') }}
                                </th>
                                <th>
                                    {{ trans('cruds.websiteMessage.fields.email') }}
                                </th>
                                <th>
                                    {{ trans('cruds.websiteMessage.fields.messages') }}
                                </th>
                                <th>
                                    Data
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
@can('website_message_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.website-messages.massDestroy') }}",
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
    ajax: "{{ route('admin.website-messages.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'email', name: 'email' },
{ data: 'messages', name: 'messages' },
{ data: 'updated_at', name: 'updated_at' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-WebsiteMessage').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection

@section('styles')
    <style>
    .chat-preview {
        display: flex;
        flex-direction: column;
        gap: 5px;
        max-width: 100%;
    }

    .user-bubble {
        align-self: flex-end;
        background-color: #d1e7dd;
        color: #0f5132;
        padding: 8px 12px;
        border-radius: 15px 15px 0 15px;
        font-size: 13px;
    }

    .assistant-bubble {
        align-self: flex-start;
        background-color: #f8d7da;
        color: #842029;
        padding: 8px 12px;
        border-radius: 15px 15px 15px 0;
        font-size: 13px;
    }
</style>

@endsection