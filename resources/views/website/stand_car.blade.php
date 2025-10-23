@extends('layouts.website')
@section('title')
Compre a sua viatura {{ $car->brand->name }} {{ $car->car_model->name }}
@endsection
@section('description')
{{ $car->brand->name }} {{ $car->car_model->name }} preparado para utilização TVDE. Apenas {{ $car->price }}€
@endsection
@section('content')
<section class="clean-block clean-product dark mt-5">
    <div class="container">
        <div class="block-heading">
            <h2 class="text-info">{{ $car->brand->name }} {{ $car->car_model->name }}</h2>
        </div>
        <div class="block-content">
            <button class="btn btn-light ms-3" onclick="history.back()"><i class="fas fa-arrow-left icon"></i></button>
            <div class="product-info">
                <div class="row">
                    <div class="col-md-6">
                        <div class="gallery">
                            <div id="product-preview" class="vanilla-zoom">
                                <div class="zoomed-image"></div>
                                <div class="sidebar" style="width: 100%; display: block;">
                                    @foreach ($car->images as $image)
                                    <img class="img-fluid d-block small-preview" style="float: left;" src="{{ $image->url }}">
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info">
                            <div class="price">
                                <div class="mb-4">
                                    <h1><small>€</small> {{ $car->price }}</h1>
                                    @if ($car->status->id == 1)
                                    <span class="badge bg-danger">Vendido</span>
                                    @else
                                    <span class="badge bg-success">Disponível</span>
                                    @endif
                                </div>
                                <div class="row">
                                    <script>console.log({!! $car !!})</script>
                                    <div class="col">
                                        <p><strong>Ano: </strong>{{ $car->year }} | {{ $car->month->name }}</p>
                                        <p><strong>Quilómetros: </strong>{{ $car->kilometers }} km</p>
                                        <p><strong>Caixa: </strong>{{ $car->transmision }}</p>
                                        <p><strong>Combustivel: </strong>{{ $car->fuel->name }}</p>
                                    </div>
                                    <div class="col">
                                        @if ($car->battery_capacity)
                                        <p><strong>Capacidade da bateria: </strong>{{ $car->battery_capacity }} kWh</p>
                                        @else
                                        <p><strong>Cilindrada: </strong>{{ $car->cylinder_capacity }} cm3</p>
                                        @endif
                                        <p><strong>Potência: </strong>{{ $car->power }} CV</p>
                                        <p><strong>Origem: </strong>{{ $car->origin->name }}</p>
                                        <p><strong>Localidade: </strong>{{ $car->distance }}</p>
                                    </div>
                                </div>
                                @php
                                    use App\Models\CrmForm;

                                    $formStand = CrmForm::with(['fields' => fn($q) => $q->orderBy('position')])
                                        ->where('slug', 'stand')->where('status', 'published')->first();
                                @endphp

                                {{-- mostra e consome a flash uma única vez --}}
                                @php $crmOk = session()->pull('crm_form_ok'); @endphp
                                @if ($crmOk)
                                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom:12px">
                                        {{ $crmOk }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                                    </div>
                                @endif


                                @if($formStand)
                                {{-- Passa o objeto ou o id da viatura ao form --}}
                                @include('website.forms.render', [
                                    'form'      => $formStand,
                                    'standCar'  => $standCar ?? null,   // se tiveres a viatura como objeto
                                    // 'car'     => null,                // só para não confundir com 'rent'
                                ])
                                @else
                                <div class="alert alert-warning">Formulário “stand” não disponível.</div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection