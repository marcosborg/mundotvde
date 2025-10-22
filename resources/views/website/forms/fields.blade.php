{{-- resources/views/website/forms/fields.blade.php --}}
@if($form)
  {{-- IMPORTANTE: esta partial não abre nem fecha <form> --}}
  {{-- Se precisares de campos hidden específicos por slug, podes tratar aqui também --}}
  @foreach($form->fields->sortBy('position') as $f)
    @php
      $opts = $f->options_json ? (is_array($f->options_json) ? $f->options_json : json_decode($f->options_json,true)) : [];
      $name = 'field_'.$f->id;
    @endphp

    <div class="form-group" style="margin-bottom:12px">
      <label>
        {{ $f->label }}
        @if($f->required)<span style="color:#e11d48">*</span>@endif
      </label>

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
@endif
