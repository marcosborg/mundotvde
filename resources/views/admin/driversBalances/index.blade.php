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
                                <th>
                                    Condutor
                                </th>
                                <th>
                                    Iban
                                </th>
                                <th>
                                    Email
                                </th>
                                <th>
                                    Balanço
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($drivers as $driver)
                            <tr>
                                <td>{{ $driver->name }}</td>
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
    $('.datatable').DataTable();
</script>
@endsection