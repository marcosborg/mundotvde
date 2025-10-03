@extends('layouts.admin')

@section('styles')
@parent
<style>
  .builder-wrap{display:flex;gap:14px;align-items:flex-start}
  .builder-left{flex:0 0 520px}
  .builder-right{flex:1}
  .field-item{border:1px solid #e5e7eb;background:#fff;border-radius:8px;padding:10px;margin-bottom:10px;display:flex;justify-content:space-between;align-items:center;cursor:grab}
  .field-meta{font-size:12px;color:#6b7280}
  .ghost{opacity:.5}
</style>
@endsection

@section('content')
<div class="content">
  <div class="panel panel-default">
    <div class="panel-heading" style="display:flex;justify-content:space-between;align-items:center;gap:10px">
      <div>
        <strong>Form Builder:</strong> {{ $crm_form->name }}
        <small class="text-muted">/ slug: <code>{{ $crm_form->slug }}</code></small>
      </div>
      <div>
        <a href="{{ route('admin.crm-forms.builder.index') }}" class="btn btn-default btn-sm">Voltar ao Hub</a>
      </div>
    </div>

    <div class="panel-body builder-wrap">
      {{-- Coluna esquerda: lista de campos --}}
      <div class="builder-left">
        <div class="panel panel-default">
          <div class="panel-heading" style="display:flex;justify-content:space-between;align-items:center">
            <span>Campos</span>
            <button class="btn btn-success btn-xs" data-toggle="modal" data-target="#fieldCreateModal">
              <i class="fa fa-plus"></i> Adicionar campo
            </button>
          </div>
          <div class="panel-body">
            <div id="fieldsList" data-form="{{ $crm_form->id }}">
              @forelse($crm_form->fields as $f)
                <div class="field-item" data-id="{{ $f->id }}">
                  <div>
                    <div>
                      <strong class="js-title">{{ $f->label }}</strong>
                      <span class="label label-default js-type">{{ $f->type }}</span>
                    </div>
                    <div class="field-meta js-meta">
                      {{ $f->required ? 'Obrigatório' : 'Opcional' }}
                      @if($f->placeholder) • placeholder: <em>{{ $f->placeholder }}</em>@endif
                      @if($f->help_text) • ajuda: <em>{{ $f->help_text }}</em>@endif
                    </div>
                  </div>
                  <div style="white-space:nowrap">
                    <button type="button" class="btn btn-default btn-xs js-edit">Editar</button>
                    <button type="button" class="btn btn-danger btn-xs js-field-del" data-id="{{ $f->id }}">Apagar</button>
                  </div>
                </div>
              @empty
                <div class="alert alert-info">Ainda não há campos. Adiciona o primeiro.</div>
              @endforelse
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">Incorporação no site</div>
          <div class="panel-body">
            <p>Usa o token no conteúdo das páginas/artigos. O teu renderer deve procurar e substituir:</p>
<pre>@{{ 'render:crm-form slug="{{ $crm_form->slug }}"' }}</pre>
            <small>Podes criar um middleware/trait para detetar este token e renderizar o HTML do form.</small>
          </div>
        </div>
      </div>

      {{-- Coluna direita: pré-visualização simples --}}
      <div class="builder-right">
        <div class="panel panel-default">
          <div class="panel-heading">Pré-visualização</div>
          <div class="panel-body" id="preview">
            @if($crm_form->fields->isEmpty())
              <div class="text-muted">Sem campos.</div>
            @else
              <form>
                @foreach($crm_form->fields as $f)
                  <div class="form-group">
                    <label>{{ $f->label }} @if($f->required)<span style="color:#e11d48">*</span>@endif</label>
                    @if($f->type === 'text')
                      <input type="text" class="form-control" placeholder="{{ $f->placeholder }}">
                    @elseif($f->type === 'textarea')
                      <textarea class="form-control" placeholder="{{ $f->placeholder }}"></textarea>
                    @elseif($f->type === 'number')
                      <input type="number" class="form-control" min="{{ $f->min_value }}" max="{{ $f->max_value }}" placeholder="{{ $f->placeholder }}">
                    @elseif($f->type === 'checkbox')
                      <div><label><input type="checkbox"> {{ $f->placeholder ?: 'Selecionar' }}</label></div>
                    @elseif($f->type === 'select')
                      @php
                        $opts = $f->options_json ? (is_array($f->options_json) ? $f->options_json : json_decode($f->options_json,true)) : [];
                      @endphp
                      <select class="form-control">
                        <option value="">—</option>
                        @foreach($opts as $o)
                          <option value="{{ $o }}">{{ $o }}</option>
                        @endforeach
                      </select>
                    @endif
                    @if($f->help_text)<p class="help-block">{{ $f->help_text }}</p>@endif
                  </div>
                @endforeach
                <button type="button" class="btn btn-primary" disabled>Enviar (preview)</button>
              </form>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal: criar campo --}}
<div class="modal fade" id="fieldCreateModal" tabindex="-1" role="dialog">
  <div class="modal-dialog"><div class="modal-content">
    <form id="fieldCreateForm" autocomplete="off">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        <h4 class="modal-title">Novo campo</h4>
      </div>
      <div class="modal-body">
        <div id="fcErr" class="alert alert-danger" style="display:none"></div>

        <div class="form-group">
          <label>Etiqueta</label>
          <input type="text" name="label" class="form-control" required>
        </div>

        <div class="row">
          <div class="col-xs-6">
            <div class="form-group">
              <label>Tipo</label>
              <select name="type" class="form-control">
                @foreach($fieldTypes as $t)
                  <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-xs-6">
            <div class="form-group">
              <label>Obrigatório</label>
              <select name="required" class="form-control">
                <option value="0">Não</option>
                <option value="1">Sim</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group"><label>Placeholder</label>
          <input type="text" name="placeholder" class="form-control">
        </div>
        <div class="form-group"><label>Ajuda</label>
          <input type="text" name="help_text" class="form-control">
        </div>

        <div class="row">
          <div class="col-xs-6">
            <div class="form-group"><label>Mín.</label>
              <input type="number" name="min_value" class="form-control">
            </div>
          </div>
          <div class="col-xs-6">
            <div class="form-group"><label>Máx.</label>
              <input type="number" name="max_value" class="form-control">
            </div>
          </div>
        </div>

        <div class="form-group"><label>Opções (para select) — 1 por linha</label>
          <textarea name="options" class="form-control" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">
          <span class="txt">Adicionar</span>
          <span class="spinner" style="display:none"><i class="fa fa-spinner fa-spin"></i></span>
        </button>
      </div>
    </form>
  </div></div>
</div>

{{-- Modal: editar campo --}}
<div class="modal fade" id="fieldEditModal" tabindex="-1" role="dialog">
  <div class="modal-dialog"><div class="modal-content">
    <form id="fieldEditForm" autocomplete="off">
      <input type="hidden" name="id">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        <h4 class="modal-title">Editar campo</h4>
      </div>
      <div class="modal-body">
        <div id="feErr" class="alert alert-danger" style="display:none"></div>

        <div class="form-group"><label>Etiqueta</label>
          <input type="text" name="label" class="form-control" required>
        </div>

        <div class="row">
          <div class="col-xs-6">
            <div class="form-group"><label>Tipo</label>
              <select name="type" class="form-control">
                @foreach($fieldTypes as $t)
                  <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-xs-6">
            <div class="form-group"><label>Obrigatório</label>
              <select name="required" class="form-control">
                <option value="0">Não</option>
                <option value="1">Sim</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group"><label>Placeholder</label>
          <input type="text" name="placeholder" class="form-control">
        </div>
        <div class="form-group"><label>Ajuda</label>
          <input type="text" name="help_text" class="form-control">
        </div>

        <div class="row">
          <div class="col-xs-6"><div class="form-group"><label>Mín.</label>
            <input type="number" name="min_value" class="form-control">
          </div></div>
          <div class="col-xs-6"><div class="form-group"><label>Máx.</label>
            <input type="number" name="max_value" class="form-control">
          </div></div>
        </div>

        <div class="form-group"><label>Opções (select) — 1 por linha</label>
          <textarea name="options" class="form-control" rows="3"></textarea>
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
  </div></div>
</div>
@endsection

@section('scripts')
@parent
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
(function(){
  const formId = @json($crm_form->id);
  const token  = document.querySelector('meta[name="csrf-token"]').content;
  const FIELDS_BASE = @json(url('admin/crm-form-fields'));
  const RE_BUILDER_DELETE = /\/crm-forms\/\d+\/builder$/i;
  const UPDATE_URL_TMPL = @json(route('admin.crm-forms.fields.update', ['field' => '__ID__']));

  // Bloqueio global de DELETE para /builder (evita 405 no console)
  (function hardBlockBuilderDeletes(){
    const _fetch = window.fetch;
    window.fetch = function(input, init){
      const url = (typeof input === 'string') ? input : (input && input.url) || '';
      const method = (init && init.method ? String(init.method) : 'GET').toUpperCase();
      if (method === 'DELETE' && RE_BUILDER_DELETE.test(url)) {
        return Promise.resolve(new Response(JSON.stringify({ ok:true, blocked:true }), {
          status: 200, headers: { 'Content-Type':'application/json' }
        }));
      }
      return _fetch.apply(this, arguments);
    };
  })();

  // Sortable: reorder
  new Sortable(document.getElementById('fieldsList'), {
    handle: '.field-item',
    animation: 150,
    ghostClass: 'ghost',
    onEnd: function(){
      const ids = Array.from(document.querySelectorAll('.field-item')).map(x => x.dataset.id);
      fetch(@json(route('admin.crm-forms.fields.reorder', $crm_form)),{
        method:'PATCH',
        headers:{
          'X-CSRF-TOKEN':token,
          'Content-Type':'application/json',
          'Accept':'application/json'
        },
        body: JSON.stringify({ order: ids }),
        credentials:'same-origin'
      }).catch(()=>{});
    }
  });

  function parseOptions(str){ return str.split(/\r?\n/).map(s=>s.trim()).filter(Boolean); }

  // CREATE field
  document.getElementById('fieldCreateForm').addEventListener('submit', function(e){
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    const err = document.getElementById('fcErr');
    err.style.display='none'; err.innerHTML='';
    btn.disabled=true; btn.querySelector('.txt').style.display='none'; btn.querySelector('.spinner').style.display='inline-block';

    const fd = new FormData(this);
    const payload = {
      form_id: formId,
      label: fd.get('label'),
      type: fd.get('type'),
      required: fd.get('required') === '1' ? 1 : 0,
      placeholder: fd.get('placeholder') || null,
      help_text: fd.get('help_text') || null,
      min_value: fd.get('min_value') || null,
      max_value: fd.get('max_value') || null,
      options_json: (fd.get('type') === 'select') ? JSON.stringify(parseOptions(fd.get('options')||'')) : '[]'
    };

    fetch(@json(route('admin.crm-forms.fields.store', $crm_form)),{
      method:'POST',
      headers:{'X-CSRF-TOKEN':token,'Content-Type':'application/json','Accept':'application/json'},
      body: JSON.stringify(payload),
      credentials:'same-origin'
    })
    .then(async r=>{ if(!r.ok){ const d=await r.json().catch(()=>({})); throw new Error(d.message||'Erro ao criar'); } return r.json(); })
    .then(()=>{ window.location.reload(); })
    .catch(e=>{ err.innerHTML=e.message; err.style.display='block'; })
    .finally(()=>{ btn.disabled=false; btn.querySelector('.spinner').style.display='none'; btn.querySelector('.txt').style.display='inline'; });
  });

  // Abrir modal editar
  document.addEventListener('click', function(e){
    const editBtn = e.target.closest('.js-edit');
    if (!editBtn) return;
    const item  = editBtn.closest('.field-item');
    const id    = item.dataset.id;
    const label = item.querySelector('.js-title').textContent.trim();
    const type  = item.querySelector('.js-type').textContent.trim();

    const fm = document.getElementById('fieldEditForm');
    fm.reset();
    fm.querySelector('[name="id"]').value    = id;
    fm.querySelector('[name="label"]').value = label;
    fm.querySelector('[name="type"]').value  = type;
    fm.querySelector('[name="required"]').value =
      item.querySelector('.js-meta')?.textContent.includes('Obrigatório') ? '1' : '0';

    $('#fieldEditModal').modal('show');
  });

  // DELETE field (usa endpoint certo)
  window.addEventListener('click', function(e){
    const delBtn = e.target.closest('.js-field-del');
    if (!delBtn) return;
    e.preventDefault();
    e.stopPropagation();

    const id = delBtn.dataset.id || delBtn.closest('.field-item')?.dataset.id;
    if (!id) return;
    if (!confirm('Apagar este campo?')) return;

    fetch(FIELDS_BASE + '/' + encodeURIComponent(id), {
      method:'DELETE',
      headers:{ 'X-CSRF-TOKEN': token, 'Accept':'application/json' },
      credentials:'same-origin'
    })
    .then(r => {
      if(!r.ok) throw new Error('Erro ao apagar');
      const item = document.querySelector(`.field-item[data-id="${id}"]`);
      if (item) item.parentNode.removeChild(item);
      setTimeout(() => window.location.reload(), 80);
    })
    .catch(() => alert('Erro ao apagar'));
  }, true);

  // UPDATE field (usa POST no endpoint dedicado {field}/update)
  document.getElementById('fieldEditForm').addEventListener('submit', function(e){
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    const err = document.getElementById('feErr');
    err.style.display='none'; err.innerHTML='';
    btn.disabled=true; btn.querySelector('.txt').style.display='none'; btn.querySelector('.spinner').style.display='inline-block';

    const fd = new FormData(this);
    const id = fd.get('id');

    // construir o corpo como FormData (sem Content-Type manual)
    const body = new FormData();
    body.append('form_id', formId);
    body.append('label', fd.get('label') || '');
    body.append('type', fd.get('type') || 'text');
    body.append('required', fd.get('required') === '1' ? 1 : 0);
    body.append('placeholder', fd.get('placeholder') || '');
    body.append('help_text', fd.get('help_text') || '');
    body.append('min_value', fd.get('min_value') || '');
    body.append('max_value', fd.get('max_value') || '');
    body.append('options_json', (fd.get('type') === 'select') ? JSON.stringify(parseOptions(fd.get('options')||'')) : '[]');

    const updUrl = UPDATE_URL_TMPL.replace('__ID__', encodeURIComponent(id));

    fetch(updUrl, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json'
      },
      body,
      credentials:'same-origin'
    })
    .then(async r=>{ if(!r.ok){ const d=await r.json().catch(()=>({})); throw new Error(d.message||'Erro ao guardar'); } return r.json(); })
    .then(()=>{ window.location.reload(); })
    .catch(e=>{ err.innerHTML=e.message; err.style.display='block'; })
    .finally(()=>{ btn.disabled=false; btn.querySelector('.spinner').style.display='none'; btn.querySelector('.txt').style.display='inline'; });
  });

  // Evitar submits “normais” dentro do builder
  document.querySelectorAll('.builder-wrap form').forEach(f=> f.addEventListener('submit', ev => ev.preventDefault()));
})();
</script>
@endsection
