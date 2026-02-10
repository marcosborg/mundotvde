<div>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation"><a href="#not-send" aria-controls="not-send" role="tab" data-toggle="tab">Extratos por
                enviar</a></li>
        <li role="presentation" class="active"><a href="#send" aria-controls="send" role="tab"
                data-toggle="tab">Extratos enviados</a>
        </li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane" id="not-send">
            <button class="btn btn-primary btn-sm" style="margin-top: 20px;" onclick="selectAllToSend()">Selecionar tudo
                para enviar</button>
            <div class="table-responsive" style="margin-top: 20px;">
                <table class=" table table-bordered table-striped table-hover datatable datatable-payouts-not-send" id="datatable-payouts-not-send">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Condutor</th>
                            <th>Código</th>
                            <th>Operação</th>
                            <th>Matrícula</th>
                            <th>Email</th>
                            <th>Semana</th>
                            <th>Valor</th>
                            <th></th>
                            <th>Selecionar para enviar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notSend as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->driver->name }}</td>
                            <td>{{ $item->driver->code ?? '' }}</td>
                            <td>{{ $item->driver->operation->name ?? '' }}</td>
                            <td>{{ $item->driver->license_plate ?? '' }}</td>
                            <td>{{ $item->driver->email ?? '' }}</td>
                            <td><span class="badge">{{ $item->week->number }}</span> <small>de {{
                                    \Carbon\Carbon::parse($item->week->start_date)->format('d-m-Y')
                                    }} a {{
                                    \Carbon\Carbon::parse($item->week->end_date)->format('d-m-Y')
                                    }}</small></td>
                            <td>{{ number_format($item->total, 2, ',', '.') }}</td>
                            <td><a href="/admin/financial-statements/pdf/{{ $item->id }}/stream"
                                    class="btn btn-success btn-sm">Extrato</a></td>
                            <td>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="checkboxes" value="{{ $item->id }}"> Enviar
                                    </label>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button style="display: none;" id="paymentButton" onclick="confirmSend()" class="btn btn-success">Confirmar
                envio de extrato</button>
        </div>
        <div role="tabpanel" class="tab-pane active" id="send">
            <div class="table-responsive" style="margin-top: 20px;">
                <table class=" table table-bordered table-striped table-hover datatable datatable-payouts-send" id="datatable-payouts-send">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Condutor</th>
                            <th>Código</th>
                            <th>Operação</th>
                            <th>Matrícula</th>
                            <th>Email</th>
                            <th>Semana</th>
                            <th>De</th>
                            <th>Até</th>
                            <th>Valor</th>
                            @can('payouts_to_driver_edit')
                            <th></th>
                            @endcan
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
