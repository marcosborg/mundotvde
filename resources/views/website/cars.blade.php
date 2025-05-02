@extends('layouts.website')
@section('title')
Aluguer de viaturas
@endsection
@section('description')
Aqui, encontrará soluções para alugar a sua viatura TVDE e começar o trabalho que tanto deseja como motorista.
@endsection
@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    .swiper {
    width: 100%;
    height: 100%;
    }

    .swiper-slide {
    text-align: center;
    font-size: 18px;
    background: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    }

    .swiper-slide img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    }

    .swiper {
    width: 100%;
    height: 300px;
    margin-left: auto;
    margin-right: auto;
    }

    .swiper-slide {
    background-size: cover;
    background-position: center;
    }

    .mySwiper2 {
    height: 80%;
    width: 100%;
    }

    .mySwiper {
    height: 20%;
    box-sizing: border-box;
    padding: 10px 0;
    }

    .mySwiper .swiper-slide {
    width: 25%;
    height: 100%;
    opacity: 0.4;
    }

    .mySwiper .swiper-slide-thumb-active {
    opacity: 1;
    }

    .swiper-slide img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    }
    .swiper-wrapper{
        height: 100%;
    }
    .swiper-wrapper.thumb {
        height: 100px;
        margin: 10px;
    }
</style>
    @endsection
    @section('content')
    <section class="clean-block clean-blog-list dark pt-5">
        <div class="container">
            <div class="block-heading">
                <h2 class="text-info">Aluguer de viaturas</h2>
            </div>
            <div class="block-content">
                @foreach ($cars as $car)
                <div class="clean-blog-post">
                    <div class="row">
                        <div class="col-lg-6">
                            @if ($car->photo)
                            
                            <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff"
                            class="swiper photo-{{ $car->id }}">
                            
                            <div class="swiper-wrapper">
                                @foreach ($car->photo as $photo)
                                <div class="swiper-slide">
                                    <img src="{{ $photo->url }}" />
                                </div>
                                @endforeach
                            </div>
                            
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                            
                            
                            
                            <div thumbsSlider="" class="swiper thumbs-{{ $car->id }}">
                                <div class="swiper-wrapper thumb">
                                    @foreach ($car->photo as $photo)
                                    <div class="swiper-slide">
                                        <img src="{{ $photo->url }}" style="height: 100px;" />
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="col-lg-6">
                            <div style="margin-left: 59px;">
                                <p><strong><span style="color: rgb(85, 85, 85);">Especificações:</span></strong><br></p>
                                <p class="fw-bold"><span style="color: rgb(108, 117, 125);">Desde €{{ $car->price }} por
                                    semana*</span><br></p>
                                {!! $car->specifications !!}
                                <button onclick="openCarModal({{ $car->id }})" class="btn btn-outline-primary btn-sm"
                                    type="button" style="margin-top: 20px;">Pedir
                                    contacto</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Modal -->
    <div class="modal fade" id="carModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/forms/carRentalContact" method="post" id="carRentalContact">
                    @csrf
                    <input type="hidden" id="car_id" name="car_id">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Pedido de contacto</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h2 id="car_title"></h2>
                        <h3 id="car_subtitle"></h3>
                        <hr>
                        <div class="form-group">
                            <label for="name">Nome</label>
                            <input type="text" class="form-control" name="name" id="name">
                        </div>
                        <div class="form-group">
                            <label for="phone">Telefone</label>
                            <input type="text" class="form-control" name="phone" id="phone">
                        </div>
                        <div class="form-group">
                            <label for="name">Email</label>
                            <input type="email" class="form-control" name="email" id="email">
                        </div>
                        <div class="form-group">
                            <label for="city">Cidade</label>
                            <input type="text" class="form-control" name="city" id="city">
                        </div>
                        <div class="row mt-4">
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tvde" name="tvde">
                                    <label class="form-check-label" for="tvde">
                                        Tem cartão TVDE?
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <input type="text" id="tvde_card" name="tvde_card" class="form-control d-none"
                                        placeholder="Número">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-4">
                            <label for="message">Mensagem</label>
                            <textarea name="message" id="message" class="form-control"></textarea>
                        </div>
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="rgpd" name="rgpd">
                            <label class="form-check-label" for="rgpd">
                                Autorizo o tratamento dos dados fornecidos
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Pedir contacto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection
    @section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @foreach ($cars as $car)
    <script>
        var swiper = new Swiper(".thumbs-{{ $car->id }}", {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
        });
        var swiper2 = new Swiper(".photo-{{ $car->id }}", {
            spaceBetween: 10,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            thumbs: {
                swiper: swiper,
            },
        });
    </script>
    @endforeach
    @endsection