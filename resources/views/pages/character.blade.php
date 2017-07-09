@extends('layouts.master')

@section('title', 'Cartelera - Películas en televisión')
@section('metadescription', '¿Que películas estan echando ahora en Televisión? ¿Cuales podras ver esta noche? Toda las peliculas en programación de TDT, Movistar plus y canales digitales')
@section('og_type', 'website')
@section('og_url', 'http://indicecine.net/Televisión')
@section('og_title', 'Indicecine televisión')
<!-- falta imagen -->
@section('og_description', 'Programación de televisión: Todas las películas de los canales de la TDT, Movistar Plus, y canales digitales')
@section('bodyclass', 'character-page')

@section('content')

	<div class="wrap">

		@include('includes.sidebar')

		<section class="content">

			@include('includes.mobile-bar')

			<div class="character-content">

				<div class="character-header margins">

					@if($character->photo && file_exists(public_path() . '/assets/movieimages/credits' . $character->photo))
						<img class="character-image" src="{{asset('/assets/movieimages/credits') . $character->photo}}" alt="{{$character->photo}}" title="foto de {{$character->photo}}">
						<div class="loop-data">
					@else
						<div class="loop-data no-character-image">
					@endif

					<h1 class="h1">{{$character->name}} </h1>

						<div class="character-info">
							<p>{{$character->department}}</p>
							<p>{{$character->movies->count()}} @if ($character->movies->count() == 1) película @else peliculas @endif</p>
						</div>

					</div>

				</div>

				@include('includes.filters')

				@if ($character->movies->count())
					
					@include('includes.loop', ['items' => $character->movies])
				@else
					<h3 class="empty">No hay nada aún</h3>
				@endif

			</div><!--main-content-->

		</section>

	</div>
@endsection
