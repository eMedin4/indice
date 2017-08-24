@extends('layouts.master')

@section('title', 'Cartelera - Películas en televisión')
@section('metadescription', '¿Que películas estan echando ahora en Televisión? ¿Cuales podras ver esta noche? Toda las peliculas en programación de TDT, Movistar plus y canales digitales')
@section('og_type', 'website')
@section('og_url', 'http://indicecine.net/Televisión')
@section('og_title', 'Indicecine televisión')
<!-- falta imagen -->
@section('og_description', 'Programación de televisión: Todas las películas de los canales de la TDT, Movistar Plus, y canales digitales')
@section('bodyclass', 'list-page')

@section('content')

	<div class="wrap">

		@include('includes.sidebar')
		<section class="content">

			<div class="main-content">

				<h1 class="h1 margins" data-id="{{$list->id}}" data-link="{{url()->current()}}" data-counter="{{$list->movies->count()}}">{{$list->name}}</h1>

				@if ($list->description)
					<p class="list-description margins">{{$list->description}}</p>
				@endif
					
				<div class="list-info-wrap flex-between margins">
					<ul class="list-info">

						@if (Auth::check())

							<!-- Si eres el autor de la lista puedes editarla-->
							@if (Auth::id() == $list->user_id)
								<li><a class="link btn-launch-edit" href="{{route('editlist', ['name' => str_slug($list->name), 'id' => $list->id])}}">editar</a></li>

							<!-- Si no eres el autor puedes darle a me gusta -->
							@elseif (!$listLiked)
								<li><span class="link launch-like btn-launch-like" data-url="{{route('likelist')}}" data-alturl="{{route('dislikelist')}}"><i class="fa fa-heart-full-outline"></i></span></li>

							<!-- Si ya le has dado a me gusta -->
							@else
								<li><span class="link launch-dislike btn-launch-dislike" data-url="{{route('dislikelist')}}" data-alturl="{{route('likelist')}}"><i class="fa fa-check"></i></span></li>
							@endif

						@else

							<!-- Si no estás logado -->
							<li><a href="{{route('login')}}" class="link launch-like"><i class="fa fa-heart-full-outline"></i></a></li>
						@endif

						<li><span class="link btn-launch-filters">filtrar</span></li>
						@if (isset($filters['year']))
							<li><span class="link btn-cancel-filter">año {{$filters['fromyear']}} - {{$filters['toyear']}}</span></li>
						@endif
						@if (isset($filters['note']))
							<li><span class="link btn-cancel-filter">puntuación {{$filters['fromnote']}} - {{$filters['tonote']}}</span></li>
						@endif
						<li class="break">{{$list->created_at->formatLocalized('%e %b %Y')}}, actualizada {{$list->updated_at->diffForHumans()}}. </li>
						<li>{{$list->movies->count()}} películas. </li>
						<li><a class="user" href="{{route('userpage', ['name' => str_slug($list->user->name), 'id' => $list->user->id])}}"><i class="icon-user fa fa-user-circle"></i> {{$list->user->nick}}</a></li>
					</ul>
				</div>

				@include('includes.filters')

				@if ($list->count())
					
					@include('includes.loop', ['items' => $list->movies])
				@else
					<h3 class="empty">No hay nada aún</h3>
				@endif

			</div><!--main-content-->

		</section>


	</div>
@endsection
