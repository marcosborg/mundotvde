{{-- resources/views/admin/crmKanban/index.blade.php --}}
@extends('layouts.admin')

@section('styles')
@parent
<style>
  /* Layout */
  .kanban-toolbar{display:flex;gap:10px;align-items:center;justify-content:space-between;margin-bottom:12px}
  .kanban-search{max-width:320px}
  .kanban-row{display:flex;gap:12px;align-items:flex-start;overflow-x:auto;padding-bottom:8px}
  .kanban-col-wrap{min-width:300px;flex:0 0 300px}
  .kanban-col{min-height:240px;max-height:70vh;overflow:auto;border:1px dashed #e5e7eb;border-radius:14px;padding:10px;background:#fafafa;transition:border-color .2s, background .2s}
  .kanban-col.is-drop{background:#f0f7ff;border-color:#60a5fa}
  .kanban-box{border-radius:14px;background:#fff;box-shadow:0 6px 18px rgba(17,24,39,.06);border:1px solid #eef2f7}

  /* Header da coluna */
  .kanban-head{display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-bottom:1px solid #eef2f7;border-top-left-radius:14px;border-top-right-radius:14px}
  .kanban-title{font-weight:700}
  .kanban-count{background:#eef2ff;border:1px solid #e0e7ff;border-radius:999px;padding:2px 8px;font-size:12px}

  /* Card */
  .kanban-card{position:relative;display:block;background:#fff;border:1px solid #eef2f7;border-radius:12px;padding:10px 10px 10px 14px;margin-bottom:10px;box-shadow:0 2px 8px rgba(0,0,0,.04);cursor:grab;text-decoration:none;color:inherit}
  .kanban-card:hover{box-shadow:0 6px 16px rgba(0,0,0,.08);border-color:#e5e7eb}
  .kanban-card.is-ghost{opacity:.5}
  .kanban-card.is-chosen{transform:rotate(.5deg)}
  .kanban-card::before{content:""; position:absolute; left:0; top:8px; bottom:8px; width:4px; border-radius:4px; background:#e5e7eb}
  .kanban-card[data-priority="low"]::before{ background:#10B981; }
  .kanban-card[data-priority="medium"]::before{ background:#3B82F6; }
  .kanban-card[data-priority="high"]::before{ background:#F59E0B; }

  .kc-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:4px}
  .kc-title{font-weight:600;line-height:1.3;margin-bottom:4px;word-break:break-word}
  .kc-meta{font-size:12px;color:#6b7280}

  .badge{display:inline-block;font-size:11px;padding:2px 8px;border-radius:999px;border:1px solid #e5e7eb;background:#f9fafb;font-weight:600}
  .badge.pri-low{background:#ECFDF5;border-color:#10B981;color:#065F46}
  .badge.pri-medium{background:#DBEAFE;border-color:#3B82F6;color:#1E3A8A}
  .badge.pri-high{background:#FEF3C7;border-color:#F59E0B;color:#92400E}

  .btn-add{border-radius:999px}

  /* Ações do card (lápis) */
  .kc-actions{position:absolute; right:8px; top:8px; opacity:0; transition:opacity .2s; z-index:5; pointer-events:auto;}
  .kc-actions .btn{pointer-events:auto;}
  .kanban-card:hover .kc-actions{opacity:1}

  .help-text{font-size:12px;color:#6b7280;margin-top:4px}
</style>
@endsection

@section('content')
<div class="content">
  <div class="panel panel-default kanban-box">
    <div class="panel-heading" style="border-top-left-radius:14px;border-top-right-radius:14px;">
      {{ $category->name }} — Kanban
    </div>
    <div class="panel-body">

      {{-- Toolbar topo --}}
      <div class="kanban-toolbar">
        <div>
          <button type="button"
                  class="btn btn-success btn-sm btn-add"
                  data-toggle="modal"
                  data-target="#createCardModal"
                  data-stage="">
            + Novo card
          </button>
        </div>
        <input id="kanbanSearch" class="form-control input-sm kanban-search" placeholder="Procurar por título…">
      </div>

      {{-- Colunas --}}
      <div class="kanban-row" id="kanban" data-category="{{ $category->id }}">
        @foreach($category->stages as $stage)
          @php $cards = $stage->cards ?? collect(); @endphp
          <div class="kanban-col-wrap">
            <div class="kanban-box">
              <div class="kanban-head">
                <div class="kanban-title">{{ $stage->name }}</div>
                <div style="display:flex;align-items:center;gap:6px">
                  <div class="kanban-count">{{ $cards->count() }}</div>
                  <button type="button" class="btn btn-default btn-xs"
                          title="Novo em {{ $stage->name }}"
                          data-toggle="modal"
                          data-target="#createCardModal"
                          data-stage="{{ $stage->id }}">
                    <i class="fa fa-plus"></i>
                  </button>
                </div>
              </div>

              <div class="kanban-col" data-stage="{{ $stage->id }}">
                {{-- placeholder (mostra quando vazio) --}}
                <div class="kanban-empty" style="{{ ($cards->count() ? 'display:none' : '') }}">
                  <div class="kc-meta" style="padding:6px 4px;color:#9ca3af;">Sem cards aqui...</div>
                </div>

                @foreach($cards as $card)
                  <a class="kanban-card"
                     href="{{ route('admin.crm-cards.show', $card->id) }}"
                     target="_blank"
                     data-card="{{ $card->id }}"
                     data-pos="{{ $card->position }}"
                     data-title="{{ strtolower($card->title) }}"
                     data-priority="{{ $card->priority ?? 'medium' }}"
                     data-due_at="{{ $card->due_at_html }}"
                     data-stage_id="{{ $card->stage_id }}">
                    <div class="kc-actions">
                      <button type="button" class="btn btn-xs btn-default btn-edit-card" data-id="{{ $card->id }}">
                        <i class="fa fa-pencil"></i>
                      </button>
                    </div>
                    <div class="kc-top">
                      <span class="badge {{ $card->priority === 'high' ? 'pri-high' : ($card->priority === 'low' ? 'pri-low' : 'pri-medium') }}">
                        {{ ucfirst($card->priority ?? 'medium') }}
                      </span>
                    </div>
                    <div class="kc-title">{{ $card->title }}</div>
                    <div class="kc-meta">#{{ $card->id }}</div>
                  </a>
                @endforeach
              </div>
            </div>
          </div>
        @endforeach
      </div>

    </div>
  </div>
</div>
@endsection

{{-- Modal: Criar --}}
@php $firstStageId = optional($category->stages->sortBy('position')->first())->id; @endphp
<div class="modal fade" id="createCardModal" tabindex="-1" role="dialog" aria-labelledby="createCardModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="createCardForm" autocomplete="off">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          <h4 class="modal-title" id="createCardModalLabel">Novo card</h4>
        </div>
        <div class="modal-body">
          <div id="createCardErrors" class="alert alert-danger" style="display:none;margin-bottom:10px;"></div>

          <input type="hidden" name="category_id" value="{{ $category->id }}">

          <div class="form-group">
            <label>Título <span style="color:#e11d48">*</span></label>
            <input type="text" name="title" class="form-control" placeholder="ex.: Pedido de contacto — João Silva" required>
          </div>

          <div class="form-group">
            <label>Estádio</label>
            <select name="stage_id" class="form-control">
              @foreach($category->stages->sortBy('position') as $st)
                <option value="{{ $st->id }}" {{ $st->id == $firstStageId ? 'selected' : '' }}>{{ $st->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="row">
            <div class="col-xs-6">
              <div class="form-group">
                <label>Prioridade</label>
                <select name="priority" class="form-control">
                  <option value="low">Low</option>
                  <option value="medium" selected>Medium</option>
                  <option value="high">High</option>
                </select>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group">
                <label>Vence em</label>
                <input type="date" name="due_at" class="form-control">
              </div>
            </div>
          </div>

          <hr style="margin:10px 0">
          <div class="row">
            <div class="col-xs-6">
              <div class="form-group">
                <label>Fonte</label>
                <select name="source" class="form-control">
                  <option value="manual" selected>Manual</option>
                  <option value="form">Form</option>
                  <option value="import">Import</option>
                  <option value="api">API</option>
                </select>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="help-text">Opcional — use “Form” se estiver a colar dados do site.</div>
            </div>
          </div>

          <div class="form-group">
            <label>Dados do formulário (JSON) <small class="text-muted">(opcional)</small></label>
            <textarea name="fields_snapshot_json" class="form-control" rows="4" placeholder='{"nome":"João","email":"..."}'></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">
            <span class="txt">Criar</span>
            <span class="spinner" style="display:none;"><i class="fa fa-spinner fa-spin"></i></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal: Editar --}}
<div class="modal fade" id="editCardModal" tabindex="-1" role="dialog" aria-labelledby="editCardModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="editCardForm" autocomplete="off">
        <input type="hidden" name="id">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          <h4 class="modal-title" id="editCardModalLabel">Editar card</h4>
        </div>
        <div class="modal-body">
          <div id="editCardErrors" class="alert alert-danger" style="display:none;margin-bottom:10px;"></div>

          <div class="form-group">
            <label>Título <span style="color:#e11d48">*</span></label>
            <input type="text" name="title" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Estádio</label>
            <select name="stage_id" class="form-control">
              @foreach($category->stages->sortBy('position') as $st)
                <option value="{{ $st->id }}">{{ $st->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="row">
            <div class="col-xs-6">
              <div class="form-group">
                <label>Prioridade</label>
                <select name="priority" class="form-control">
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                </select>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group">
                <label>Vence em</label>
                <input type="date" name="due_at" class="form-control">
              </div>
            </div>
          </div>

          <hr style="margin:10px 0">
          <div class="row">
            <div class="col-xs-6">
              <div class="form-group">
                <label>Fonte</label>
                <select name="source" class="form-control">
                  <option value="manual">Manual</option>
                  <option value="form">Form</option>
                  <option value="import">Import</option>
                  <option value="api">API</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Dados do formulário (JSON) <small class="text-muted">(opcional)</small></label>
            <textarea name="fields_snapshot_json" class="form-control" rows="4" placeholder='{"nome":"João","email":"..."}'></textarea>
            <div class="help-text">Se o card veio de um formulário, este JSON guarda um snapshot dos campos.</div>
          </div>

          <ul class="nav nav-tabs" role="tablist" style="margin-bottom:10px">
            <li class="active"><a href="#tab-details" role="tab" data-toggle="tab">Detalhes</a></li>
            <li><a href="#tab-notes" role="tab" data-toggle="tab">Notas</a></li>
            <li><a href="#tab-files" role="tab" data-toggle="tab">Anexos</a></li>
          </ul>

          <div class="tab-content">
            <!-- Detalhes (o que já tens) -->
            <div role="tabpanel" class="tab-pane active" id="tab-details">
              <!-- ... os teus inputs: título, stage_id, priority, due_at ... -->
            </div>

            <!-- Notas -->
            <div role="tabpanel" class="tab-pane" id="tab-notes">
              <div id="editNotesErrors" class="alert alert-danger" style="display:none"></div>

              <div class="form-group">
                <label>Nova nota</label>
                <textarea id="noteContent" class="form-control" rows="3" placeholder="Escreve uma nota curta…"></textarea>
                <button type="button" id="btnAddNote" class="btn btn-default" style="margin-top:6px">Adicionar nota</button>
              </div>

              <div id="notesList" class="list-group" style="max-height:240px; overflow:auto">
                <!-- notas aparecem aqui -->
              </div>
            </div>

            <!-- Anexos -->
            <div role="tabpanel" class="tab-pane" id="tab-files">
              <div id="editFilesErrors" class="alert alert-danger" style="display:none"></div>

              <div class="form-inline" style="margin-bottom:8px">
                <input type="file" id="attachFile" class="form-control" style="display:inline-block">
                <button type="button" id="btnUploadFile" class="btn btn-default">Carregar</button>
              </div>

              <ul id="filesList" class="list-unstyled" style="max-height:240px; overflow:auto">
                <!-- anexos aparecem aqui -->
              </ul>
            </div>
          </div>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">
            <span class="txt">Guardar</span>
            <span class="spinner" style="display:none;"><i class="fa fa-spinner fa-spin"></i></span>
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
  // --- Helpers ---
  function refreshCol(col){
    const count = col.querySelectorAll('.kanban-card').length;
    const empty = col.querySelector('.kanban-empty');
    if (empty) empty.style.display = count ? 'none' : 'block';
    const badge = col.closest('.kanban-box')?.querySelector('.kanban-count');
    if (badge) badge.textContent = count;
  }
  function findSiblingCardId(el, dir){
    let s = el[dir];
    while (s && !s.classList.contains('kanban-card')) s = s[dir];
    return s ? s.dataset.card : null;
  }
  function buildCardElement(c){
    const a = document.createElement('a');
    a.className = 'kanban-card';
    a.href = c.show_url || ('{{ url('admin/crm-cards') }}/'+c.id);
    a.target = '_blank';
    a.dataset.card = c.id;
    a.dataset.pos  = c.position || '';
    a.dataset.title = (c.title || '').toLowerCase();
    a.dataset.priority = c.priority || 'medium';
    a.dataset.due_at = c.due_at || '';
    a.dataset.stage_id = c.stage_id;

    a.innerHTML = `
      <div class="kc-actions">
        <button type="button" class="btn btn-xs btn-default btn-edit-card" data-id="${c.id}">
          <i class="fa fa-pencil"></i>
        </button>
      </div>
      <div class="kc-top">
        <span class="badge ${c.priority === 'high' ? 'pri-high' : (c.priority === 'low' ? 'pri-low' : 'pri-medium')}">
          ${(c.priority || 'medium').replace(/^./, s=>s.toUpperCase())}
        </span>
      </div>
      <div class="kc-title">${c.title || ''}</div>
      <div class="kc-meta">#${c.id}</div>
    `;
    return a;
  }
  function openEditFromEl(el){
    const $f = $('#editCardForm');
    $('#editCardErrors').hide().empty();

    if (el) {
      $f.find('[name="id"]').val(el.dataset.card);
      $f.find('[name="title"]').val(el.querySelector('.kc-title')?.textContent?.trim() || '');
      $f.find('[name="priority"]').val(el.dataset.priority || 'medium');
      $f.find('[name="due_at"]').val(el.dataset.due_at || '');
      const stageId = el.dataset.stage_id || el.closest('.kanban-col')?.dataset.stage;
      $f.find('[name="stage_id"]').val(String(stageId));

      // Source / JSON do formulário ficam em branco por omissão (se o backend devolver no quick-show, podes popular)
      $f.find('[name="source"]').val('manual');
      $f.find('[name="fields_snapshot_json"]').val('');

      $('#editCardModal').modal('show');
    } else {
      alert('Card não encontrado.');
    }
  }

  // Atualiza/insere card na DOM
  function upsertCard(c){
    let cardEl = document.querySelector(`.kanban-card[data-card="${c.id}"]`);
    const targetCol = document.querySelector(`.kanban-col[data-stage="${c.stage_id}"]`);

    // cria se não existir (ou move se mudou de coluna)
    if (!cardEl) {
      if (!targetCol) return;
      cardEl = buildCardElement(c);
      targetCol.appendChild(cardEl);
      refreshCol(targetCol);
    } else {
      const fromCol = cardEl.closest('.kanban-col');
      if (targetCol && fromCol !== targetCol) {
        targetCol.appendChild(cardEl);
        refreshCol(fromCol); refreshCol(targetCol);
      }
    }

    // título
    const titleEl = cardEl.querySelector('.kc-title');
    if (titleEl) titleEl.textContent = c.title || '';
    cardEl.dataset.title = (c.title || '').toLowerCase();

    // prioridade
    const priBadge = cardEl.querySelector('.badge');
    if (priBadge) {
      priBadge.classList.remove('pri-low','pri-medium','pri-high');
      priBadge.classList.add(c.priority === 'high' ? 'pri-high' : (c.priority === 'low' ? 'pri-low' : 'pri-medium'));
      priBadge.textContent = (c.priority || 'medium').replace(/^./, s=>s.toUpperCase());
    }

    // datasets
    cardEl.dataset.priority = c.priority || 'medium';
    cardEl.dataset.due_at = c.due_at || '';
    cardEl.dataset.stage_id = c.stage_id;
    if (c.position) cardEl.dataset.pos = c.position;

    // pequeno “flash”
    cardEl.style.transition = 'background-color .4s';
    cardEl.style.backgroundColor = '#f0fdf4';
    setTimeout(()=> cardEl.style.backgroundColor = '', 400);
  }

  // --- Sortable (drag & drop) ---
  document.querySelectorAll('.kanban-col').forEach(function(col){
    refreshCol(col);
    new Sortable(col, {
      group: 'crm-kanban',
      animation: 150,
      ghostClass: 'is-ghost',
      chosenClass: 'is-chosen',
      onAdd: (evt)=> { refreshCol(evt.to); },
      onRemove: (evt)=> { refreshCol(evt.from); },
      onEnd: function (evt) {
        const el = evt.item;
        const cardId = el.dataset.card;
        const stageId = el.parentElement.dataset.stage;
        const prev = findSiblingCardId(el, 'previousElementSibling');
        const next = findSiblingCardId(el, 'nextElementSibling');

        refreshCol(evt.from);
        refreshCol(evt.to);

        const moveUrl = '{{ route('admin.crm-cards.move', ['crm_card' => '___ID___']) }}'.replace('___ID___', cardId);

        fetch(moveUrl, {
          method: 'PATCH',
          headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}','Content-Type': 'application/json'},
          body: JSON.stringify({ stage_id: stageId, prev_id: prev, next_id: next })
        })
        .then(r => r.ok ? r.json() : Promise.reject())
        .then(data => {
          if (data && data.ok && data.card) {
            el.dataset.stage_id = String(data.card.stage_id);
            el.dataset.pos = String(data.card.position || '');
          }
        })
        .catch(() => { /* opcional: reverter visualmente */ });
      }
    });
  });

  // --- Pesquisa instantânea ---
  const search = document.getElementById('kanbanSearch');
  if (search) {
    search.addEventListener('input', function(){
      const q = this.value.trim().toLowerCase();
      document.querySelectorAll('.kanban-card').forEach(function(card){
        const t = (card.dataset.title || '').toLowerCase();
        card.style.display = (!q || t.indexOf(q) !== -1) ? '' : 'none';
      });
    });
  }

  // --- Modal Criar (pré-selecionar estádio) ---
  $('#createCardModal').on('show.bs.modal', function (e) {
    const btn = $(e.relatedTarget);
    const stageFromBtn = btn && btn.data('stage') ? String(btn.data('stage')) : '';
    const $form = $('#createCardForm');
    $form[0].reset();
    $('#createCardErrors').hide().empty();
    const $stageSel = $form.find('[name="stage_id"]');
    if (stageFromBtn) $stageSel.val(stageFromBtn);
    setTimeout(()=> $form.find('[name="title"]').trigger('focus'), 120);
  });

  // --- Submit Criar ---
  $('#createCardForm').on('submit', function(e){
    e.preventDefault();
    const $btn = $(this).find('button[type="submit"]');
    const $errors = $('#createCardErrors');
    $errors.hide().empty();
    $btn.prop('disabled', true); $btn.find('.txt').hide(); $btn.find('.spinner').show();

    fetch('{{ route('admin.crm-cards.quick') }}', {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
      body: new FormData(this)
    })
    .then(async r=>{
      if(!r.ok){
        const data = await r.json().catch(()=>({}));
        const msgs = data.errors ? Object.values(data.errors).map(a=>a.join('<br>')).join('<br>') : 'Erro ao criar.';
        throw new Error(msgs);
      }
      return r.json();
    })
    .then(resp=>{
      if(!resp.ok) throw new Error('Erro ao criar.');
      const c = resp.card;
      const col = document.querySelector(`.kanban-col[data-stage="${c.stage_id}"]`);
      if (col) {
        col.appendChild(buildCardElement(c));
        refreshCol(col);
      }
      $('#createCardModal').modal('hide');
    })
    .catch(err=>{ $errors.html(err.message).show(); })
    .finally(()=>{ $btn.prop('disabled', false); $btn.find('.spinner').hide(); $btn.find('.txt').show(); });
  });

  // --- Abrir Modal Editar ---
  $(document).on('click', '.btn-edit-card', function(e){
    e.preventDefault(); e.stopPropagation();
    const id = $(this).data('id');
    const el = document.querySelector(`.kanban-card[data-card="${id}"]`);
    openEditFromEl(el);
  });
  $(document).on('click', '.kanban-card', function(e){
    if (e.metaKey || e.ctrlKey || e.button === 1) return; // nova aba
    if (e.target.closest('.btn-edit-card')) return;
    e.preventDefault();
    openEditFromEl(this);
  });

  // --- Submit Editar (POST + _method=PATCH para aceitar FormData) ---
  $('#editCardForm').on('submit', function (e) {
    e.preventDefault();

    const $btn = $(this).find('button[type="submit"]');
    const $errors = $('#editCardErrors');
    $errors.hide().empty();
    $btn.prop('disabled', true); $btn.find('.txt').hide(); $btn.find('.spinner').show();

    const id  = $(this).find('[name="id"]').val();
    const url = @json(route('admin.crm-cards.quick-update', ['crm_card' => '___ID___'])).replace('___ID___', id);

    const fd = new FormData(this);
    fd.append('_method', 'PATCH');

    fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      },
      body: fd
    })
    .then(async r => {
      if (!r.ok) {
        const data = await r.json().catch(()=>({}));
        const msgs = data?.errors ? Object.values(data.errors).flat().join('<br>') : 'Erro ao gravar.';
        throw new Error(msgs);
      }
      return r.json();
    })
    .then(resp => {
      if (!resp.ok) throw new Error('Erro ao gravar.');
      upsertCard(resp.card);
      $('#editCardModal').modal('hide');
    })
    .catch(err => $errors.html(err.message).show())
    .finally(() => { $btn.prop('disabled', false); $btn.find('.spinner').hide(); $btn.find('.txt').show(); });
  });

  function loadNotes(cardId){
    const url = @json(route('admin.crm-cards.notes.index',['crm_card'=>'__ID__'])).replace('__ID__', cardId);
    fetch(url, {headers:{'Accept':'application/json'}})
      .then(r=>r.json())
      .then(data=>{
        if(!data.ok) return;
        const box = document.getElementById('notesList');
        box.innerHTML = '';
        data.notes.forEach(n=>{
          const item = document.createElement('div');
          item.className = 'list-group-item';
          item.innerHTML = `<div style="font-size:12px;color:#6b7280">${n.user_name||'—'} • ${n.created_at}</div>
                            <div>${escapeHtml(n.content)}</div>`;
          box.appendChild(item);
        });
      });
  }

  function addNote(cardId, content){
    const url = @json(route('admin.crm-cards.notes.store',['crm_card'=>'__ID__'])).replace('__ID__', cardId);
    const fd = new FormData(); fd.append('content', content);
    return fetch(url, {
      method:'POST',
      headers:{'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
      body: fd
    }).then(r=>r.json());
  }

  function loadFiles(cardId){
    const url = @json(route('admin.crm-cards.attachments.index',['crm_card'=>'__ID__'])).replace('__ID__', cardId);
    fetch(url, {headers:{'Accept':'application/json'}})
      .then(r=>r.json())
      .then(data=>{
        if(!data.ok) return;
        const ul = document.getElementById('filesList');
        ul.innerHTML = '';
        data.attachments.forEach(a=>{
          const li = document.createElement('li');
          li.dataset.mediaId = a.id;
          li.style.marginBottom = '6px';
          li.innerHTML = `
            <a href="${a.url}" target="_blank">${a.name}</a>
            <small>(${a.size})</small>
            <button type="button" class="btn btn-xs btn-danger pull-right btn-del-file">remover</button>`;
          ul.appendChild(li);
        });
      });
  }

  function uploadFile(cardId, file){
    const url = @json(route('admin.crm-cards.attachments.store',['crm_card'=>'__ID__'])).replace('__ID__', cardId);
    const fd = new FormData(); fd.append('file', file);
    return fetch(url, {
      method:'POST',
      headers:{'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
      body: fd
    }).then(r=>r.json());
  }

  function deleteFile(cardId, mediaId){
    const url = @json(route('admin.crm-cards.attachments.destroy',['crm_card'=>'__ID__','media'=>'__MID__']))
                  .replace('__ID__', cardId).replace('__MID__', mediaId);
    return fetch(url, {
      method:'DELETE',
      headers:{
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept':'application/json'
      }
    }).then(r=>r.json());
  }

  // util simples
  function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m])); }

  // quando abres o modal de edição, além de preencher os campos…
  $('#editCardModal').on('shown.bs.modal', function(){
    const id = $('#editCardForm [name="id"]').val();
    loadNotes(id);
    loadFiles(id);
  });

  // botão "Adicionar nota"
  $('#btnAddNote').on('click', function(){
    const id = $('#editCardForm [name="id"]').val();
    const $ta = $('#noteContent'); const content = $ta.val().trim();
    const $err = $('#editNotesErrors');
    if (!content) return;

    addNote(id, content)
    .then(resp=>{
      if(!resp.ok) throw new Error('Falhou ao gravar nota.');
      $('#noteContent').val('');
      loadNotes(id);
    })
    .catch(err=>{ $err.text(err.message).show(); setTimeout(()=> $err.hide().empty(), 3000); });
  });

  // upload
  $('#btnUploadFile').on('click', function(){
    const id = $('#editCardForm [name="id"]').val();
    const f = document.getElementById('attachFile').files[0];
    const $err = $('#editFilesErrors');
    if (!f) return;

    uploadFile(id, f)
      .then(resp=>{
        if(!resp.ok) throw new Error('Falhou upload.');
        document.getElementById('attachFile').value = '';
        loadFiles(id);
      })
      .catch(err=>{ $err.text(err.message).show(); setTimeout(()=> $err.hide().empty(), 3000); });
  });

  // apagar ficheiro
  $(document).on('click', '.btn-del-file', function(){
    const id = $('#editCardForm [name="id"]').val();
    const mediaId = $(this).closest('li').data('mediaId');
    const $err = $('#editFilesErrors');

    deleteFile(id, mediaId)
      .then(resp=>{
        if(!resp.ok) throw new Error('Falhou ao remover.');
        loadFiles(id);
      })
      .catch(err=>{ $err.text(err.message).show(); setTimeout(()=> $err.hide().empty(), 3000); });
  });


</script>
@endsection
