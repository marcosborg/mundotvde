<div>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#not-paid" aria-controls="not-paid" role="tab"
                data-toggle="tab">Extratos por enviar</a></li>
        <li role="presentation"><a href="#paid" aria-controls="paid" role="tab" data-toggle="tab">Extratos enviados</a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="not-paid">
            <div class="table-responsive" style="margin-top: 20px;">
                <table class=" table table-bordered table-striped table-hover datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Condutor</th>
                            <th>Semana</th>
                            <th>Valor</th>
                            <th>Selecionar para enviar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notPaid as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->driver->name }}</td>
                            <td><span class="badge">{{ $item->week->number }}</span> <small>de {{
                                    \Carbon\Carbon::parse($item->week->start_date)->format('d-m-Y')
                                    }} a {{
                                    \Carbon\Carbon::parse($item->week->end_date)->format('d-m-Y')
                                    }}</small></td>
                            <td>{{ $item->total }}</td>
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
            <button style="display: none;" id="paymentButton" onclick="confirmPay()" class="btn btn-success">Confirmar
                envio de extrato</button>
        </div>
        <div role="tabpanel" class="tab-pane" id="paid">
            <div class="table-responsive" style="margin-top: 20px;">
                <table class=" table table-bordered table-striped table-hover datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Condutor</th>
                            <th>Semana</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paid as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->driver->name }}</td>
                            <td><span class="badge">{{ $item->week->number }}</span> <small>de {{
                                    \Carbon\Carbon::parse($item->week->start_date)->format('d-m-Y')
                                    }} a {{
                                    \Carbon\Carbon::parse($item->week->end_date)->format('d-m-Y')
                                    }}</small></td>
                            <td>{{ $item->total }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>