
<div class="top-bar">

	<div class="wrap">

		<div class="top-bar-inner">

			<div class="top-bar-title">
				<div class="toggle-icon">
					<i class="fa fa-menu-oi"></i>
				</div>
				<div class="logo">
					<a href="{{route('home')}}">
						indicecine
					</a>
				</div>
			</div>


			<ul class="top-bar-options">
				<li><a href="{{route('search')}}">
					<span>Buscar</span>
				</a></li>
				@if (Auth::check())
					<li><a href="{{route('userpage', ['name' => str_slug(Auth::user()->name), 'id' => Auth::id()])}}" class="nick">
						<span>{{Auth::user()->nick}}</span>
					</a></li>
				@else
					<li><a href="{{route('login')}}" class="nick">
						<span>Entrar</span>
					</a></li>
				@endif
			</ul>

		</div>

	</div>

</div>
	