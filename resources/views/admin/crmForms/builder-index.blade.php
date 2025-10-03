@extends('layouts.admin')

@section('content')
<div class="content">
  <div class="panel panel-default">
    <div class="panel-heading">
      <div style="display:flex;justify-content:space-between;align-items:center;gap:10px">
        <span>Form Builder — Hub</span>
        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#newFormModal">
          <i class="fa fa-plus"></i> Novo formulário
        </button>
      </div>
    </div>

    <div class="panel-body">
      @if($forms->isEmpty())
        <div class="alert alert-info">Ainda não existem formulários. Cria o primeiro.</div>
      @else
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Slug</th>
                <th>Estado</th>
                <th>Campos</th>
                <th>Submissões</th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <tbody>
              @foreach($forms as $f)
                <tr>
                  <td>{{ $f->id }}</td>
                  <td>{{ $f->name }}</td>
                  <td>{{ $f->category->name ?? '—' }}</td>
                  <td><code>{{ $f->slug }}</code></td>
                  <td>
                    <span class="label label-{{ $f->status === 'published' ? 'success' : ($f->status === 'archived' ? 'default' : 'warning') }}">
                      {{ ucfirst($f->status) }}
                    </span>
                  </td>
                  <td>{{ $f->fields_count }}</td>
                  <td>{{ $f->submissions_count }}</td>
                  <td style="white-space:nowrap">
                    <a class="btn btn-primary btn-xs" href="{{ route('admin.crm-forms.builder', $f) }}">
                      <i class="fa fa-wrench"></i> Abrir builder
                    </a>
                    <button class="btn btn-default btn-xs" data-toggle="collapse" data-target="#embed-{{ $f->id }}">
                      <i class="fa fa-code"></i> Embed
                    </button>
                  </td>
                </tr>
                <tr id="embed-{{ $f->id }}" class="collapse">
                  <td colspan="8">
                    <strong>Incorporação no site:</strong>
<pre style="margin-top:8px">@{{ 'render:crm-form slug="'.$f->slug.'"' }}</pre>
                    <small>Dica: podes varrer o conteúdo do site por este token e renderizar o formulário.</small>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
</div>

{{-- Modal: novo formulário --}}
<div class="modal fade" id="newFormModal" tabindex="-1" role="dialog">
  <div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="{{ route('admin.crm-forms.builder.store') }}">
      @csrf
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        <h4 class="modal-title">Novo formulário</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Nome</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Categoria (Kanban)</label>
          <select name="category_id" class="form-control">
            <option value="">—</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <p class="help-block">Será criado em rascunho e aberto no Builder.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Criar</button>
      </div>
    </form>
  </div></div>
</div>
@endsection
