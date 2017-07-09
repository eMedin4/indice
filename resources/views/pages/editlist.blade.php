@extends('layouts.master')

@section('title', 'Cartelera - Películas en televisión')
@section('metadescription', '¿Que películas estan echando ahora en Televisión? ¿Cuales podras ver esta noche? Toda las peliculas en programación de TDT, Movistar plus y canales digitales')
@section('og_type', 'website')
@section('og_url', 'http://indicecine.net/Televisión')
@section('og_title', 'Indicecine televisión')
<!-- falta imagen -->
@section('og_description', 'Programación de televisión: Todas las películas de los canales de la TDT, Movistar Plus, y canales digitales')
@section('bodyclass', 'edit-list-page')

@section('content')

	<div class="wrap">

		@include('includes.sidebar')

		<section class="content">

			@include('includes.mobile-bar')

			<div class="main-content">

				<h1 class="h1 margins" data-id="{{$list->id}}">{{$list->name}}</h1>

				@if ($list->description)
					<p class="list-description margins">{{$list->description}}</p>
				@endif

				<div class="list-info-wrap flex-between margins">
					<ul class="list-info">
						<li><span class="link btn-new-list" data-position="edit-list">editar info</span></li>
						<li><span class="link">guardar</span></li>
						<li><span class="link link-alert btn-delete" data-id="{{$list->id}}" data-text="¿Deseas borrar la lista '{{$list->name}}'? No podrás recuperarla" data-type="delete-list">borrar lista</span></li>
						<li><span class="link link-dark">cancelar</span></li>
						<li>{{$list->movies->count()}} películas. </li>
					</ul>
				</div>

				<div class="edit-label margins">
					<h2>Modo edición</h2>
					<p>*Arrastra las películas para ordenarlas</p>
				</div>

				@if ($list->count())
					@include('includes.loop', ['items' => $list->movies])
				@else
					<h3 class="empty">No hay nada aún</h3>
				@endif

			</div><!--main-content-->

		</section>


	</div>
@endsection

@section('scripts')
	<script src="{{ asset('/assets/js/sortable.js') }}"></script>
@endsection
