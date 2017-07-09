
	<section class="info">

		<div class="logo info-margins">
			<a href="{{route('home')}}">
				Indicecine
				<!-- <span class="shape"></span>
				<span class="shape2"></span>
				<span class="shape3"></span> -->
			</a>
		</div>

		<ul class="items info-margins">
			<li><a href="{{route('search')}}"><span>Buscar películas</span><i class="fa fa-search-btb search-minicon"></i></a></li>
			@if (Auth::check())
				<li><a href="{{route('userpage', ['name' => str_slug(Auth::user()->name), 'id' => Auth::id()])}}" class="nick"><span>{{Auth::user()->nick}}</span><i class="icon-user fa fa-user"></i></a></li>
			@else
				<li><a href="{{route('login')}}" class="nick">Entra con tu usuario<i class="icon-user fa fa-user"></i></a></li>
			@endif
		</ul>

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
					<a href="{{route('login')}}"><span><i class="icon-ordered fa fa-sort-numeric-asc"></i>Mi Top 100</span><span class="item-count">0</span></a>
				</li>
				<li>
					<a href="{{route('login')}}"><span>Películas que quiero ver</span><span class="item-count">0</span></a>
				</li>
				<li>
					<a href="{{route('login')}}"><span>Películas que ya he visto</span><span class="item-count">0</span></a>
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
			<li><div class="item-top item-top-trend">POPULARES</div></li>
			@foreach ($popular as $list)
				@include('partials.info-lists')
			@endforeach
		</ul>
		
	</section>

	<!-- MODAL NEW LIST, SE LEE TAMBIEN DESDE SINGLE Y DESDE EDIT -->
	@include('includes.modal-new-list')
