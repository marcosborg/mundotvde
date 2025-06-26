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
    $(() => {
        ajax();
    });
    ajax = () => {
        $.LoadingOverlay('show');
        $.get('/admin/payouts-to-drivers/ajax').then((resp) => {
            $.LoadingOverlay('hide');
            $('#payoutsToDrivers').html(resp);
            $('.checkboxes').change(function() {
                checkChecked();
            });
            $('.datatable').DataTable({
                columnDefs: [{
                    targets: 0, // índice da coluna de checkboxes
                    visible: false // oculta a coluna
                }]
                , order: [
                    [3, 'desc']
                ]
            });
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                $('.datatable').DataTable().draw();
            });
            $('.select-checkbox:before').css('display', 'none');
        });
    }
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
            , text: "Um email vai ser enviado e não poderá reverter o processo!"
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
        });
    }
    selectAllToSend = () => {
        $('input[type="checkbox"]').prop('checked', true);
    }

</script>

@endsection
