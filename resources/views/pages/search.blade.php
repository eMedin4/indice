@extends('layouts.master')

@section('title', 'Cartelera - Películas en televisión')
@section('metadescription', '¿Que películas estan echando ahora en Televisión? ¿Cuales podras ver esta noche? Toda las peliculas en programación de TDT, Movistar plus y canales digitales')
@section('og_type', 'website')
@section('og_url', 'http://indicecine.net/Televisión')
@section('og_title', 'Indicecine televisión')
<!-- falta imagen -->
@section('og_description', 'Programación de televisión: Todas las películas de los canales de la TDT, Movistar Plus, y canales digitales')
@section('bodyclass', 'home-page')

@section('content')

	<div class="wrap">

		@include('includes.sidebar')

		<section class="content">

			<div class="main-content">

				<div class="wrap-search margins">

					<form autocomplete="off" method="GET" action="{{route('search')}}" class="form-search">{!! csrf_field() !!}
						<input type="text" name="search" class="input-search" placeholder="Busca una película" data-url="{{ route('livesearch') }}" data-path="{{ asset('') }}">
						<button type="submit"><i class="icon-search fa fa-search-btb"></i></button>
					</form>

					<div class="search-results">
						<ul></ul>
					</div>

				</div>

				@if ($movies)
					<div class="list-info-wrap margins">
						<ul class="list-info">
							<li>Resultados de Búsqueda: <span>{{$string}}.</span></li>
							<li>{{$movies->count()}} películas. </li>
						</ul>
						<ul class="list-info-actions">
							<li><span class="link btn-launch-filters">filtrar</span></li>
						</ul>
					</div>

					@include('includes.filters')

					@include('includes.loop', ['items' => $movies])
					
				@endif

			</div>

		</section>


	</div>
@endsection
