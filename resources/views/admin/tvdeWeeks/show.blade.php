@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.tvdeWeek.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.tvde-weeks.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.tvdeWeek.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $tvdeWeek->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.tvdeWeek.fields.tvde_month') }}
                                    </th>
                                    <td>
                                        {{ $tvdeWeek->tvde_month->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.tvdeWeek.fields.number') }}
                                    </th>
                                    <td>
                                        {{ $tvdeWeek->number }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.tvdeWeek.fields.start_date') }}
                                    </th>
                                    <td>
                                        {{ $tvdeWeek->start_date }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.tvdeWeek.fields.end_date') }}
                                    </th>
                                    <td>
                                        {{ $tvdeWeek->end_date }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Estado</th>
                                    <td>
                                        @if($tvdeWeek->isClosed())
                                            <span class="label label-danger">Fechada</span>
                                        @else
                                            <span class="label label-success">Aberta</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($tvdeWeek->isClosed())
                                <tr>
                                    <th>Fechada por</th>
                                    <td>{{ $tvdeWeek->lockedBy->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Data de fecho</th>
                                    <td>{{ $tvdeWeek->locked_at ? \Carbon\Carbon::parse($tvdeWeek->locked_at)->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Snapshots</th>
                                    <td>{{ $tvdeWeek->statementSnapshots->count() }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        <div class="form-group">
                            @if($tvdeWeek->isOpen())
                                @can('tvde_week_close')
                                    <form action="{{ route('admin.tvde-weeks.close', $tvdeWeek->id) }}" method="POST" onsubmit="return confirm('Fechar esta semana e arquivar todos os extratos?');" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-warning">Fechar Semana</button>
                                    </form>
                                @endcan
                            @else
                                @can('tvde_week_reopen')
                                    <form action="{{ route('admin.tvde-weeks.reopen', $tvdeWeek->id) }}" method="POST" onsubmit="return confirm('Reabrir esta semana para edicao? Os snapshots antigos serao mantidos.');" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Reabrir Semana</button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.tvde-weeks.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
</div>
@endsection
