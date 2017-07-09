<div class="loop margins">

	@foreach ($items as $movie)
		<article data-id="{{$movie->id}}">
			<!-- {{-- <div class="tv-tag tv-tag-alert">
				<div class="tv-time">{!!$formatTime!!}</div>
				<div class="channel-logo"><div class="channel-logo-{{$channel_code}}"></div></div>
			</div> --}} -->

			<a class="loop-poster" href="{{route('show', $movie->slug)}}" data-id="{{$movie->id}}">
				@if ($movie->check_poster)
					<img src="{{asset('/assets/movieimages/posters/std') . '/' . $movie->slug . '.jpg'}}" alt="{{$movie->title}}" title="poster de {{$movie->title}}">
				@else 
					<img src="{{asset('/assets/images/no-poster-medium.png')}}" alt="{{$movie->title}}" title="poster de {{$movie->title}}">						
				@endif
			</a>

			<div class="loop-data">

				<h2><a class="title" href="{{route('show', $movie->slug)}}">{{$movie->title}}</a></h2>
				@if (Route::is('home'))
					@if ($movie->name == 'Estreno')
						<span class="lab-release lab-new-release">Estreno {{$movie->date->formatLocalized('%d %b')}}</span>
					@else
						<span class="lab-release">{{$movie->date->diffForHumans()}}</span>
					@endif
				@elseif (Route::is('editlist'))
					<div class="link link-alert btn-delete btn-delete-movie" data-id="{{$movie->id}}" data-text="¿Deseas borrar '{{$movie->title}}'' de la lista?" data-type="delete-movie">borrar</div>
				@endif


				<div class="review"><p>{{str_limit($movie->review, 400)}}</p></div>

				<div class="loop-features">
					{{$movie->year}}
					<div class="country country-{{$movie->country}}" title="{{$movie->country}}"></div>
					<div class="stars stars-{{$movie->avg}}"></div>
				</div>

			</div>


		</article>
	@endforeach

	@if (Route::is('editlist'))
	<!-- borrar películas o listas -->
		<div class="modal-wrap modal-wrap-confirm">
			<div class="modal">
				<div class="modal-inner">
					<div class="header">
						<h3></h3>
						<i class="modal-close propagation fa fa-times"></i>
					</div>
					<form method="POST" class="form-delete" data-actionmovie="{{route('extractlist')}}" data-actionlist="{{route('deletelist')}}" data-redirect="{{route('userpage', ['name' => str_slug(Auth::user()->name), 'id' => Auth::id()])}}">
			            {{ csrf_field() }}
			            <input type="hidden" name="id">  
			            <input type="hidden" name="type">  
			            <div class="btn-group">
			                <button type="submit" class="link">Aceptar</button>
			                <button type="button" class="link link-dark propagation">Cancelar</button>
			            </div>
			        </form>
				</div>
			</div>
		</div>
	@endif

</div>
