@extends('layouts.admin')
@section('content')
<div class="content">
  @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
  <div class="panel panel-default"><div class="panel-heading">Editar agendamento #{{ $inspectionSchedule->id }}</div><div class="panel-body">
    <form method="POST" action="{{ route('admin.inspection-schedules.update', $inspectionSchedule->id) }}">@csrf @method('PUT')
      @include('admin.inspectionSchedules.partials.form')
      <button class="btn btn-danger" type="submit">Atualizar</button>
    </form>
  </div></div>
</div>
@endsection
