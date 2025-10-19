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

  /* Snapshot viewer */
  .snapshot-wrap{border:1px solid #e5e7eb;border-radius:8px;background:#fafafa;padding:8px;max-height:220px;overflow:auto}
  .snapshot-empty{color:#9ca3af;font-style:italic;padding:6px}
  .snapshot-list{list-style:none;margin:0;padding:0}
  .snapshot-item{display:flex;gap:10px;padding:6px 8px;border-bottom:1px dashed #eee}
  .snapshot-item:last-child{border-bottom:none}
  .snapshot-key{min-width:160px;max-width:45%;font-weight:600;color:#374151;word-break:break-word}
  .snapshot-val{flex:1;white-space:pre-wrap;word-break:break-word}
  .snapshot-sublist{list-style:disc;margin:0 0 0 18px;padding:0}
  .snapshot-code{font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;color:#6b7280}
  .kc-dates{font-size:12px;color:#6b7280;margin-top:4px; display:block}
  .kc-dates .kc-date-item{display:block}
  .kc-date-item + .kc-date-item{margin-top:2px}
  .kc-date-sep{display:none}


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
                    data-priority="{{ strtolower($card->priority ?? 'medium') }}"
                    data-due_at="{{ $card->due_at_html }}"
                    data-stage_id="{{ $card->stage_id }}"
                    data-source="{{ strtolower($card->source ?? 'manual') }}"
                    data-snapshot="{{ e($card->fields_snapshot_json ?? '') }}"
                    data-created_at="{{ optional($card->created_at)->format('d/m/Y H:i') }}"
                    data-updated_at="{{ optional($card->updated_at)->format('d/m/Y H:i') }}"
                  >
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

                    {{-- META: ID + datas --}}
                    <div class="kc-meta">#{{ $card->id }}</div>
                    <div class="kc-meta kc-dates">
                      <span class="kc-date-item">Criado: <strong>{{ optional($card->created_at)->format('d/m/Y H:i') }}</strong></span>
                      <span class="kc-date-sep">•</span>
                      <span class="kc-date-item">Atualizado: <strong>{{ optional($card->updated_at)->format('d/m/Y H:i') }}</strong></span>
                    </div>
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

          {{-- Mantém o valor original para submit --}}
          <input type="hidden" name="fields_snapshot_json" value="">

          <div class="form-group">
            <label>Dados do formulário</label>
            <div id="snapshotView" class="snapshot-wrap">
              <div class="snapshot-empty">Sem dados de formulário.</div>
            </div>
            <div class="help-text">Leitura dos dados enviados no formulário que originou este card.</div>
          </div>

          <ul class="nav nav-tabs" role="tablist" style="margin-bottom:10px">
            <li class="active"><a href="#tab-details" role="tab" data-toggle="tab">Detalhes</a></li>
            <li><a href="#tab-notes" role="tab" data-toggle="tab">Notas</a></li>
            <li><a href="#tab-files" role="tab" data-toggle="tab">Anexos</a></li>
          </ul>

          <div class="tab-content">
            <!-- Detalhes (placeholder) -->
            <div role="tabpanel" class="tab-pane active" id="tab-details"></div>

            <!-- Notas -->
            <div role="tabpanel" class="tab-pane" id="tab-notes">
              <div id="editNotesErrors" class="alert alert-danger" style="display:none"></div>

              <div class="form-group">
                <label>Nova nota</label>
                <textarea id="noteContent" class="form-control" rows="3" placeholder="Escreve uma nota curta…"></textarea>
                <button type="button" id="btnAddNote" class="btn btn-default" style="margin-top:6px">Adicionar nota</button>
              </div>

              <div id="notesList" class="list-group" style="max-height:240px; overflow:auto"></div>
            </div>

            <!-- Anexos -->
            <div role="tabpanel" class="tab-pane" id="tab-files">
              <div id="editFilesErrors" class="alert alert-danger" style="display:none"></div>

              <div class="form-inline" style="margin-bottom:8px">
                <input type="file" id="attachFile" class="form-control" style="display:inline-block">
                <button type="button" id="btnUploadFile" class="btn btn-default">Carregar</button>
              </div>

              <ul id="filesList" class="list-unstyled" style="max-height:240px; overflow:auto"></ul>
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
  // --- Helpers UI ---
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

  // --- Snapshot helpers ---
  function decodeHtmlEntities(str){
    if (!str) return '';
    const txt = document.createElement('textarea');
    txt.innerHTML = str;
    return txt.value;
  }
  function renderSnapshot(jsonStr){
    const box = document.getElementById('snapshotView');
    if (!box) return;
    box.innerHTML = '';

    if (!jsonStr || !jsonStr.trim()){
      const empty = document.createElement('div');
      empty.className = 'snapshot-empty';
      empty.textContent = 'Sem dados de formulário.';
      box.appendChild(empty);
      return;
    }

    let data;
    try { data = JSON.parse(jsonStr); }
    catch(e){
      const pre = document.createElement('pre');
      pre.className = 'snapshot-wrap';
      pre.textContent = jsonStr;
      box.appendChild(pre);
      return;
    }

    function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m])); }
    function makeValueNode(v){
      const div = document.createElement('div');
      div.className = 'snapshot-val';
      if (v === null || v === undefined) div.innerHTML = '<span class="snapshot-code">—</span>';
      else if (Array.isArray(v)){
        if (!v.length) div.innerHTML = '<span class="snapshot-code">[]</span>';
        else {
          const ul = document.createElement('ul'); ul.className = 'snapshot-sublist';
          v.forEach(it=>{ const li=document.createElement('li'); li.textContent = (typeof it==='object'? JSON.stringify(it): String(it)); ul.appendChild(li); });
          div.appendChild(ul);
        }
      } else if (typeof v === 'object'){
        const ul = document.createElement('ul'); ul.className = 'snapshot-sublist';
        Object.keys(v).forEach(k=>{
          const li=document.createElement('li');
          li.innerHTML = '<strong>'+escapeHtml(k)+':</strong> '+escapeHtml(String(v[k]));
          ul.appendChild(li);
        });
        div.appendChild(ul);
      } else {
        div.textContent = String(v);
      }
      return div;
    }

    const list = document.createElement('ul');
    list.className = 'snapshot-list';

    if (Array.isArray(data)){
      data.forEach((v,i)=>{
        const li = document.createElement('li'); li.className='snapshot-item';
        const k  = document.createElement('div'); k.className='snapshot-key'; k.textContent = '#'+i;
        li.appendChild(k); li.appendChild(makeValueNode(v)); list.appendChild(li);
      });
    } else if (typeof data === 'object'){
      Object.keys(data).forEach(key=>{
        const li = document.createElement('li'); li.className='snapshot-item';
        const k  = document.createElement('div'); k.className='snapshot-key'; k.textContent = key;
        li.appendChild(k); li.appendChild(makeValueNode(data[key])); list.appendChild(li);
      });
    }
    box.appendChild(list);
  }

  // --- Card builders ---
  function buildCardElement(c){
    const createdTxt = c.created_at_html || c.created_at || '';
    const updatedTxt = c.updated_at_html || c.updated_at || '';

    const a = document.createElement('a');
    a.className = 'kanban-card';
    a.href = c.show_url || ('{{ url('admin/crm-cards') }}/'+c.id);
    a.target = '_blank';
    a.dataset.card = c.id;
    a.dataset.pos  = c.position || '';
    a.dataset.title = (c.title || '').toLowerCase();
    a.dataset.priority = (c.priority || 'medium').toLowerCase();
    a.dataset.due_at = c.due_at || '';
    a.dataset.stage_id = c.stage_id;
    a.dataset.source = (c.source || 'manual').toLowerCase();
    a.setAttribute('data-snapshot', c.fields_snapshot_json || '');
    a.setAttribute('data-created_at', createdTxt);
    a.setAttribute('data-updated_at', updatedTxt);

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
      <div class="kc-meta kc-dates">
        <span class="kc-date-item">Criado: <strong>${createdTxt || '—'}</strong></span>
        <span class="kc-date-sep">•</span>
        <span class="kc-date-item">Atualizado: <strong>${updatedTxt || '—'}</strong></span>
      </div>
    `;
    return a;
  }

  function upsertCard(c){
    let cardEl = document.querySelector(`.kanban-card[data-card="${c.id}"]`);
    const targetCol = document.querySelector(`.kanban-col[data-stage="${c.stage_id}"]`);

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

    const titleEl = cardEl.querySelector('.kc-title');
    if (titleEl) titleEl.textContent = c.title || '';
    cardEl.dataset.title = (c.title || '').toLowerCase();

    const priBadge = cardEl.querySelector('.badge');
    if (priBadge) {
      priBadge.classList.remove('pri-low','pri-medium','pri-high');
      priBadge.classList.add(c.priority === 'high' ? 'pri-high' : (c.priority === 'low' ? 'pri-low' : 'pri-medium'));
      priBadge.textContent = (c.priority || 'medium').replace(/^./, s=>s.toUpperCase());
    }

    cardEl.dataset.priority = (c.priority || 'medium').toLowerCase();
    cardEl.dataset.due_at   = c.due_at || '';
    cardEl.dataset.stage_id = c.stage_id;
    cardEl.dataset.source   = (c.source || 'manual').toLowerCase();
    cardEl.setAttribute('data-snapshot', c.fields_snapshot_json || '');

    // >>> NOVO: datas
    const createdTxt = c.created_at_html || c.created_at || cardEl.getAttribute('data-created_at') || '';
    const updatedTxt = c.updated_at_html || c.updated_at || ''; // após update, backend deve devolver atualizado
    cardEl.setAttribute('data-created_at', createdTxt);
    if (updatedTxt) cardEl.setAttribute('data-updated_at', updatedTxt);

    const datesEl = cardEl.querySelector('.kc-dates');
    if (datesEl) {
      datesEl.innerHTML = `
        <span class="kc-date-item">Criado: <strong>${createdTxt || '—'}</strong></span>
        <span class="kc-date-sep">•</span>
        <span class="kc-date-item">Atualizado: <strong>${updatedTxt || cardEl.getAttribute('data-updated_at') || '—'}</strong></span>
      `;
    }

    if (c.position) cardEl.dataset.pos = c.position;

    cardEl.style.transition = 'background-color .4s';
    cardEl.style.backgroundColor = '#f0fdf4';
    setTimeout(()=> cardEl.style.backgroundColor = '', 400);
  }


  // --- Abrir modal editar a partir do card ---
  function openEditFromEl(el){
    const $f = $('#editCardForm');
    $('#editCardErrors').hide().empty();
    if (!el) return alert('Card não encontrado.');

    $f.find('[name="id"]').val(el.dataset.card);
    $f.find('[name="title"]').val(el.querySelector('.kc-title')?.textContent?.trim() || '');
    $f.find('[name="priority"]').val(el.dataset.priority || 'medium');
    $f.find('[name="due_at"]').val(el.dataset.due_at || '');
    const stageId = el.dataset.stage_id || el.closest('.kanban-col')?.dataset.stage;
    $f.find('[name="stage_id"]').val(String(stageId));
    $f.find('[name="source"]').val(el.dataset.source || 'manual');

    // snapshot
    const snapRaw = el.getAttribute('data-snapshot') || '';   // ainda com &quot;
    const snap    = decodeHtmlEntities(snapRaw);              // JSON válido
    $f.find('[name="fields_snapshot_json"]').val(snapRaw);    // mantém como veio para submit
    renderSnapshot(snap);

    $('#editCardModal').modal('show');
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
        .catch(() => {});
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

  // -------- Notas / Anexos (opcional, igual ao teu) ----------
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
          item.innerHTML = `<div style="font-size:12px;color:#6b7280">${(n.user_name||'—')} • ${n.created_at}</div>
                            <div>${(n.content||'').replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m]))}</div>`;
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
  $('#editCardModal').on('shown.bs.modal', function(){
    const id = $('#editCardForm [name="id"]').val();
    loadNotes(id);
    loadFiles(id);
  });
  $('#btnAddNote').on('click', function(){
    const id = $('#editCardForm [name="id"]').val();
    const $ta = $('#noteContent'); const content = $ta.val().trim();
    const $err = $('#editNotesErrors');
    if (!content) return;
    addNote(id, content)
      .then(resp=>{ if(!resp.ok) throw new Error('Falhou ao gravar nota.'); $('#noteContent').val(''); loadNotes(id); })
      .catch(err=>{ $err.text(err.message).show(); setTimeout(()=> $err.hide().empty(), 3000); });
  });
  $('#btnUploadFile').on('click', function(){
    const id = $('#editCardForm [name="id"]').val();
    const f = document.getElementById('attachFile').files[0];
    const $err = $('#editFilesErrors'); if (!f) return;
    uploadFile(id, f)
      .then(resp=>{ if(!resp.ok) throw new Error('Falhou upload.'); document.getElementById('attachFile').value=''; loadFiles(id); })
      .catch(err=>{ $err.text(err.message).show(); setTimeout(()=> $err.hide().empty(), 3000); });
  });
  $(document).on('click', '.btn-del-file', function(){
    const id = $('#editCardForm [name="id"]').val();
    const mediaId = $(this).closest('li').data('mediaId');
    const $err = $('#editFilesErrors');
    deleteFile(id, mediaId)
      .then(resp=>{ if(!resp.ok) throw new Error('Falhou ao remover.'); loadFiles(id); })
      .catch(err=>{ $err.text(err.message).show(); setTimeout(()=> $err.hide().empty(), 3000); });
  });
</script>
@endsection
