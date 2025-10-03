@php($fields = $form->fields->sortBy('position'))
<form method="POST" action="{{ route('public-forms.submit') }}" class="crm-form" enctype="multipart/form-data">
  @csrf
  <input type="hidden" name="form_id" value="{{ $form->id }}">
  @foreach($fields as $f)
    <div class="form-group">
      <label>
        {{ $f->label }} {!! $f->required ? '<span style="color:#e11d48">*</span>' : '' !!}
      </label>

      @switch($f->type)
        @case('textarea')
          <textarea class="form-control" name="f[{{ $f->id }}]" placeholder="{{ $f->placeholder }}" {{ $f->required?'required':'' }}>{{ old("f.$f->id",$f->default_value) }}</textarea>
        @break

        @case('number')
          <input type="number" class="form-control" name="f[{{ $f->id }}]" placeholder="{{ $f->placeholder }}" value="{{ old("f.$f->id",$f->default_value) }}" {{ $f->required?'required':'' }}>
        @break

        @case('checkbox')
          <div>
            <label><input type="checkbox" name="f[{{ $f->id }}]" value="1" {{ old("f.$f->id")?'checked':'' }}> {{ $f->placeholder }}</label>
          </div>
        @break

        @case('select')
          @php($opts = json_decode($f->options_json ?: '[]', true) ?: [])
          <select class="form-control" name="f[{{ $f->id }}]" {{ $f->required?'required':'' }}>
            <option value="">-- selecione --</option>
            @foreach($opts as $opt)
              <option value="{{ $opt }}" @selected(old("f.$f->id")==$opt)>{{ $opt }}</option>
            @endforeach
          </select>
        @break

        @default
          <input type="text" class="form-control" name="f[{{ $f->id }}]" placeholder="{{ $f->placeholder }}" value="{{ old("f.$f->id",$f->default_value) }}" {{ $f->required?'required':'' }}>
      @endswitch

      @if($f->help_text)<small class="text-muted">{{ $f->help_text }}</small>@endif
    </div>
  @endforeach

  <button type="submit" class="btn btn-primary">Enviar</button>
</form>
@if(session('form_ok_'.$form->id))
  <div class="alert alert-success" style="margin-top:10px">{{ session('form_ok_'.$form->id) }}</div>
@endif
