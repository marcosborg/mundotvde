@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.tvdeDriverManagement.title') }}
                </div>
                <div class="panel-body" id="tvdeDriverManagement"></div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="launchActivityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <form action="/admin/tvde-driver-managements/create-activity" id="createActivity" method="post">
                @csrf
                <input type="hidden" name="driver_id">
                <input type="hidden" name="week_id">
                <div class="collapse" id="activityData">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label>Aluguer</label>
                                            <input type="text" class="form-control" name="rent" value="0">
                                        </div>
                                        <div class="form-group">
                                            <label>Gestão</label>
                                            <input type="text" class="form-control" name="management" value="0">
                                        </div>
                                        <div class="form-group">
                                            <label>Seguro</label>
                                            <input type="text" class="form-control" name="insurance" value="0">
                                        </div>
                                        <div class="form-group">
                                            <label>Combustivel</label>
                                            <input type="text" class="form-control" name="fuel" value="0">
                                        </div>
                                        <div class="form-group">
                                            <label>Portagens</label>
                                            <input type="text" class="form-control" name="tolls" value="0">
                                        </div>
                                        <div class="form-group">
                                            <label>Oficina</label>
                                            <input type="text" class="form-control" name="garage" value="0">
                                        </div>
                                        <div class="form-group">
                                            <label>Caução</label>
                                            <input type="text" class="form-control" name="management_fee" value="25">
                                        </div>
                                        <div class="form-group">
                                            <label>Débitos</label>
                                            <input type="text" class="form-control" name="others" value="0">
                                        </div>
                                        <div class="form-group">
                                            <label>Créditos</label>
                                            <input type="text" class="form-control" name="refund" value="0">
                                        </div>
                                        <div class="form-group">
                                            <label>Quilómetros iniciais</label>
                                            <input type="number" class="form-control" name="initial_kilometers"
                                                value="">
                                        </div>
                                        <div class="form-group">
                                            <label>Quilómetros finais</label>
                                            <input type="number" class="form-control" name="final_kilometers" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 update"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Gravar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="updateActivityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <form action="/admin/tvde-driver-managements/update-activity" id="updateActivity" method="post">
                @csrf
                <input type="hidden" name="activity_launch_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label>Aluguer</label>
                                        <input type="text" class="form-control" name="rent">
                                    </div>
                                    <div class="form-group">
                                        <label>Gestão</label>
                                        <input type="text" class="form-control" name="management">
                                    </div>
                                    <div class="form-group">
                                        <label>Seguro</label>
                                        <input type="text" class="form-control" name="insurance">
                                    </div>
                                    <div class="form-group">
                                        <label>Combustivel</label>
                                        <input type="text" class="form-control" name="fuel">
                                    </div>
                                    <div class="form-group">
                                        <label>Portagens</label>
                                        <input type="text" class="form-control" name="tolls">
                                    </div>
                                    <div class="form-group">
                                        <label>Oficina</label>
                                        <input type="text" class="form-control" name="garage">
                                    </div>
                                    <div class="form-group">
                                        <label>Caução</label>
                                        <input type="text" class="form-control" name="management_fee">
                                    </div>
                                    <div class="form-group">
                                        <label>Débitos</label>
                                        <input type="text" class="form-control" name="others">
                                    </div>
                                    <div class="form-group">
                                        <label>Créditos</label>
                                        <input type="text" class="form-control" name="refund">
                                    </div>
                                    <div class="form-group">
                                        <label>Quilómetros iniciais</label>
                                        <input type="number" class="form-control" name="initial_kilometers" value="">
                                    </div>
                                    <div class="form-group">
                                        <label>Quilómetros finais</label>
                                        <input type="number" class="form-control" name="final_kilometers" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 update">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Gravar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
    integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/assets/admin/vendor/table2csv/table2csv.min.js"></script>
<script>
    $(() => {
        ajax();
        $('#createActivity').ajaxForm({
            beforeSubmit: () => {
                $.LoadingOverlay('show');
            },
            success: () => {
                $('#launchActivityModal').modal('hide');
                $.LoadingOverlay('hide');
                Swal.fire(
                    'Criado!',
                    'Pode continuar!',
                    'success'
                );
                $('#activityData input[name=rent]').val(0);
                $('#activityData input[name=management]').val(0);
                $('#activityData input[name=insurance]').val(0);
                $('#activityData input[name=fuel]').val(0);
                $('#activityData input[name=tolls]').val(0);
                $('#activityData input[name=garage]').val(0);
                $('#activityData input[name=management_fee]').val(25);
                $('#activityData input[name=others]').val(0);
                $('#activityData input[name=refund]').val(0);
                $('#activityData input[name=initial_kilometers]').val('');
                $('#activityData input[name=final_kilometers]').val('');
                ajax();
            }
        });
        $('#updateActivity').ajaxForm({
            beforeSubmit: () => {
                $.LoadingOverlay('show');
            },
            success: () => {
                $('#updateActivityModal').modal('hide');
                $.LoadingOverlay('hide');
                Swal.fire(
                    'Atualizado!',
                    'Pode continuar!',
                    'success'
                );
                ajax();
            }
        });

    });

    ajax = () => {
        $.LoadingOverlay('show');
        $.get('/admin/tvde-driver-managements/ajax').then((resp) => {
            $.LoadingOverlay('hide');
            $('#tvdeDriverManagement').html(resp);
            $('.select2').select2();
            $('.driver_form').ajaxForm({
                beforeSubmit: () => {
                    $.LoadingOverlay('show');
                },
                success: (resp) => {
                    $.LoadingOverlay('hide');
                    if(resp){
                        
                        $('#launchActivityModal').modal('show');
                        $('#launchActivityModal input[name=week_id]').val(resp.week_id);
                        $('#launchActivityModal input[name=driver_id]').val(resp.driver.id);
                        if (resp.driver.card){
                            $('#launchActivityModal .modal-title').text(resp.driver.name + ' - ' + resp.driver.card.code);
                        } else {
                            $('#launchActivityModal .modal-title').text(resp.driver.name);
                        }
                        let html = '';
                        resp.driver.tvde_operators.forEach(tvde_operator => {
                            let earnings_one = 0;
                            let earnings_two = 0;
                            let earnings_three = 0;
                            switch (tvde_operator.id) {
                                case 1:
                                    earnings_one = resp.uber_activities.earnings_one;
                                    earnings_two = resp.uber_activities.earnings_two;
                                    earnings_three = resp.uber_activities.earnings_three;
                                    break;
                                case 2:
                                    earnings_one = resp.bolt_activities.earnings_one;
                                    earnings_two = resp.bolt_activities.earnings_two;
                                    earnings_three = resp.bolt_activities.earnings_three;
                                    break;
                                default:
                                    earnings_one = 0;
                                    earnings_two = 0;
                                    earnings_three = 0;
                                    break;
                            }

                            html += '<div class="panel panel-default">';
                            html += '<div class="panel-heading">';
                            html += tvde_operator.name;
                            html += '</div>';
                            html += '<div class="panel-body">';
                            html += '<div class="form-group">';
                            html += '<label>Bruto</label>';
                            html += '<input type="text" name="create-' + tvde_operator.id + '-gross" class="form-control" value="' + earnings_one + '">';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label>Líquido</label>';
                            html += '<input type="text" name="create-' + tvde_operator.id + '-net" class="form-control" value="' + earnings_two + '">';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label>Impostos</label>';
                            html += '<input type="text" name="create-' + tvde_operator.id + '-taxes" class="form-control" value="' + earnings_three + '">';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                        });
                        $('#launchActivityModal .update').html(html);
                        if($('#activityData').hasClass('in') != true) {
                            $('#activityData').collapse('toggle');
                        }
                    } else {
                        console.log('nope');
                    }
                },
                error: (err) => {
                    $.LoadingOverlay('hide');
                    console.log(err);
                }
            });
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $('.select2').select2();
            });
        });
    }

    showActivityLaunch = (activity_launch_id) => {
        $.LoadingOverlay('show');
        $.get('tvde-driver-managements/activity-launch/' + activity_launch_id).then((resp) => {
            $.LoadingOverlay('hide');
            let activityLaunch = resp;
            if (activityLaunch.driver.card){
                $('#updateActivityModal .modal-title').text(activityLaunch.driver.name + ' - ' + activityLaunch.driver.card.code);
            } else {
                $('#updateActivityModal .modal-title').text(activityLaunch.driver.name);
            }
            $('#updateActivityModal input[name=rent]').val(activityLaunch.rent);
            $('#updateActivityModal input[name=management]').val(activityLaunch.management);
            $('#updateActivityModal input[name=insurance]').val(activityLaunch.insurance);
            $('#updateActivityModal input[name=fuel]').val(activityLaunch.fuel);
            $('#updateActivityModal input[name=tolls]').val(activityLaunch.tolls);
            $('#updateActivityModal input[name=garage]').val(activityLaunch.garage);
            $('#updateActivityModal input[name=management_fee]').val(activityLaunch.management_fee);
            $('#updateActivityModal input[name=others]').val(activityLaunch.others);
            $('#updateActivityModal input[name=refund]').val(activityLaunch.refund);
            $('#updateActivityModal input[name=initial_kilometers]').val(activityLaunch.initial_kilometers);
            $('#updateActivityModal input[name=final_kilometers]').val(activityLaunch.final_kilometers);
            $('#updateActivityModal input[name=activity_launch_id').val(activityLaunch.id);
            let html = '';
            activityLaunch.activity_per_operators.forEach(activityPerOperator => {
                html += '<div class="panel panel-default">';
                html += '<div class="panel-heading">';
                html += activityPerOperator.tvde_operator.name;
                html += '</div>';
                html += '<div class="panel-body">';
                html += '<div class="form-group">';
                html += '<label>Bruto</label>';
                html += '<input type="text" name="update-' + activityPerOperator.id + '-gross" class="form-control" value="' + activityPerOperator.gross + '">';
                html += '</div>';
                html += '<div class="form-group">';
                html += '<label>Líquido</label>';
                html += '<input type="text" name="update-' + activityPerOperator.id + '-net" class="form-control" value="' + activityPerOperator.net + '">';
                html += '</div>';
                html += '<div class="form-group">';
                html += '<label>Impostos</label>';
                html += '<input type="text" name="update-' + activityPerOperator.id + '-taxes" class="form-control" value="' + activityPerOperator.taxes + '">';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            });
            $('#updateActivityModal .update').html(html);
            $('#updateActivityModal').modal('show');
        });
    }

    deleteActivityLaunch = (activity_louch_id) => {
        Swal.fire({
            title: 'Tem a certeza??',
            text: "Não poderá reverter esta ação!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, quero apagar!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.get('/admin/tvde-driver-managements/delete-activity-launch/' + activity_louch_id).then(() => {
                    ajax();
                });
            }
        })
    };

    exportCsv = (table_id) => {
        console.log(table_id);
        $('#' + table_id).table2csv();
    };
    
</script>
@endsection