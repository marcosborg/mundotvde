@extends('layouts.website')

@section('title', 'Aluguer de viaturas')

@section('description')
Aqui, encontrará soluções para alugar a sua viatura TVDE e começar o trabalho que tanto deseja como motorista.
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
  .swiper{width:100%;height:100%}
  .swiper-slide{
    text-align:center;font-size:18px;background:#fff;
    display:flex;justify-content:center;align-items:center
  }
  .swiper-slide img{display:block;width:100%;height:100%;object-fit:cover}
  .swiper{width:100%;height:300px;margin-left:auto;margin-right:auto}
  .swiper-slide{background-size:cover;background-position:center}
  .mySwiper2{height:80%;width:100%}
  .mySwiper{height:20%;box-sizing:border-box;padding:10px 0}
  .mySwiper .swiper-slide{width:25%;height:100%;opacity:.4}
  .mySwiper .swiper-slide-thumb-active{opacity:1}
  .swiper-wrapper{height:100%}
  .swiper-wrapper.thumb{height:100px;margin:10px}
  .submit-status{display:none;margin:8px 0 0;font-size:.95rem}
</style>
@endsection

@section('content')
<section class="clean-block clean-blog-list dark pt-5">
  <div class="container">
    <div class="block-heading">
      <h2 class="text-info">Aluguer de viaturas</h2>
    </div>

    {{-- ✅ Alerta de sucesso após redirect (consumido 1x) --}}
    @php $crmOk = session()->pull('crm_form_ok'); @endphp
    @if ($crmOk)
      <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom:16px">
        {{ $crmOk }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
      </div>
    @endif

    <div class="block-content">
      @php
        $formRent = \App\Models\CrmForm::with(['fields' => fn($q) => $q->orderBy('position')])
                    ->where('slug','rent')->where('status','published')->first();
      @endphp

      @foreach ($cars as $car)
        <div class="clean-blog-post">
          <div class="row">
            <div class="col-lg-6">
              @if ($car->photo && $car->photo->count())
                <div class="swiper photo-{{ $car->id }}" style="--swiper-navigation-color:#fff;--swiper-pagination-color:#fff">
                  <div class="swiper-wrapper">
                    @foreach ($car->photo as $photo)
                      <div class="swiper-slide">
                        <img src="{{ $photo->url }}" alt="Foto {{ $car->title }}">
                      </div>
                    @endforeach
                  </div>
                  <div class="swiper-button-next"></div>
                  <div class="swiper-button-prev"></div>
                </div>

                <div thumbsSlider class="swiper thumbs-{{ $car->id }}">
                  <div class="swiper-wrapper thumb">
                    @foreach ($car->photo as $photo)
                      <div class="swiper-slide">
                        <img src="{{ $photo->url }}" alt="Thumb {{ $car->title }}" style="height:100px">
                      </div>
                    @endforeach
                  </div>
                </div>
              @endif
            </div>

            <div class="col-lg-6">
              <div style="margin-left:59px">
                <p><strong><span style="color:#555">Especificações:</span></strong></p>
                <p class="fw-bold"><span style="color:#6c757d">Desde €{{ number_format($car->price, 2, ',', '.') }} por semana*</span></p>
                {!! $car->specifications !!}
                <button
                  class="btn btn-outline-primary btn-sm"
                  type="button"
                  style="margin-top:20px"
                  data-bs-toggle="modal"
                  data-bs-target="#carModal-{{ $car->id }}">
                  Pedir contacto
                </button>
              </div>
            </div>
          </div>
        </div>

        {{-- Modal por carro --}}
        <div class="modal fade" id="carModal-{{ $car->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <form action="{{ route('public-forms.submit') }}" method="POST" class="js-guard-submit">
                @csrf
                <input type="hidden" name="form_slug" value="rent">
                <input type="hidden" name="car_id" value="{{ $car->id }}">

                <div class="modal-header">
                  <h1 class="modal-title fs-5">Pedido de contacto</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                  <h2 class="h4 mb-1">{{ $car->title }}</h2>
                  <h3 class="h6 text-muted">{{ $car->subtitle }}</h3>
                  <hr>

                  @if($formRent)
                    @include('website.forms.fields', ['form' => $formRent])
                  @else
                    <div class="alert alert-warning">Formulário "rent" não disponível.</div>
                  @endif

                  <div class="submit-status text-muted">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    A enviar o seu pedido...
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                  <button type="submit" class="btn btn-primary">
                    <span class="btn-text">Pedir contacto</span>
                    <span class="btn-spinner spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none"></span>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
        {{-- /Modal --}}
      @endforeach
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

{{-- Swiper --}}
@foreach ($cars as $car)
<script>
  (function(){
    var thumbs = new Swiper(".thumbs-{{ $car->id }}", {
      spaceBetween: 10, slidesPerView: 4, freeMode: true, watchSlidesProgress: true
    });
    new Swiper(".photo-{{ $car->id }}", {
      spaceBetween: 10,
      navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
      thumbs: { swiper: thumbs }
    });
  })();
</script>
@endforeach

<script>
(function(){
  // ✅ Reabrir automaticamente o modal correto se houve erros de validação do backend
  @if ($errors->any() && old('form_slug') === 'rent' && old('car_id'))
    document.addEventListener('DOMContentLoaded', function(){
      var id = 'carModal-{{ old('car_id') }}';
      var el = document.getElementById(id);
      if (el && window.bootstrap && bootstrap.Modal) {
        var m = new bootstrap.Modal(el);
        m.show();
      } else if (el) {
        // fallback: trigger data-bs
        el.classList.add('show');
        el.style.display = 'block';
      }
    });
  @endif

  // ✅ Guard de submissão + mensagens nativas do HTML5 dentro do modal
  document.querySelectorAll('form.js-guard-submit').forEach(function(form){
    form.addEventListener('submit', function(e){
      if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
        if (typeof form.reportValidity === 'function') form.reportValidity();
        return;
      }
      if (form.dataset.submitting === '1') {
        e.preventDefault();
        return;
      }
      form.dataset.submitting = '1';

      var btn = form.querySelector('button[type="submit"]');
      if (btn) {
        var btnText = btn.querySelector('.btn-text');
        var btnSpin = btn.querySelector('.btn-spinner');
        btn.disabled = true;
        if (btnText) btnText.textContent = 'A enviar...';
        if (btnSpin) btnSpin.style.display = 'inline-block';
      }
      var status = form.querySelector('.submit-status');
      if (status) status.style.display = 'block';

      // Failsafe (se não houver redirect)
      setTimeout(function(){
        if (form.dataset.submitting === '1') {
          form.dataset.submitting = '0';
          if (btn) {
            btn.disabled = false;
            if (btnText) btnText.textContent = 'Pedir contacto';
            if (btnSpin) btnSpin.style.display = 'none';
          }
          if (status) status.style.display = 'none';
        }
      }, 10000);
    }, { passive: false });
  });
})();
</script>
@endsection
