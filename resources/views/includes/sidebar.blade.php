
	<section class="info">

		<div class="info-close"><i class="fa fa-times"></i></div>

		<ul class="item-list info-margins my-lists">
			<li><div class="item-top item-top-mylists">MIS LISTAS</div></li>
			@if (Auth::check())
				@if ($mylists->count())
					@foreach($mylists as $list)
						@include('partials.info-lists')
					@endforeach
				@else
					<li><span class="item-empty">No tienes ninguna lista creada</span></li>
				@endif
				<li><div class="link btn-new-list" data-position="info">nueva lista</div></li>

			@else
				<li>
					<a href="{{route('login')}}"><span class="item-count">0</span><span><i class="icon-ordered fa fa-sort-numeric-asc"></i>Mi Top 100</span></a>
				</li>
				<li>
					<a href="{{route('login')}}"><span class="item-count">0</span><span>Películas que quiero ver</span></a>
				</li>
				<li>
					<a href="{{route('login')}}"><span class="item-count">0</span><span>Películas que ya he visto</span></a>
				</li>
			@endif
		</ul>

		<ul class="item-list info-margins my-likes">
			<li><div class="item-top item-top-likes">LISTAS QUE ME GUSTAN</div></li>
			@if (Auth::check())
				@if ($mylikelists->count())
					@foreach ($mylikelists as $list)
						@include('partials.info-lists')
					@endforeach
				@endif
			@else
				<li><span class="item-empty">Aún no tienes nada</span></li>
			@endif
		</ul>

		<ul class="item-list info-margins popular">
			<li><div class="item-top item-top-trend">LISTAS TOP</div></li>

			<li>
				<a href="{{route('home')}}">
					<span class="item-count"><i class="fa fa-theater"></i></span>
					<span>Estrenos y cartelera en cines</span>
				</a>
			</li>

			<li>
				<a href="{{route('tv')}}">
					<span class="item-count"><i class="fa fa-live-tv"></i></span>
					<span>Ahora en Televisión</span>
				</a>
			</li>

			@foreach ($popular as $list)
				@include('partials.info-lists')
			@endforeach
		</ul>
		
	</section>

	<!-- MODAL NEW LIST, SE LEE TAMBIEN DESDE SINGLE Y DESDE EDIT -->
	@include('includes.modal-new-list')
