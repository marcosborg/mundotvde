@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Dashboard
                </div>

                <div class="panel-body">
                    @can('dashboard')
                    @if ($activityLaunches->count() > 0)
                    <ul class="list-group">
                        @foreach ($activityLaunches as $activityLaunch)
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-md-3">
                                    <ul class="list-group">
                                        <li class="list-group-item">{{
                                            \Carbon\Carbon::parse($activityLaunch->week->start_date)->format('d-m-Y') }}
                                            a {{
                                            \Carbon\Carbon::parse($activityLaunch->week->end_date)->format('d-m-Y') }}
                                            <span class="badge">Semana
                                                {{ $activityLaunch->week->number }}</span>
                                        </li>
                                        <li class="list-group-item"><strong>Aluguer: </strong>€ {{ $activityLaunch->rent
                                            }}</li>
                                        <li class="list-group-item"><strong>Gestão: </strong>€ {{
                                            $activityLaunch->management }}</li>
                                        <li class="list-group-item"><strong>Seguro: </strong>€ {{
                                            $activityLaunch->insurance }}</li>
                                    </ul>
                                </div>
                                <div class="col-md-3">
                                    <ul class="list-group">
                                        <li class="list-group-item"><strong>Combustivel: </strong>€ {{
                                            $activityLaunch->rent }}</li>
                                        <li class="list-group-item"><strong>Portagens: </strong>€ {{
                                            $activityLaunch->tolls }}</li>
                                        <li class="list-group-item"><strong>Débitos: </strong>€ {{
                                            $activityLaunch->others }}</li>
                                        <li class="list-group-item"><strong>Créditos: </strong>€ {{
                                            $activityLaunch->refund }}</li>
                                    </ul>
                                </div>
                                <div class="col-md-3">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Operador</th>
                                                <th>Líquido</th>
                                                <th>Impostos</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($activityLaunch->activityPerOperators as $activityPerOperator)
                                            <tr>
                                                <td>{{ $activityPerOperator->tvde_operator->name }}</td>
                                                <td>€ {{ $activityPerOperator->net }}</td>
                                                <td>€ {{ $activityPerOperator->taxes }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-3">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Ganhos</th>
                                                        <td>€ {{ $activityLaunch->sum + $activityLaunch->refund }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Descontos</th>
                                                        <td>€ {{ $activityLaunch->sub }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Total</th>
                                                        <th>€ {{ $activityLaunch->total }}</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                            @if ($activityLaunch->paid == 1)
                                                <span class="badge">Pago</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="alert alert-info" role="alert">Ainda não existem registos de atividade.</div>
                    @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('styles')
<style>
    th,
    td {
        padding: 1px !important;
    }

    table {
        margin-bottom: 10px !important;
    }
</style>

@endsection
@section('scripts')
@parent

@endsection