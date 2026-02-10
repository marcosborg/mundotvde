@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.driversBalance.title') }}
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th style="display: none;"></th>
                                <th>
                                    Condutor
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.code') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.operation') }}
                                </th>
                                <th>
                                    {{ trans('cruds.driver.fields.license_plate') }}
                                </th>
                                <th>
                                    Iban
                                </th>
                                <th>
                                    Email
                                </th>
                                <th class="sortable">
                                    Balanço
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($drivers as $driver)
                            <tr>
                                <td style="display: none"></td>
                                <td>{{ $driver->name }}</td>
                                <td>{{ $driver->code ?? '' }}</td>
                                <td>{{ $driver->operation->name ?? '' }}</td>
                                <td>{{ $driver->license_plate ?? '' }}</td>
                                <td>{{ $driver->iban }}</td>
                                <td>{{ $driver->email }}</td>
                                <td>{{ $driver->balance }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>

    $(() => {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);
        $('.datatable').DataTable({
            buttons: dtButtons,
            columnDefs: [{
                targets: 'sortable',
                orderable: true,
            }],
        });
    });
    
</script>
@endsection
