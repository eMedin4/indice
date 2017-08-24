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

				<h1 class="h1 margins">Estrenos y cartelera en cines</h1>

				<p class="list-description margins">Todas las películas en cartelera y películas de estreno en esta semana, proyectadas en salas de cine de España desde el {{$currentDate->formatLocalized('%e de %B del %Y')}}.</p>

				<div class="list-info-wrap margins">
					<ul class="list-info">
						<li><span class="link btn-launch-filters">filtrar</span></li>
						@if (isset($filters['year']))
							<li><span class="link btn-cancel-filter">año {{$filters['fromyear']}} - {{$filters['toyear']}}</span></li>
						@endif
						@if (isset($filters['note']))
							<li><span class="link btn-cancel-filter">puntuación {{$filters['fromnote']}} - {{$filters['tonote']}}</span></li>
						@endif
						<li class="break">{{$list->count()}} películas. </li>
						<li><a class="user" href="{{route('userpage', ['name' => 'indicecine', 'id' => 1])}}"><i class="icon-user fa fa-user-circle"></i> Indicecine</a></li>
					</ul>
				</div>
				
				@include('includes.filters')

				@if ($list->count())
					@include('includes.loop', ['items' => $list])
				@else
					<h3 class="empty">No hay nada aún</h3>
				@endif

			</div><!--main-content-->

			<div class="darken-overlay"></div>

		</section>


	</div>
@endsection
