<div>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        @php
        $count = 1;
        $total = $years->count();
        @endphp
        @foreach ($years as $year)
        <li role="presentation" class="{{ ($selectedMonth && (int) $selectedMonth->year_id === (int) $year->id) ? 'active' : '' }}"><a href="#year-{{ $year->id }}"
                aria-controls="year-{{ $year->id }}" role="tab" data-toggle="tab">{{ $year->name }}</a></li>
        @endforeach
    </ul>

    @php
        $activeYearId = $selectedMonth ? (int) $selectedMonth->year_id : null;
    @endphp
    <div class="tab-content" style="margin-top: 20px;">
        @foreach ($years as $year)
        <div role="tabpanel" class="tab-pane {{ $activeYearId === (int) $year->id ? 'active' : '' }}" id="year-{{ $year->id }}">
            <div>
                <ul class="nav nav-tabs" role="tablist">
                    @foreach ($year->months as $month)
                    <li role="presentation" class="{{ ($selectedMonthId ?? null) == $month->id ? 'active' : '' }}">
                        <a href="{{ route('admin.tvde-driver-managements.index', ['month_id' => $month->id]) }}">
                            {{ $month->name }}
                        </a>
                    </li>
                    @endforeach
                </ul>

                @if($selectedMonth && (int) $selectedMonth->year_id === (int) $year->id)
                <div style="margin-top: 20px;">
                    <ul class="nav nav-tabs" role="tablist">
                        @php
                            $weeks = $selectedMonth->weeks->sortBy('number')->values();
                            $activeWeek = $weeks->last();
                            $activeWeekId = optional($activeWeek)->id;
                        @endphp
                        @foreach ($weeks as $week)
                        <li role="presentation" class="{{ $week->id === $activeWeekId ? 'active' : '' }}"><a
                                href="#week-{{ $week->id }}" aria-controls="week-{{ $week->id }}" role="tab"
                                data-toggle="tab"><span class="badge">Semana {{ $week->number }}</span> de {{ \Carbon\Carbon::parse($week->start_date)->format('d') }} a
                                {{ \Carbon\Carbon::parse($week->end_date)->format('d') }}
                                @if($week->isClosed())
                                    <span class="label label-danger">Fechada</span>
                                @else
                                    <span class="label label-success">Aberta</span>
                                @endif
                            </a></li>
                        @endforeach
                    </ul>
                    
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-4">
                            @if($activeWeek && $activeWeek->isOpen())
                            <form action="/admin/tvde-driver-managements/driver" method="post" class="driver_form">
                                @csrf
                                <input type="hidden" name="week_id" value="{{ $activeWeek->id }}">
                                <div class="input-group">
                                    <select class="form-control select2" name="driver_id" required>
                                        <option selected disabled>Selecionar condutor</option>
                                        @php
                                            $drivers = \App\Models\Driver::whereDoesntHave('activity_launches', function($query) use ($activeWeek){
                                                $query->where('week_id', $activeWeek->id);
                                            })
                                            ->get()->load('card');
                                        @endphp
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="input-group-btn">
                                    <button class="btn btn-success" type="submit">Lan&ccedil;ar
                                        atividade</button>
                                    </span>
                                </div>
                            </form>
                            @endif
                        </div>
                        <div class="col-md-4">
                            @if($activeWeek && $activeWeek->isOpen())
                            <a href="/admin/tvde-driver-managements/launch-all-activities/{{ $activeWeek->id }}" class="btn btn-primary">Lan&ccedil;ar todas as atividades</a>
                            @endif
                        </div>
                    </div>
                    
                    <div class="tab-content" style="margin-top: 20px;">
                        @foreach ($weeks as $week)
                        <div role="tabpanel" class="tab-pane {{ $week->id === $activeWeekId ? 'active' : '' }}"
                            id="week-{{ $week->id }}">
                            <div class="clearfix" style="margin-bottom: 15px;">
                                <div class="pull-left">
                                    @if($week->isClosed())
                                        <span class="label label-danger" style="font-size: 13px;">Semana Fechada</span>
                                        <span style="margin-left: 8px;">
                                            Fechada por: {{ $week->lockedBy->name ?? '-' }}
                                            @if($week->locked_at)
                                                | Data: {{ \Carbon\Carbon::parse($week->locked_at)->format('d/m/Y H:i') }}
                                            @endif
                                        </span>
                                    @else
                                        <span class="label label-success" style="font-size: 13px;">Semana Aberta</span>
                                    @endif
                                </div>
                                <div class="pull-right">
                                    @if($week->isOpen())
                                        @can('tvde_week_close')
                                            <form action="{{ route('admin.tvde-weeks.close', $week->id) }}" method="POST" onsubmit="return confirm('Fechar esta semana e arquivar todos os extratos?');" style="display: inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm">Fechar Semana</button>
                                            </form>
                                        @endcan
                                    @else
                                        @can('tvde_week_reopen')
                                            <form action="{{ route('admin.tvde-weeks.reopen', $week->id) }}" method="POST" onsubmit="return confirm('Reabrir esta semana para edicao? Os snapshots antigos serao mantidos.');" style="display: inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">Reabrir Semana</button>
                                            </form>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                            <button class="btn btn-default" onclick="exportCsv('week_{{ $week->id }}')" style="margin-bottom: 20px;">Exportar CSV</button>
                            <div style="overflow-x: auto; width: 100%;">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Condutor</th>
                                        <th>C&oacute;digo</th>
                                        <th>Cart&atilde;o</th>
                                        <th>Opera&ccedil;&atilde;o</th>
                                        <th>Matr&iacute;cula</th>
                                        <th>Aluguer</th>
                                        <th>Gest&atilde;o</th>
                                        <th>Seguro</th>
                                        <th>Combust&iacute;vel</th>
                                        <th>Portagens</th>
                                        <th>Oficina</th>
                                        <th>Cau&ccedil;&atilde;o</th>
                                        <th>D&eacute;bitos</th>
                                        <th>Cr&eacute;ditos</th>
                                        @foreach ($week->activityLaunches as $key => $activityLaunch)
                                        @if($key == 0)
                                        @foreach ($activityLaunch->activityPerOperators as $activityPerOperator)
                                        <th>
                                            {{ $activityPerOperator->tvde_operator->name }} Bruto
                                        </th>
                                        <th>
                                            {{ $activityPerOperator->tvde_operator->name }} L&iacute;quido
                                        </th>
                                        <th>
                                            {{ $activityPerOperator->tvde_operator->name }} Impostos
                                        </th>
                                        @endforeach
                                        @endif
                                        @endforeach
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($week->activityLaunches as $activityLaunch)
                                    <tr>
                                        <td>{{ $activityLaunch->driver->name }}</td>
                                        <td>{{ $activityLaunch->driver->code }}</td>
                                        <td>{{ $activityLaunch->driver->card->code ?? '' }}</td>
                                        <td>{{ $activityLaunch->driver->operation->name ?? '' }}</td>
                                        <td>{{ $activityLaunch->driver->license_plate ?? '' }}</td>
                                        <td>{{ $activityLaunch->rent }}</td>
                                        <td>{{ $activityLaunch->management }}</td>
                                        <td>{{ $activityLaunch->insurance }}</td>
                                        <td>{{ $activityLaunch->fuel }}</td>
                                        <td>{{ $activityLaunch->tolls }}</td>
                                        <td>{{ $activityLaunch->garage }}</td>
                                        <td>{{ $activityLaunch->management_fee }}</td>
                                        <td>{{ $activityLaunch->others }}</td>
                                        <td>{{ $activityLaunch->refund }}</td>
                                        @php
                                            $sum = [];
                                        @endphp
                                        @foreach ($activityLaunch->activityPerOperators as $activityPerOperator)
                                        @php
                                            $sum[] = $activityPerOperator->net - $activityPerOperator->taxes;
                                        @endphp
                                        <td>
                                            {{ $activityPerOperator->gross }}
                                        </td>
                                        <td>
                                            {{ $activityPerOperator->net }}
                                        </td>
                                        <td>
                                            {{ $activityPerOperator->taxes }}
                                        </td>
                                        @endforeach
                                        @php
                                            $sum = array_sum($sum);
                                            $sub = [
                                                $activityLaunch->rent,
                                                $activityLaunch->management,
                                                $activityLaunch->insurance,
                                                $activityLaunch->management_fee,
                                                $activityLaunch->fuel,
                                                $activityLaunch->tolls,
                                                $activityLaunch->garage,
                                                $activityLaunch->others
                                            ];
                                            $sub = array_sum($sub);
                                            $total = $sum - $sub + $activityLaunch->refund;
                                        @endphp
                                        <td>
                                            {{ $total }}
                                        </td>
                                        <td>
                                            @if($week->isOpen())
                                                <button type="button" class="btn btn-xs btn-info"
                                                    onclick="showActivityLaunch({{ $activityLaunch->id }})">
                                                    Editar
                                                </button>
                                                <button type="button" class="btn btn-xs btn-danger"
                                                    onclick="deleteActivityLaunch({{ $activityLaunch->id }})">
                                                    Eliminar
                                                </button>
                                            @else
                                                <span class="label label-danger">Bloqueado</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                            <div style="overflow-x: auto; width: 100%;">
                            <table id="week_{{ $week->id }}" style="visibility: hidden;">
                                <thead>
                                    <tr>
                                        <th>Condutor</th>
                                        <th>C&oacute;digo</th>
                                        <th>Cart&atilde;o</th>
                                        <th>Opera&ccedil;&atilde;o</th>
                                        <th>Matr&iacute;cula</th>
                                        <th>Aluguer</th>
                                        <th>Gest&atilde;o</th>
                                        <th>Seguro</th>
                                        <th>Combust&iacute;vel</th>
                                        <th>Portagens</th>
                                        <th>Oficina</th>
                                        <th>Cau&ccedil;&atilde;o</th>
                                        <th>D&eacute;bitos</th>
                                        <th>Cr&eacute;ditos</th>
                                        @foreach ($week->activityLaunches as $key => $activityLaunch)
                                        @if($key == 0)
                                        @foreach ($activityLaunch->activityPerOperators as $activityPerOperator)
                                        <th>
                                            {{ $activityPerOperator->tvde_operator->name }} Bruto
                                        </th>
                                        <th>
                                            {{ $activityPerOperator->tvde_operator->name }} L&iacute;quido
                                        </th>
                                        <th>
                                            {{ $activityPerOperator->tvde_operator->name }} Impostos
                                        </th>
                                        @endforeach
                                        @endif
                                        @endforeach
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($week->activityLaunches as $activityLaunch)
                                    <tr>
                                        <td>{{ $activityLaunch->driver->name }}</td>
                                        <td>{{ $activityLaunch->driver->code }}</td>
                                        <td>{{ $activityLaunch->driver->card->code ?? '' }}</td>
                                        <td>{{ $activityLaunch->driver->operation->name ?? '' }}</td>
                                        <td>{{ $activityLaunch->driver->license_plate ?? '' }}</td>
                                        <td>{{ $activityLaunch->rent }}</td>
                                        <td>{{ $activityLaunch->management }}</td>
                                        <td>{{ $activityLaunch->insurance }}</td>
                                        <td>{{ $activityLaunch->fuel }}</td>
                                        <td>{{ $activityLaunch->tolls }}</td>
                                        <td>{{ $activityLaunch->garage }}</td>
                                        <td>{{ $activityLaunch->management_fee }}</td>
                                        <td>{{ $activityLaunch->others }}</td>
                                        <td>{{ $activityLaunch->refund }}</td>
                                            @php
                                                $sum = [];
                                            @endphp
                                            @foreach ($activityLaunch->activityPerOperators as $activityPerOperator)
                                            @php
                                                $sum[] = $activityPerOperator->net - $activityPerOperator->taxes;
                                            @endphp
                                            <td>
                                            {{ $activityPerOperator->gross }}
                                            </td>
                                            <td>
                                            {{ $activityPerOperator->net }}
                                            </td>
                                            <td>
                                            {{ $activityPerOperator->taxes }}
                                            </td>
                                            @endforeach
                                        @php
                                            $sum = array_sum($sum);
                                            $sub = [
                                                $activityLaunch->rent,
                                                $activityLaunch->management,
                                                $activityLaunch->insurance,
                                                $activityLaunch->management_fee,
                                                $activityLaunch->fuel,
                                                $activityLaunch->tolls,
                                                $activityLaunch->garage,
                                                $activityLaunch->others
                                            ];
                                            $sub = array_sum($sub);
                                            $total = $sum - $sub + $activityLaunch->refund;
                                        @endphp
                                        <td>
                                            {{ $total }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
