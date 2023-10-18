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
                    @php
                    $total = 0;
                    @endphp
                    @foreach ($activityLaunches as $activityLaunch)
                    @if ($activityLaunch->paid == 0)
                    @php
                    $total += $activityLaunch->total;
                    @endphp
                    @endif
                    @endforeach
                    @php
                    if ($last_receipt) {
                    $last_receipt_time = strtotime($last_receipt->created_at) + (24 * 3600);
                    $current_time = time();
                    $disable_button = ($total == 0 || $current_time < $last_receipt_time) ? 'disabled' : '' ; } else {
                        $disable_button='' ; } @endphp <span class="budget">Saldo: € {{ number_format($total, 2)
                        }}<span>
                            <button class="btn btn-success btn-sm" {{ $disable_button }}
                                onclick="openModalReceipt('{{ number_format($total, 2) }}')">Enviar recibo</button>
                        </span></span>
                        <ul class="list-group">
                            <script>
                                console.log({!! $activityLaunches !!})
                            </script>
                            @foreach ($activityLaunches as $activityLaunch)
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-3">
                                        <ul class="list-group">
                                            <li class="list-group-item">{{
                                                \Carbon\Carbon::parse($activityLaunch->week->start_date)->format('d-m-Y')
                                                }}
                                                a {{
                                                \Carbon\Carbon::parse($activityLaunch->week->end_date)->format('d-m-Y')
                                                }}
                                                <span class="badge">Semana
                                                    {{ $activityLaunch->week->number }}</span>
                                            </li>
                                            <li class="list-group-item"><strong>Aluguer: </strong>€ {{
                                                $activityLaunch->rent
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
                                                $activityLaunch->fuel }}</li>
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
                                                            <td>€ {{ $activityLaunch->sum + $activityLaunch->refund }}
                                                            </td>
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
</div>

<!-- Modal -->
<div class="modal fade" id="receipt-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Enviar recibo</h4>
            </div>
            <form action="/admin/my-receipts/create" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Valor do recibo</label>
                        <input type="text" class="form-control" value="0.00" name="value" id="value">
                    </div>
                    <div class="form-group">
                        <label>Ficheiro</label>
                        <div class="needsclick dropzone" id="file-dropzone">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </div>
            </form>
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

    .budget {
        font-size: 20px;
        font-weight: bold;
        text-transform: uppercase;
        background: #eeeeee;
        border: solid 1px #ccc;
        padding: 10px;
        display: flex;
        margin-bottom: 10px;
        justify-content: space-between;
    }
</style>

@endsection
@section('scripts')
@parent
<script>
    Dropzone.options.fileDropzone = {
    url: '{{ route('admin.receipts.storeMedia') }}',
    maxFilesize: 5, // MB
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 5
    },
    success: function (file, response) {
      $('form').find('input[name="file"]').remove()
      $('form').append('<input type="hidden" name="file" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="file"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($receipt) && $receipt->file)
      var file = {!! json_encode($receipt->file) !!}
          this.options.addedfile.call(this, file)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="file" value="' + file.file_name + '">')
      this.options.maxFiles = this.options.maxFiles - 1
@endif
    },
     error: function (file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends it's own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }

         return _results
     }
}
    openModalReceipt = (value) => {
        $('#receipt-modal').modal('show');
        $('#value').val(value.replace(/,/g, ""));
        console.log(value);
    }
</script>
@endsection