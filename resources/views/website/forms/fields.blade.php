{{-- resources/views/website/forms/fields.blade.php --}}
@if($form)
  {{-- IMPORTANTE: esta partial não abre nem fecha <form> --}}
  @foreach($form->fields->sortBy('position') as $f)
    @php
      // Decodificar opções (aceita string JSON ou array)
      $opts = $f->options_json
        ? (is_array($f->options_json) ? $f->options_json : (json_decode($f->options_json, true) ?: []))
        : [];

      $name    = 'field_'.$f->id;
      $oldVal  = old($name, ''); // manter valor após validação
      $label   = (string) $f->label;
      $labelLc = mb_strtolower($label); // evitar dependência do Illuminate\Support\Str

      // Heurística simples para tratar telefone como TEXTO
      $isPhone = str_contains($labelLc, 'telefone')
              || str_contains($labelLc, 'telemóvel')
              || str_contains($labelLc, 'telemovel')
              || str_contains($labelLc, 'phone')
              || str_contains($labelLc, 'celular');

      // limites vindos do model
      $min = $f->min_value;
      $max = $f->max_value;
    @endphp

    <div class="form-group" style="margin-bottom:12px">
      <label>
        {{ $f->label }}
        @if($f->required)<span style="color:#e11d48">*</span>@endif
      </label>

      @if($f->type === 'text' || ($f->type === 'number' && $isPhone))
        {{-- Telefone/telemóvel como TEXTO (valida por comprimento, não por valor numérico) --}}
        <input
          type="text"
          name="{{ $name }}"
          class="form-control"
          placeholder="{{ $f->placeholder }}"
          value="{{ $oldVal }}"
          @if($f->required) required @endif
          @if(!is_null($min)) minlength="{{ (int) $min }}" @endif
          @if(!is_null($max)) maxlength="{{ (int) $max }}" @endif
          @if($isPhone) inputmode="tel" autocomplete="tel" @endif
        >
      @elseif($f->type === 'textarea')
        <textarea
          name="{{ $name }}"
          class="form-control"
          placeholder="{{ $f->placeholder }}"
          @if($f->required) required @endif
          @if(!is_null($min)) minlength="{{ (int) $min }}" @endif
          @if(!is_null($max)) maxlength="{{ (int) $max }}" @endif
        >{{ $oldVal }}</textarea>
      @elseif($f->type === 'number')
        {{-- Número real: aplica min/max numéricos --}}
        <input
          type="number"
          name="{{ $name }}"
          class="form-control"
          placeholder="{{ $f->placeholder }}"
          value="{{ $oldVal }}"
          @if($f->required) required @endif
          @if(!is_null($min)) min="{{ (float) $min }}" @endif
          @if(!is_null($max)) max="{{ (float) $max }}" @endif
        >
      @elseif($f->type === 'checkbox')
        <div>
          <label>
            <input type="checkbox" name="{{ $name }}" value="1" {{ old($name) ? 'checked' : '' }}>
            {{ $f->placeholder ?: 'Selecionar' }}
          </label>
        </div>
      @elseif($f->type === 'select')
        <select name="{{ $name }}" class="form-control" @if($f->required) required @endif>
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
      @endif

      @if($f->help_text)
        <p class="help-block">{{ $f->help_text }}</p>
      @endif
    </div>
  @endforeach
@endif
