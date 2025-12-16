<div>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        @php
        $count = 1;
        $total = $years->count();
        @endphp
        @foreach ($years as $year)
        <li role="presentation" class="{{ ($selectedMonth && $selectedMonth->tvde_year_id === $year->id) ? 'active' : '' }}"><a href="#year-{{ $year->id }}"
                aria-controls="year-{{ $year->id }}" role="tab" data-toggle="tab">{{ $year->name }}</a></li>
        @endforeach
    </ul>

    @php
        $activeYearId = $selectedMonth ? $selectedMonth->year_id : null;
    @endphp
    <div class="tab-content" style="margin-top: 20px;">
        @foreach ($years as $year)
        <div role="tabpanel" class="tab-pane {{ $activeYearId === $year->id ? 'active' : '' }}" id="year-{{ $year->id }}">
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

                @if($selectedMonth && $selectedMonth->year_id === $year->id)
                <div style="margin-top: 20px;">
                    <ul class="nav nav-tabs" role="tablist">
                        @php
                            $weekCount = 1;
                            $weekTotal = $selectedMonth->weeks->count();
                        @endphp
                        @foreach ($selectedMonth->weeks as $week)
                        <li role="presentation" class="{{ $weekCount++ == $weekTotal ? 'active' : '' }}"><a
                                href="#week-{{ $week->id }}" aria-controls="week-{{ $week->id }}" role="tab"
                                data-toggle="tab"><span class="badge">Semana {{ $week->number }}</span> de {{ \Carbon\Carbon::parse($week->start_date)->format('d') }} a
                                {{ \Carbon\Carbon::parse($week->end_date)->format('d') }}</a></li>
                        @endforeach
                    </ul>
                    
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-4">
                            @php $firstWeek = $selectedMonth->weeks->first(); @endphp
                            @if($firstWeek)
                            <form action="/admin/tvde-driver-managements/driver" method="post" class="driver_form">
                                @csrf
                                <input type="hidden" name="week_id" value="{{ $firstWeek->id }}">
                                <div class="input-group">
                                    <select class="form-control select2" name="driver_id" required>
                                        <option selected disabled>Selecionar condutor</option>
                                        @php
                                            $drivers = \App\Models\Driver::whereDoesntHave('activity_launches', function($query) use ($firstWeek){
                                                $query->where('week_id', $firstWeek->id);
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
                            @if($firstWeek)
                            <a href="/admin/tvde-driver-managements/launch-all-activities/{{ $firstWeek->id }}" class="btn btn-primary">Lan&ccedil;ar todas as atividades</a>
                            @endif
                        </div>
                    </div>
                    
                    <div class="tab-content" style="margin-top: 20px;">
                        @php
                        $weekCount = 1;
                        $weekTotal = $selectedMonth->weeks->count();
                        @endphp
                        @foreach ($selectedMonth->weeks as $week)
                        <div role="tabpanel" class="tab-pane {{ $weekCount++ == $weekTotal ? 'active' : '' }}"
                            id="week-{{ $week->id }}">
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
                                            <button type="button" class="btn btn-xs btn-info"
                                                onclick="showActivityLaunch({{ $activityLaunch->id }})">
                                                Editar
                                            </button>
                                            <button type="button" class="btn btn-xs btn-danger"
                                                onclick="deleteActivityLaunch({{ $activityLaunch->id }})">
                                                Eliminar
                                            </button>
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
