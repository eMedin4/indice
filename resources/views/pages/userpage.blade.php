@extends('layouts.master')

@section('title', 'Cartelera - Películas en televisión')
@section('metadescription', '¿Que películas estan echando ahora en Televisión? ¿Cuales podras ver esta noche? Toda las peliculas en programación de TDT, Movistar plus y canales digitales')
@section('og_type', 'website')
@section('og_url', 'http://indicecine.net/Televisión')
@section('og_title', 'Indicecine televisión')
<!-- falta imagen -->
@section('og_description', 'Programación de televisión: Todas las películas de los canales de la TDT, Movistar Plus, y canales digitales')
@section('bodyclass', 'user-page')

@section('content')

	<div class="wrap">

		@include('includes.sidebar')

		<section class="content">

			<div class="main-content">

				<h1 class="h1 margins">{{$user->name}}</h1>

				<div class="list-info-wrap margins">
					<ul class="list-info">
						@if(Auth::check() && Auth::id() == $user->id)
							<li><a href="{{route('logout')}}" class="link">desconectar</a></li>
						@endif	
						<li class="break">En Indicecine desde el {{$user->created_at->formatLocalized('%d %b')}}. </li>
						<li>{{$user->lists->count()}} listas. </li>
					</ul>
				</div>


				@if ($user->lists())
					@include('includes.listloop', ['items' => $user->lists])
				@else
					<h3 class="empty">No hay nada aún</h3>
				@endif

			</div><!--main-content-->

		</section>


	</div>
@endsection
