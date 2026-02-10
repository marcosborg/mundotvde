@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.payoutsToDriver.title') }}
                </div>
                <div class="panel-body" id="payoutsToDrivers"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js">
</script>
<script>
    const payoutsSendUrl = "{{ route('admin.payouts-to-drivers.send') }}";
    const canPayPayouts = {{ Gate::allows('pay_payout_access') ? 'true' : 'false' }};
    let sendDataTable = null;

    $(() => {
        ajax();
    });

    ajax = () => {
        $.LoadingOverlay('show');
        $.get('/admin/payouts-to-drivers/ajax').then((resp) => {
            $.LoadingOverlay('hide');
            $('#payoutsToDrivers').html(resp);
            bindNotSendCheckboxes();
            initNotSendTable();
            initSendTable();
            $('a[data-toggle="tab"]').off('shown.bs.tab').on('shown.bs.tab', function() {
                if (sendDataTable) {
                    sendDataTable.columns.adjust();
                }
            });
        });
    }

    bindNotSendCheckboxes = () => {
        $('.checkboxes').off('change').on('change', function() {
            checkChecked();
        });
    };

    initNotSendTable = () => {
        const table = $('#datatable-payouts-not-send');

        if (!table.length) {
            return;
        }

        table.DataTable({
            columnDefs: [{
                targets: 0,
                visible: false
            }],
            order: [
                [7, 'desc']
            ],
            select: false
        });
    };

    initSendTable = () => {
        const table = $('#datatable-payouts-send');

        if (!table.length) {
            return;
        }

        if ($.fn.DataTable.isDataTable(table)) {
            table.DataTable().destroy();
        }

        const columns = [
            {
                data: 'id',
                name: 'activity_launches.id',
                visible: false
            },
            {
                data: 'driver_name',
                name: 'drivers.name',
                orderable: false
            },
            {
                data: 'driver_code',
                name: 'drivers.code',
                orderable: false
            },
            {
                data: 'driver_operation',
                name: 'operations.name',
                orderable: false
            },
            {
                data: 'driver_license_plate',
                name: 'drivers.license_plate',
                orderable: false
            },
            {
                data: 'driver_email',
                name: 'drivers.email',
                orderable: false
            },
            {
                data: 'week_number',
                name: 'tvde_weeks.number',
                orderable: false
            },
            {
                data: 'week_start_date',
                name: 'tvde_weeks.start_date'
            },
            {
                data: 'week_end_date',
                name: 'tvde_weeks.end_date'
            },
            {
                data: 'total',
                name: 'total',
                orderable: false,
                searchable: false
            },
        ];

        if (canPayPayouts) {
            columns.push({
                data: 'pay_action',
                name: 'pay_action',
                orderable: false,
                searchable: false
            });
        }

        columns.push({
            data: 'statement',
            name: 'statement',
            orderable: false,
            searchable: false
        });

        sendDataTable = table.DataTable({
            processing: true,
            serverSide: true,
            ajax: payoutsSendUrl,
            columns: columns,
            order: [
                [7, 'desc']
            ],
            select: false,
            columnDefs: [{
                targets: 0,
                visible: false
            }]
        });
    };

    checkChecked = () => {
        if ($('.checkboxes:checked').length > 0) {
            $('#paymentButton').show();
        } else {
            $('#paymentButton').hide();
        }
    }
    confirmSend = () => {
        Swal.fire({
            title: 'Confirmar envio?'
            , text: "Um email vai ser enviado e nao podera reverter o processo!"
            , icon: 'warning'
            , showCancelButton: true
            , confirmButtonColor: '#3085d6'
            , cancelButtonColor: '#d33'
            , confirmButtonText: 'Sim, confirmar envio!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.LoadingOverlay('show');
                var activityLaunches = [];
                $('.checkboxes:checked').each(function() {
                    activityLaunches.push($(this).val());
                });
                var form = new FormData();
                form.append("activityLaunches", JSON.stringify(activityLaunches));
                var settings = {
                    "url": "/admin/payouts-to-drivers/confirm-send"
                    , "method": "POST"
                    , "timeout": 0
                    , "headers": {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    }
                    , "processData": false
                    , "mimeType": "multipart/form-data"
                    , "contentType": false
                    , "data": form
                };
                $.ajax(settings).done(function(response) {
                    $.LoadingOverlay('hide');
                    ajax();
                    Swal.fire(
                        'Confirmado!',
                        'Foi enviado um email para o condutor.',
                        'success'
                    );
                });
            }
        });
    }
    pay = (id) => {
        $.LoadingOverlay('show');
        $.get('/admin/payouts-to-drivers/pay/' + id).then((resp) => {
            $.LoadingOverlay('hide');
            $('#pay-' + id).hide();
            if (sendDataTable) {
                sendDataTable.ajax.reload(null, false);
            }
        });
    }
    selectAllToSend = () => {
        $('input[type="checkbox"]').prop('checked', true);
    }

</script>

@endsection


