{{-- resources/views/admin/crmKanban/hub.blade.php --}}
@extends('layouts.admin')

@section('styles')
@parent
<style>
  .kanban-hub-toolbar{display:flex;gap:8px;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap}
  .hub-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:12px}
  .hub-card{background:#fff;border:1px solid #eef2f7;border-radius:14px;padding:14px;box-shadow:0 4px 14px rgba(17,24,39,.06)}
  .hub-title{font-weight:700;margin:0 0 6px}
  .hub-meta{color:#6b7280;font-size:12px;margin-bottom:10px}
  .hub-actions{display:flex;gap:6px;justify-content:flex-end;margin-bottom:8px}

  /* Lista de estados no cartão */
  .stage-list{list-style:none;margin:0;padding:0;border:1px dashed #e5e7eb;border-radius:12px;padding:6px;max-height:220px;overflow:auto}
  .stage-item{display:flex;align-items:center;gap:8px;padding:6px 8px;border:1px solid #eef2f7;border-radius:10px;background:#fafafa;margin:6px 0}
  .stage-item .handle{cursor:grab;color:#9ca3af}
  .stage-name{font-weight:600}
  .stage-dot{width:10px;height:10px;border-radius:999px;background:#e5e7eb;border:1px solid #d1d5db}
  .stage-chip{font-size:11px;border:1px solid #e5e7eb;border-radius:999px;padding:2px 6px;background:#f9fafb}
  .stage-actions{margin-left:auto;display:flex;gap:6px}
</style>
@endsection

@section('content')
<div class="content">
  <div class="panel panel-default">
    <div class="panel-heading">CRM — Kanban</div>
    <div class="panel-body">
      <div class="kanban-hub-toolbar">
        <div>
          @can('crm_category_create')
          <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#createCategoryModal">
            + Nova categoria
          </button>
          @endcan
        </div>
        <input id="hubSearch" class="form-control input-sm" style="max-width:320px" placeholder="Procurar categoria…">
      </div>

      <div id="hubGrid" class="hub-grid">
        @forelse($categories as $cat)
          <div class="hub-card" data-name="{{ strtolower($cat->name) }}">
            <div class="hub-title">{{ $cat->name }}</div>
            <div class="hub-meta">
              {{ $cat->stages_count }} estados • {{ $cat->open_cards_count }} cards abertos
            </div>

            <div class="hub-actions">
              <a class="btn btn-primary btn-xs" href="{{ route('admin.crm-kanban.index', $cat->id) }}">
                Abrir board
              </a>
              @can('crm_stage_create')
              <button class="btn btn-default btn-xs"
                      data-toggle="modal" data-target="#createStageModal"
                      data-category-id="{{ $cat->id }}"
                      data-category-name="{{ $cat->name }}">
                + Estado
              </button>
              @endcan
            </div>

            {{-- Lista de estados editável --}}
            <ul class="stage-list" data-category="{{ $cat->id }}">
              @foreach(($cat->stages ?? collect()) as $st)
                <li class="stage-item" data-stage="{{ $st->id }}">
                  <i class="fa fa-bars handle" title="Arrastar para ordenar"></i>
                  <span class="stage-dot" style="background: {{ $st->color ?: '#e5e7eb' }};"></span>
                  <span class="stage-name">{{ $st->name }}</span>
                  @if($st->is_won)<span class="stage-chip">Ganho</span>@endif
                  @if($st->is_lost)<span class="stage-chip">Perdido</span>@endif
                  <div class="stage-actions">
                    @can('crm_stage_edit')
                    <button class="btn btn-default btn-xs btn-edit-stage"
                            data-stage-id="{{ $st->id }}"
                            data-stage-name="{{ $st->name }}"
                            data-stage-color="{{ $st->color }}"
                            data-stage-won="{{ $st->is_won ? 1 : 0 }}"
                            data-stage-lost="{{ $st->is_lost ? 1 : 0 }}">
                      <i class="fa fa-pencil"></i>
                    </button>
                    @endcan
                    @can('crm_stage_delete')
                    <button class="btn btn-danger btn-xs btn-del-stage" data-stage-id="{{ $st->id }}">
                      <i class="fa fa-trash"></i>
                    </button>
                    @endcan
                  </div>
                </li>
              @endforeach
            </ul>
          </div>
        @empty
          <div class="alert alert-info">Ainda não tens categorias. Cria a primeira 👇</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection

{{-- Modal: Criar Categoria --}}
<div class="modal fade" id="createCategoryModal" tabindex="-1" role="dialog" aria-labelledby="createCategoryLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="createCategoryForm" autocomplete="off">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          <h4 class="modal-title" id="createCategoryLabel">Nova categoria</h4>
        </div>
        <div class="modal-body">
          <div id="createCategoryErrors" class="alert alert-danger" style="display:none"></div>

          <div class="form-group">
            <label>Nome <span style="color:#e11d48">*</span></label>
            <input type="text" name="name" class="form-control" required placeholder="ex.: Leads TVDE">
          </div>

          <div class="form-group">
            <label>Descrição</label>
            <input type="text" name="description" class="form-control" placeholder="opcional">
          </div>

          <div class="row">
            <div class="col-xs-6">
              <div class="form-group">
                <label>Cor (opcional)</label>
                <input type="text" name="color" class="form-control" placeholder="#4f46e5">
              </div>
            </div>
            <div class="col-xs-6">
              <div class="checkbox" style="margin-top:26px">
                <label>
                  <input type="checkbox" name="with_defaults" value="1" checked> Criar estados por omissão
                </label>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">
            <span class="txt">Criar e abrir</span>
            <span class="spinner" style="display:none"><i class="fa fa-spinner fa-spin"></i></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal: Criar Estado --}}
<div class="modal fade" id="createStageModal" tabindex="-1" role="dialog" aria-labelledby="createStageLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="createStageForm" autocomplete="off">
        <input type="hidden" name="category_id">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          <h4 class="modal-title" id="createStageLabel">Novo estado</h4>
        </div>
        <div class="modal-body">
          <div id="createStageErrors" class="alert alert-danger" style="display:none"></div>

          <div class="form-group">
            <label>Categoria</label>
            <input type="text" class="form-control" name="category_name" readonly>
          </div>

          <div class="form-group">
            <label>Nome do estado <span style="color:#e11d48">*</span></label>
            <input type="text" name="name" class="form-control" required placeholder="ex.: Contactado">
          </div>

          <div class="row">
            <div class="col-xs-6">
              <div class="form-group">
                <label>Cor</label>
                <input type="text" name="color" class="form-control" placeholder="#3B82F6">
              </div>
            </div>
            <div class="col-xs-6">
              <div class="checkbox" style="margin-top:26px">
                <label><input type="checkbox" name="is_won" value="1"> Marcado como ganho</label>
              </div>
              <div class="checkbox">
                <label><input type="checkbox" name="is_lost" value="1"> Marcado como perdido</label>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
          <button type="submit" class="btn btn-primary">
            <span class="txt">Criar</span>
            <span class="spinner" style="display:none"><i class="fa fa-spinner fa-spin"></i></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal: Editar Estado --}}
<div class="modal fade" id="editStageModal" tabindex="-1" role="dialog" aria-labelledby="editStageLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="editStageForm" autocomplete="off">
        <input type="hidden" name="stage_id">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          <h4 class="modal-title" id="editStageLabel">Editar estado</h4>
        </div>
        <div class="modal-body">
          <div id="editStageErrors" class="alert alert-danger" style="display:none"></div>

          <div class="form-group">
            <label>Nome <span style="color:#e11d48">*</span></label>
            <input type="text" name="name" class="form-control" required>
          </div>

          <div class="row">
            <div class="col-xs-6">
              <div class="form-group">
                <label>Cor</label>
                <input type="text" name="color" class="form-control" placeholder="#3B82F6">
              </div>
            </div>
            <div class="col-xs-6">
              <div class="checkbox" style="margin-top:26px">
                <label><input type="checkbox" name="is_won" value="1"> Marcado como ganho</label>
              </div>
              <div class="checkbox">
                <label><input type="checkbox" name="is_lost" value="1"> Marcado como perdido</label>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">
            <span class="txt">Guardar</span>
            <span class="spinner" style="display:none"><i class="fa fa-spinner fa-spin"></i></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@section('scripts')
@parent
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
  // Pesquisa no hub (por nome de categoria)
  document.getElementById('hubSearch')?.addEventListener('input', function(){
    const q = this.value.trim().toLowerCase();
    document.querySelectorAll('#hubGrid .hub-card').forEach(function(card){
      const name = card.dataset.name || '';
      card.style.display = (!q || name.indexOf(q) !== -1) ? '' : 'none';
    });
  });

  // Modal criar estado: preencher categoria alvo
  $('#createStageModal').on('show.bs.modal', function (e) {
    const btn = $(e.relatedTarget);
    const catId = btn.data('category-id');
    const catName = btn.data('category-name');
    const $f = $('#createStageForm');
    $f[0].reset();
    $('#createStageErrors').hide().empty();
    $f.find('[name="category_id"]').val(catId);
    $f.find('[name="category_name"]').val(catName);
  });

  // Criar categoria
  $('#createCategoryForm').on('submit', function(e){
    e.preventDefault();
    const $btn = $(this).find('button[type="submit"]');
    const $err = $('#createCategoryErrors');
    $err.hide().empty();
    $btn.prop('disabled', true); $btn.find('.txt').hide(); $btn.find('.spinner').show();

    fetch('{{ route('admin.crm-kanban.category.store') }}', {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
      body: new FormData(this)
    })
    .then(async r => {
      if(!r.ok){
        const data = await r.json().catch(()=>({}));
        const msg = data.errors ? Object.values(data.errors).map(a=>a.join('<br>')).join('<br>') : 'Erro ao criar.';
        throw new Error(msg);
      }
      return r.json();
    })
    .then(resp => {
      if(!resp.ok) throw new Error('Falhou a criação.');
      // Abre imediatamente a board da nova categoria
      const url = '{{ route('admin.crm-kanban.index', ['category' => '___ID___']) }}'.replace('___ID___', resp.category.id);
      window.location = url;
    })
    .catch(err => { $err.html(err.message).show(); })
    .finally(()=> { $btn.prop('disabled', false); $btn.find('.spinner').hide(); $btn.find('.txt').show(); });
  });

  // Criar estado (adiciona linha na lista da categoria)
  $('#createStageForm').on('submit', function(e){
    e.preventDefault();
    const $btn = $(this).find('button[type="submit"]');
    const $err = $('#createStageErrors');
    $err.hide().empty();
    $btn.prop('disabled', true); $btn.find('.txt').hide(); $btn.find('.spinner').show();

    const catId = $(this).find('[name="category_id"]').val();
    const fd = new FormData(this);

    const url = '{{ route('admin.crm-kanban.stage.store', ['category' => '___ID___']) }}'.replace('___ID___', catId);

    fetch(url, {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
      body: fd
    })
    .then(async r => {
      if(!r.ok){
        const data = await r.json().catch(()=>({}));
        const msg = data.errors ? Object.values(data.errors).map(a=>a.join('<br>')).join('<br>') : 'Erro ao criar.';
        throw new Error(msg);
      }
      return r.json();
    })
    .then(resp => {
      if(!resp.ok) throw new Error('Falhou a criação.');
      const st = resp.stage;
      const ul = document.querySelector(`.stage-list[data-category="${catId}"]`);
      if (ul) {
        const li = document.createElement('li');
        li.className = 'stage-item';
        li.dataset.stage = st.id;
        li.innerHTML = `
          <i class="fa fa-bars handle" title="Arrastar para ordenar"></i>
          <span class="stage-dot" style="background: ${st.color || '#e5e7eb'};"></span>
          <span class="stage-name">${st.name}</span>
          ${st.is_won ? '<span class="stage-chip">Ganho</span>' : ''}
          ${st.is_lost ? '<span class="stage-chip">Perdido</span>' : ''}
          <div class="stage-actions">
            <button class="btn btn-default btn-xs btn-edit-stage"
                    data-stage-id="${st.id}"
                    data-stage-name="${st.name}"
                    data-stage-color="${st.color || ''}"
                    data-stage-won="${st.is_won ? 1 : 0}"
                    data-stage-lost="${st.is_lost ? 1 : 0}">
              <i class="fa fa-pencil"></i>
            </button>
            <button class="btn btn-danger btn-xs btn-del-stage" data-stage-id="${st.id}">
              <i class="fa fa-trash"></i>
            </button>
          </div>`;
        ul.appendChild(li);
      }
      $('#createStageModal').modal('hide');
    })
    .catch(err => { $err.html(err.message).show(); })
    .finally(()=> { $btn.prop('disabled', false); $btn.find('.spinner').hide(); $btn.find('.txt').show(); });
  });

  // Abrir modal de editar estado
  $(document).on('click', '.btn-edit-stage', function(){
    const btn = $(this);
    const $f = $('#editStageForm');
    $('#editStageErrors').hide().empty();
    $f[0].reset();
    $f.find('[name="stage_id"]').val(btn.data('stage-id'));
    $f.find('[name="name"]').val(btn.data('stage-name'));
    $f.find('[name="color"]').val(btn.data('stage-color') || '');
    $f.find('[name="is_won"]').prop('checked', btn.data('stage-won') == 1);
    $f.find('[name="is_lost"]').prop('checked', btn.data('stage-lost') == 1);
    $('#editStageModal').modal('show');
  });

  // Guardar edição de estado (PATCH correto!)
  $('#editStageForm').on('submit', function(e){
    e.preventDefault();
    const $btn = $(this).find('button[type="submit"]');
    const $err = $('#editStageErrors');
    $err.hide().empty();
    $btn.prop('disabled', true); $btn.find('.txt').hide(); $btn.find('.spinner').show();

    const id = $(this).find('[name="stage_id"]').val();
    const fd = new FormData(this);

    const url = '{{ route('admin.crm-kanban.stage.update', ['stage' => '___ID___']) }}'.replace('___ID___', id);

    fetch(url, {
      method: 'PATCH',
      headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
      body: fd
    })
    .then(async r => {
      if(!r.ok){
        const data = await r.json().catch(()=>({}));
        const msg = data.errors ? Object.values(data.errors).map(a=>a.join('<br>')).join('<br>') : (data.message || 'Erro ao gravar.');
        throw new Error(msg);
      }
      return r.json();
    })
    .then(resp => {
      if(!resp.ok) throw new Error('Falhou a atualização.');
      const st = resp.stage;
      const li = document.querySelector(`.stage-item[data-stage="${st.id}"]`);
      if (li) {
        li.querySelector('.stage-dot').style.background = st.color || '#e5e7eb';
        li.querySelector('.stage-name').textContent = st.name;

        // Recriar chips
        li.querySelectorAll('.stage-chip').forEach(n => n.remove());
        if (st.is_won) {
          const chip = document.createElement('span');
          chip.className = 'stage-chip';
          chip.textContent = 'Ganho';
          li.insertBefore(chip, li.querySelector('.stage-actions'));
        }
        if (st.is_lost) {
          const chip = document.createElement('span');
          chip.className = 'stage-chip';
          chip.textContent = 'Perdido';
          li.insertBefore(chip, li.querySelector('.stage-actions'));
        }

        // Atualizar data-* do botão editar
        const editBtn = li.querySelector('.btn-edit-stage');
        editBtn.dataset.stageName = st.name;
        editBtn.dataset.stageColor = st.color || '';
        editBtn.dataset.stageWon = st.is_won ? 1 : 0;
        editBtn.dataset.stageLost = st.is_lost ? 1 : 0;
      }
      $('#editStageModal').modal('hide');
    })
    .catch(err => { $err.html(err.message).show(); })
    .finally(()=> { $btn.prop('disabled', false); $btn.find('.spinner').hide(); $btn.find('.txt').show(); });
  });

  // Apagar estado
  $(document).on('click', '.btn-del-stage', function(){
    if (!confirm('Apagar este estado? (cards abertos impedem a remoção)')) return;
    const id = $(this).data('stage-id');

    const url = '{{ route('admin.crm-kanban.stage.destroy', ['stage' => '___ID___']) }}'.replace('___ID___', id);

    fetch(url, {
      method: 'DELETE',
      headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
    })
    .then(async r=>{
      if(!r.ok){
        const data = await r.json().catch(()=>({}));
        throw new Error(data.message || 'Erro ao apagar.');
      }
      return r.json();
    })
    .then(resp=>{
      if(!resp.ok) throw new Error('Falhou a eliminação.');
      const li = document.querySelector(`.stage-item[data-stage="${id}"]`);
      if (li) li.remove();
    })
    .catch(err=> alert(err.message || 'Erro ao apagar.'));
  });

  // Sortable para cada lista de estados (reorder PATCH correto!)
  document.querySelectorAll('.stage-list').forEach(function(ul){
    new Sortable(ul, {
      group: 'stages',
      handle: '.handle',
      animation: 150,
      onEnd: function(){
        const catId = ul.dataset.category;
        const order = Array.from(ul.querySelectorAll('.stage-item')).map(li => li.dataset.stage);

        const url = '{{ route('admin.crm-kanban.stage.reorder', ['category' => '___ID___']) }}'.replace('___ID___', catId);

        fetch(url, {
          method: 'PATCH',
          headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}','Content-Type':'application/json'},
          body: JSON.stringify({ order })
        }).catch(()=>{/* opcional: toast erro */});
      }
    });
  });
</script>
@endsection
