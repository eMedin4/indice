@extends('layouts.master')

@section('title', 'Película - ' . $movie->title)
@section('metadescription', $movie->title . ': ' . $movie->review)
@section('og_type', 'article')
@section('more_og')
	<meta property="article:author"        content="https://www.facebook.com/Indicecine-247570282336712" />
	<meta property="article:publisher"     content="https://www.facebook.com/Indicecine-247570282336712" />
@endsection
@section('og_url', Request::fullUrl())
@section('og_title', $movie->title)
@section('og_image', asset('/assets/posters/large') . $movie->poster)
@section('og_description', $movie->review)
@section('bodyclass', 'single-page')

@section('content')

<div class="wrap">

	@include('includes.sidebar')

	<section class="content">

		@include('includes.mobile-bar')

		@if ($movie->check_background)
			<div class="single-content" style="background-image: url({{ asset('/assets/movieimages/backgrounds/std/' . $movie->slug . '.jpg') }})">
		@else
			<div class="single-content single-content-no-background">
		@endif
			
			<div class="background-gradient"></div>

			<div class="movie-header wrap-mobile">

				<div class="inner">

					@if ($movie->check_poster)
						<picture>
						  <source srcset="{{asset('/assets/movieimages/posters/lrg') . '/' . $movie->slug . '.jpg'}}" media="(min-width: 500px)">
						  <source srcset="{{asset('/assets/movieimages/posters/std') . '/' . $movie->slug . '.jpg'}}">
						  <img srcset="medium.jpg" alt="{{$movie->title}}" title="poster de {{$movie->title}}" >
						</picture>
					@else 
						<img src="{{asset('/assets/images/no-poster-medium.png')}}" alt="{{$movie->title}}" title="poster de {{$movie->title}}">
					@endif


					<div class="movie-data">

						<h1>{{$movie->title}}</h1>

						<ul class="features">
							<li><span class="country country-{{str_slug($movie->country)}}"></span>{{$movie->original_title}}</li>
							<li>{{$movie->year}}</li>
							@if ($movie->genres->count())
								<li>@foreach ($movie->genres as $genre)
									{{$genre->name}}@if(!$loop->last),@endif
								@endforeach</li>
							@endif
							<li>{{$movie->duration}} minutos</li>
						</ul>

					</div>
				</div>
			</div>

			<div class="movie-summary wrap-mobile">

				<p>{{$movie->review}}</p>

				@if ($directors->count())
					<p class="actors">
						<span class="intro">Director: </span>
						@foreach($directors as $director)
							@if ($loop->last)
								<a href="{{route('character', ['id' => $director->id, 'name' => $director->slug])}}">{{$director->name}}</a>.
							@else
								<a href="{{route('character', ['id' => $director->id, 'name' => $director->slug])}}">{{$director->name}}</a>, 
							@endif

						@endforeach
					</p>
				@endif	

				@if ($actors->count())
					<p class="actors">
						<span class="intro">Actores: </span>
						@foreach($actors as $actor)
							@if ($loop->index < 4)
								<a href="{{route('character', ['id' => $actor->id, 'name' => $actor->slug])}}">{{$actor->name}}</a>, 
							@elseif ($loop->index == 4)
								<span class="more">más...</span>
								<a class="hide" href="{{route('character', ['id' => $actor->id, 'name' => $actor->slug])}}">{{$actor->name}}, </a> 
							@else
								<a class="hide" href="{{route('character', ['id' => $actor->id, 'name' => $actor->slug])}}">{{$actor->name}}, </a>
							@endif

						@endforeach
					</p>
				@endif
			</div>

			<div class="tools wrap-mobile">

				@if ($movie->fa_rat)

					<div>
					
						<div class="rating">
							<div class="rating-score">
								<span class="rating-total">{{$movie->fa_rat}}</span>
								<div class="stars stars-{{$movie->fa_stars}}"></div>
							</div>
							<div class="rating-meta">
								{{$movie->fa_rat_count}}<i class="fa fa-supervisor-account"></i><a href="http://www.filmaffinity.com/es/film{{$movie->fa_id}}.html" rel=nofollow target="_blank" class="link">Filmaffinity</a>
							</div>
						</div>

						@if ($movie->im_rat)
							<div class="rating">
								<div class="rating-score">
									<span class="rating-total">{{$movie->im_rat}}</span>
									<div class="stars stars-{{$movie->im_stars}}"></div>
								</div>
								<div class="rating-meta">
									{{$movie->im_rat_count}}<i class="fa fa-supervisor-account"></i><a href="http://www.imdb.com/title/{{$movie->imdb_id}}" rel=nofollow target="_blank" class="link">IMDB</a>
								</div>
							</div>
						@endif

						@if ($movie->rt_rat)
							<div class="rating rating-tomattoes">
								<div class="rating-score">
									<span class="rating-total">{{$movie->rt_rat}}<i>%</i></span>
									<div class="stars stars-{{$movie->rt_stars}}"></div>
								</div>
								<div class="rating-meta">
									<a href="{{$movie->rt_url}}" rel=nofollow target="_blank" class="link">Rotten Tomattoes</a>
								</div>
							</div>
						@endif

					</div>

				@endif


				@if (Auth::guest())

					<a href="{{route('login')}}" class="link link-large">
						Añadir a tus listas <i class="fa fa-keyboard-arrow-down"></i>
					</a>

				@else

					<div class="link link-large btn-launch-lists">
						Añadir a tus listas <i class="fa fa-keyboard-arrow-down"></i>
					</div>

					@include('includes.modal-add-to-list')
					
				@endif

			</div>

			<div class="critics wrap-mobile">
				@foreach($movie->critics as $critic)
					<ul class="critic-auth">
						<li><i class="icon-user fa fa-user-circle"></i></li>
						<li>{{$critic->ext_author}}</li>
						<li>{{$critic->ext_media}}</li>
					</ul>
					<p>{{$critic->text}}</p>
				@endforeach
			</div>


		</div><!-- single container -->

	</section>

</div>
@endsection
