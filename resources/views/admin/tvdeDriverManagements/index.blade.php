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
                <h4 class="modal-title">Lançar atividade</h4>
            </div>
            <form action="/admin/tvde-driver-managements/create-activity" id="createActivity" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Condutor</label>
                        <select name="driver_id" class="form-control" onchange="selectDriver()"></select>
                    </div>
                </div>
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
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
    integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        });
    }
    
    launchActivity = (week_id) => {
        console.log(week_id);
        $('#launchActivityModal input[name=week_id]').val(week_id);
        $.LoadingOverlay('show');
        $.get('/admin/tvde-driver-managements/drivers').then((resp) => {
            $.LoadingOverlay('hide');
            let html = '<option selected disabled>Selecionar condutor</option>';
            let drivers = resp;
            console.log(drivers);
            $.each(drivers, (i, v) => {
                let h1 = '<option value="' + v.id + '">' + v.name;
                let h2 = '</option>';
                if(v.card) {
                    h2 = ' - Cartão ' + v.card.code + '</option>';
                }
                html += h1 + h2;
            });
            $('#launchActivityModal select[name=driver_id]').html(html);
            $('#launchActivityModal').modal('show');
        });
    }

    selectDriver = () => {
        $.LoadingOverlay('show');
        let driver_id = $('#launchActivityModal select[name=driver_id]').val();
        $('#launchActivityModal input[name=driver_id]').val(driver_id);
        $.get('/admin/tvde-driver-managements/driver/' + driver_id).then((resp) => {
            $.LoadingOverlay('hide');
            let driver = resp;
            let html = '';
            driver.tvde_operators.forEach(tvde_operator => {
                html += '<div class="panel panel-default">';
                html += '<div class="panel-heading">';
                html += tvde_operator.name;
                html += '</div>';
                html += '<div class="panel-body">';
                html += '<div class="form-group">';
                html += '<label>Bruto</label>';
                html += '<input type="text" name="create-' + tvde_operator.id + '-gross" class="form-control" value="0">';
                html += '</div>';
                html += '<div class="form-group">';
                html += '<label>Líquido</label>';
                html += '<input type="text" name="create-' + tvde_operator.id + '-net" class="form-control" value="0">';
                html += '</div>';
                html += '<div class="form-group">';
                html += '<label>Impostos</label>';
                html += '<input type="text" name="create-' + tvde_operator.id + '-taxes" class="form-control" value="0">';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            });
            $('#launchActivityModal .update').html(html);
            if($('#activityData').hasClass('in') != true) {
                $('#activityData').collapse('toggle');
            }
        });
    }

    showActivityLaunch = (activity_launch_id) => {
        $.LoadingOverlay('show');
        $.get('tvde-driver-managements/activity-launch/' + activity_launch_id).then((resp) => {
            $.LoadingOverlay('hide');
            let activityLaunch = resp;
            $('#updateActivityModal .modal-title').text(activityLaunch.driver.name);
            $('#updateActivityModal input[name=rent]').val(activityLaunch.rent);
            $('#updateActivityModal input[name=management]').val(activityLaunch.management);
            $('#updateActivityModal input[name=insurance]').val(activityLaunch.insurance);
            $('#updateActivityModal input[name=fuel]').val(activityLaunch.fuel);
            $('#updateActivityModal input[name=tolls]').val(activityLaunch.tolls);
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
    
</script>
@endsection