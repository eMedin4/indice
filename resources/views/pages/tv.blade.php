@extends('layouts.master')

@section('title', 'Cartelera - Películas en televisión')
@section('metadescription', '¿Que películas estan echando ahora en Televisión? ¿Cuales podras ver esta noche? Toda las peliculas en programación de TDT, Movistar plus y canales digitales')
@section('og_type', 'website')
@section('og_url', 'http://indicecine.net/Televisión')
@section('og_title', 'Películas hoy en tv')
<!-- falta imagen -->
@section('og_description', '¿Quieres qué películas hay ahora en tv? ¿Y esta noche? Indicecine es la guía de películas en tv de toda la parrilla de televisión.')
@section('bodyclass', 'list-page')

@section('content')

	<div class="wrap">

		@include('includes.sidebar')

		<section class="content">

			<div class="main-content">

				<div class="header-list margins">
					<i class="fa fa-live-tv"></i>
					<h1 class="h1">Películas en Tv</h1>
				</div>

				<p class="list-description margins">Todas las películas hoy en tv. La programación de tv de cine hoy, canal por canal.</p>

				<div class="list-info-wrap margins">
					<ul class="list-info">
						<li><span class="link btn-launch-filters">filtrar</span></li>
						@if (isset($filters['year']))
							<li><span class="link btn-cancel-filter">año {{$filters['fromyear']}} - {{$filters['toyear']}}</span></li>
						@endif
						@if (isset($filters['note']))
							<li><span class="link btn-cancel-filter">puntuación {{$filters['fromnote']}} - {{$filters['tonote']}}</span></li>
						@endif
						<li><a class="user" href="{{route('userpage', ['name' => 'indicecine', 'id' => 1])}}"><i class="icon-user fa fa-user-circle"></i> Indicecine</a></li>
					</ul>
				</div>
				
				@include('includes.filters')

				@if ($list->count())
					@include('includes.loop', ['items' => $list])
				@else
					<h3 class="empty margins">No hay nada aún</h3>
				@endif

			</div><!--main-content-->

			<div class="darken-overlay"></div>

		</section>


	</div>
@endsection
