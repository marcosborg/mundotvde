@if($form)
  @if (session('crm_form_ok'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom:12px">
      {{ session('crm_form_ok') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
  @endif

  <form action="{{ route('public-forms.submit') }}" method="POST"
        class="crm-form js-guard-submit" data-slug="{{ $form->slug }}">
    @csrf
    <input type="hidden" name="form_slug" value="{{ $form->slug }}">

    @foreach($form->fields->sortBy('position') as $f)
      <div class="form-group" style="margin-bottom:12px">
        <label>
          {{ $f->label }}
          @if($f->required)<span style="color:#e11d48">*</span>@endif
        </label>

        @php
          $opts = $f->options_json ? (is_array($f->options_json) ? $f->options_json : json_decode($f->options_json,true)) : [];
          $name = 'field_'.$f->id;
        @endphp

        @if($f->type === 'text')
          <input type="text" name="{{ $name }}" class="form-control" placeholder="{{ $f->placeholder }}" {{ $f->required ? 'required' : '' }}>
        @elseif($f->type === 'textarea')
          <textarea name="{{ $name }}" class="form-control" placeholder="{{ $f->placeholder }}" {{ $f->required ? 'required' : '' }}></textarea>
        @elseif($f->type === 'number')
          <input type="number" name="{{ $name }}" class="form-control" placeholder="{{ $f->placeholder }}"
                 @if(!is_null($f->min_value)) min="{{ $f->min_value }}" @endif
                 @if(!is_null($f->max_value)) max="{{ $f->max_value }}" @endif
                 {{ $f->required ? 'required' : '' }}>
        @elseif($f->type === 'checkbox')
          <div><label><input type="checkbox" name="{{ $name }}" value="1"> {{ $f->placeholder ?: 'Selecionar' }}</label></div>
        @elseif($f->type === 'select')
          <select name="{{ $name }}" class="form-control" {{ $f->required ? 'required' : '' }}>
            <option value="">—</option>
            @foreach($opts as $o)
              <option value="{{ $o }}">{{ $o }}</option>
            @endforeach
          </select>
        @endif

        @if($f->help_text)<p class="help-block">{{ $f->help_text }}</p>@endif
      </div>
    @endforeach

    @if($form->slug === 'rent' && isset($car))
      <input type="hidden" name="car_id" value="{{ (int)$car->id }}">
    @endif

    @if($form->slug === 'stand' && isset($standCar))
      <input type="hidden" name="stand_car_id" value="{{ (int)$standCar->id }}">
    @endif

    {{-- Mensagem durante submissão --}}
    <div class="submit-status text-muted" style="display:none;margin-top:8px">
      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
      A enviar o seu pedido...
    </div>

    <button type="submit" class="btn btn-primary" style="margin-top:8px">
      <span class="btn-text">Enviar</span>
      <span class="btn-spinner spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none"></span>
    </button>
  </form>

  {{-- Guard de submissão (inicializa uma única vez) --}}
  <script>
    (function(){
      if (window.__crmFormGuardInit) return; // evita duplicar
      window.__crmFormGuardInit = true;

      function arm(form){
        if (!form || form.__armed) return;
        form.__armed = true;

        form.addEventListener('submit', function(e){
          // evita duplos cliques
          if (form.dataset.submitting === '1') {
            e.preventDefault();
            return false;
          }
          form.dataset.submitting = '1';

          // botão: desativar, trocar texto, mostrar spinner
          var btn = form.querySelector('button[type="submit"]');
          if (btn) {
            var t = btn.querySelector('.btn-text');
            var s = btn.querySelector('.btn-spinner');
            btn.disabled = true;
            if (t) t.textContent = 'A enviar...';
            if (s) s.style.display = 'inline-block';
          }

          // mensagem “A enviar…”
          var status = form.querySelector('.submit-status');
          if (status) status.style.display = 'block';
        }, { passive: true });
      }

      // Armar todos os forms existentes e futuros (se forem injetados)
      document.querySelectorAll('form.js-guard-submit').forEach(arm);
      // fallback para casos em que o DOM ainda carrega conteúdo
      document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('form.js-guard-submit').forEach(arm);
      });
    })();
  </script>
@endif
