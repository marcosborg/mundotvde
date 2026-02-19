@extends('layouts.admin')
@section('content')
<div class="content">
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading">Novo template</div>
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.inspections.templates.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Nome</label>
                        <input class="form-control" name="name" required>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Executante</label>
                        <select class="form-control" name="performer_type" required>
                            <option value="driver">Motorista</option>
                            <option value="company">Empresa</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Ângulos obrigatórios</label>
                        <select class="form-control" name="required_photo_angles_json[]" multiple>
                            @foreach(['front','rear','left','right','front_left','front_right','interior','odometer','other'] as $angle)
                                <option value="{{ $angle }}" {{ in_array($angle, ['front','rear','left','right']) ? 'selected' : '' }}>{{ $angle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 form-group">
                        <label>Assinatura</label>
                        <input type="checkbox" name="requires_signature" value="1" class="form-control">
                    </div>
                    <div class="col-md-1 form-group">
                        <label>Ativo</label>
                        <input type="checkbox" name="is_active" value="1" class="form-control" checked>
                    </div>
                </div>
                <div class="form-group">
                    <label>Schema JSON (opcional)</label>
                    <textarea class="form-control" rows="5" name="schema_json" placeholder='{"cards": [{"title": "Checklist"}]}'></textarea>
                </div>
                <button class="btn btn-success" type="submit">Criar</button>
            </form>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Templates</div>
        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Executante</th>
                        <th>Ângulos</th>
                        <th>Ativo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                        <tr>
                            <td>{{ $template->id }}</td>
                            <td>{{ $template->name }}</td>
                            <td>{{ $template->performer_type }}</td>
                            <td>{{ implode(', ', $template->required_photo_angles_json ?? []) }}</td>
                            <td>{{ $template->is_active ? 'Sim' : 'Não' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.inspections.templates.destroy', $template->id) }}" style="display:inline-block" onsubmit="return confirm('Remover template?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-xs btn-danger">Apagar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $templates->links() }}
        </div>
    </div>
</div>
@endsection

