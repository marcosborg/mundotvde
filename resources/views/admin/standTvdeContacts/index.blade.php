@extends('layouts.admin')
@section('content')
<div class="content">
    @can('stand_tvde_contact_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.stand-tvde-contacts.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.standTvdeContact.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.standTvdeContact.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-StandTvdeContact">
                            <thead>
                                <tr>
                                    <th width="10">

                                    </th>
                                    <th>
                                        {{ trans('cruds.standTvdeContact.fields.id') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.standTvdeContact.fields.name') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.standTvdeContact.fields.email') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.standTvdeContact.fields.phone') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.standTvdeContact.fields.car') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.standTvdeContact.fields.subject') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.standTvdeContact.fields.message') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.standTvdeContact.fields.created_at') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($standTvdeContacts as $key => $standTvdeContact)
                                    <tr data-entry-id="{{ $standTvdeContact->id }}">
                                        <td>

                                        </td>
                                        <td>
                                            {{ $standTvdeContact->id ?? '' }}
                                        </td>
                                        <td>
                                            {{ $standTvdeContact->name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $standTvdeContact->email ?? '' }}
                                        </td>
                                        <td>
                                            {{ $standTvdeContact->phone ?? '' }}
                                        </td>
                                        <td>
                                            {{ $standTvdeContact->car ?? '' }}
                                        </td>
                                        <td>
                                            {{ $standTvdeContact->subject ?? '' }}
                                        </td>
                                        <td>
                                            {{ $standTvdeContact->message ?? '' }}
                                        </td>
                                        <td>
                                            {{ $standTvdeContact->created_at ?? '' }}
                                        </td>
                                        <td>
                                            @can('stand_tvde_contact_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.stand-tvde-contacts.show', $standTvdeContact->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                            @can('stand_tvde_contact_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('admin.stand-tvde-contacts.edit', $standTvdeContact->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan

                                            @can('stand_tvde_contact_delete')
                                                <form action="{{ route('admin.stand-tvde-contacts.destroy', $standTvdeContact->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('stand_tvde_contact_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.stand-tvde-contacts.massDestroy') }}",
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
  let table = $('.datatable-StandTvdeContact:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection