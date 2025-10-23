@if($form)
  {{-- IMPORTANTE: esta partial não abre nem fecha <form> --}}
  @foreach($form->fields->sortBy('position') as $f)
    @php
      // Decodificar opções (aceita string JSON ou array)
      $opts = $f->options_json
        ? (is_array($f->options_json) ? $f->options_json : (json_decode($f->options_json, true) ?: []))
        : [];

      $name    = 'field_'.$f->id;
      $oldVal  = old($name, '');
      $label   = (string) $f->label;
      $labelLc = function_exists('mb_strtolower') ? mb_strtolower($label) : strtolower($label);

      // Heurística p/ tratar telefone como TEXTO (não number)
      $isPhone = (strpos($labelLc, 'telefone') !== false)
              || (strpos($labelLc, 'telemóvel') !== false)
              || (strpos($labelLc, 'telemovel') !== false)
              || (strpos($labelLc, 'phone') !== false)
              || (strpos($labelLc, 'celular') !== false);

      $min = $f->min_value;
      $max = $f->max_value;

      // Helpers de classes de erro
      $invalidClass = $errors->has($name) ? ' is-invalid' : '';
    @endphp

    <div class="form-group" style="margin-bottom:12px">
      <label>
        {{ $f->label }}
        @if($f->required)<span style="color:#e11d48">*</span>@endif
      </label>

      @if($f->type === 'text' || ($f->type === 'number' && $isPhone))
        {{-- Telefone como TEXTO: valida por comprimento --}}
        <input
          type="text"
          name="{{ $name }}"
          class="form-control{{ $invalidClass }}"
          placeholder="{{ $f->placeholder }}"
          value="{{ $oldVal }}"
          @if($f->required) required @endif
          @if(!is_null($min)) minlength="{{ (int) $min }}" @endif
          @if(!is_null($max)) maxlength="{{ (int) $max }}" @endif
          @if($isPhone) inputmode="tel" autocomplete="tel" @endif
        >
        @error($name)
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror

      @elseif($f->type === 'textarea')
        <textarea
          name="{{ $name }}"
          class="form-control{{ $invalidClass }}"
          placeholder="{{ $f->placeholder }}"
          @if($f->required) required @endif
          @if(!is_null($min)) minlength="{{ (int) $min }}" @endif
          @if(!is_null($max)) maxlength="{{ (int) $max }}" @endif
        >{{ $oldVal }}</textarea>
        @error($name)
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror

      @elseif($f->type === 'number')
        {{-- Números reais: min/max numéricos --}}
        <input
          type="number"
          name="{{ $name }}"
          class="form-control{{ $invalidClass }}"
          placeholder="{{ $f->placeholder }}"
          value="{{ $oldVal }}"
          @if($f->required) required @endif
          @if(!is_null($min)) min="{{ (float) $min }}" @endif
          @if(!is_null($max)) max="{{ (float) $max }}" @endif
        >
        @error($name)
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror

      @elseif($f->type === 'checkbox')
        <div class="{{ $errors->has($name) ? 'is-invalid' : '' }}">
          <label>
            <input
              type="checkbox"
              name="{{ $name }}"
              value="1"
              {{ old($name) ? 'checked' : '' }}
              @if($f->required) required @endif
            >
            {{ $f->placeholder ?: 'Selecionar' }}
          </label>
        </div>
        @error($name)
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror

      @elseif($f->type === 'select')
        <select name="{{ $name }}" class="form-control{{ $invalidClass }}" @if($f->required) required @endif>
          <option value="">—</option>
          @foreach($opts as $o)
            @php
              // aceita arrays simples ou [{value,label}]
              $val = is_array($o) ? ($o['value'] ?? ($o['label'] ?? '')) : $o;
              $lab = is_array($o) ? ($o['label'] ?? ($o['value'] ?? '')) : $o;
            @endphp
            <option value="{{ $val }}" {{ (string)$oldVal === (string)$val ? 'selected' : '' }}>
              {{ $lab }}
            </option>
          @endforeach
        </select>
        @error($name)
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      @endif

      @if($f->help_text)
        <p class="help-block">{{ $f->help_text }}</p>
      @endif
    </div>
  @endforeach
@endif
