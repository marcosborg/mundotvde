@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.financialStatement.title') }}
                </div>
                <div class="panel-body">
                    <table
                        class="table table-bordered table-striped table-hover datatable datatable-financialStatement">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ano</th>
                                <th>Mês</th>
                                <th>Semana</th>
                                <th>Datas</th>
                                <th>Ganhos</th>
                                <th>Descontos</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activityLaunches as $activityLaunch)
                            <tr>
                                <td>{{ $activityLaunch->id }}</td>
                                <td>{{ $activityLaunch->week->tvde_month->year->name }}</td>
                                <td>{{ $activityLaunch->week->tvde_month->name }}</td>
                                <td>{{ $activityLaunch->week->number }}</td>
                                <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $activityLaunch->week->start_date)->day
                                    }} a {{ \Carbon\Carbon::createFromFormat('Y-m-d',
                                    $activityLaunch->week->end_date)->day }}</td>
                                <td>€ {{ number_format($activityLaunch->sum, 2, '.', '') }}</td>
                                <td>€ {{ number_format($activityLaunch->sub, 2, '.', '') }}</td>
                                <td>€ {{ number_format($activityLaunch->total, 2, '.', '') }}</td>
                                <td>
                                    <a href="/admin/financial-statements/pdf/{{ $activityLaunch->id }}" class="btn btn-success btn-sm">Download</a>
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
@endsection
@section('styles')
<style>
    .select-checkbox::before {
        display: none !important;
    }
</style>
@endsection
@section('scripts')
<script>

$(() => {
    $('.datatable-financialStatement').DataTable();
});

</script>
@endsection