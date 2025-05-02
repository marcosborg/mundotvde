@extends('layouts.website')
@section('title')
{{ $article->title ?? '' }} - Mundo TVDE
@endsection
@section('description')
{{ $article->resume ?? '' }}
@endsection
@section('content')
<section class="clean-block clean-post dark">
    <div class="container">
        <div class="block-content">
            <div class="post-body">
                <h3>{{ $article->title ?? '' }}</h3>
                <hr>
                @if ($article->photo)
                <img src="{{ $article->photo->getUrl() }}" class="img-fluid float-start" style="margin-right: 60px; margin-bottom: 20px;">
                @endif
                <h6>{{ $article->resume ?? '' }}</h6>
                {!! $article->text ?? '' !!}
            </div>
        </div>
    </div>
</section>
@endsection