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

		<section class="info">
		




		</section>

		<section class="content">

			<div class="content-header">

				<div class="toggle-icon">
				  <span></span>
				  <span></span>
				  <span></span>
				  <span></span>
				</div>
			
				<div class="search">
					<form autocomplete="off" method="GET" action="{{route('normalsearch')}}">
						{!! csrf_field() !!}
						<div class="close"><i class="fa fa-times"></i></div>
						<button type="submit"><i class="icon-search fa fa-search-mdi"></i></button>
						<input type="text" name="search" class="input-search" placeholder="Busca una película" data-url="{{ route('livesearch') }}" data-path="{{ asset('') }}">
						<div class="search-results"></div>
						<div class="search-results-wrap"></div>
					</form>
				</div>

				<div class="menu">
					<span class="search-launch"><i class="fa fa-search-mdi"></i></span>
				</div>
			</div>

			<div class="info-data">
				<h1 class="h1">En Televisión</h1>
				<h2>Toda las películas de la programación de televisión desde ahora</h2>
			</div>

			<div class="loop-meta">
				<div class="loop-meta-info">
					<h5>{{$list->count()}} películas</h5>
					<span class="piece">·</span>
					por <a href="{{route('userlists', ['name' => str_slug('Oficial Indicecine'), 'id' => 1])}}">Indicecine</a>
				</div>
				<div class="icon-filter">
					Filtros <i class="fa fa-keyboard-arrow-down"></i>
				</div>
			</div>

<!-- 			<ul class="loop-tools">
	<li class="loop-auth">
		<a href="{{route('userlists', ['name' => str_slug('Oficial Indicecine'), 'id' => 1])}}">Indicecine</a>
	</li>
	<li class="loop-count">
		<i class="fa fa-bar-chart"></i>{{$list->count()}} películas
	</li>
	<li class="icon-filter">
		<i class="fa fa-tune"></i>
	</li>
</ul> -->

			<div class="filter-wrap">
				<div class="filter">
					<div class="inner">
						<i class="icon-modal-close fa fa-times propagation"></i>
						<form class="form-filter" method="GET" action="{{route('tvfilter')}}">
							{!! csrf_field() !!}
							<h3>Ordena por</h3>
							<select name="order">
							  <option value="time">Fecha de emisión</option>
							  <option value="movies.year">Año</option>
							  <option value="movies.fa_rat">Puntuación</option>
							</select>
							<h3>Filtra por año</h3>
							<div class="select-year">
								<select class="from-year" name="from-year">
									@for ($i = 2017; $i > 1900; $i--)
										<option value="{{$i}}">{{$i}}</option>
									@endfor
									<option value="1900" selected="selected">1900</option>
								</select>
								<span>-</span>
								<select class="to-year" name="to-year">
									@for ($i = 2017; $i > 1901; $i--)
										<option value="{{$i}}">{{$i}}</option>
									@endfor
								</select>
							</div>
							<h3>Filtra por puntuación</h3>
							<div class="stars-slider-wrap">
								<div id="js-stars-slider"></div>
								<div class="stars-slider-back"></div>
								<input type="hidden" name="from-stars" id="from-stars">
								<input type="hidden" name="to-stars" id="to-stars">
							</div>
							<h3 class="btn-channel-dropdown">Filtra por canal <i class="icon-channel-dropdown fa fa-keyboard-arrow-down"></i></h3>
							<div class="channel-group">
								<div>
									<label><input type="checkbox" id="select-all">Todas</label>
								</div>	
								<div>
									<label><input type="checkbox" name="channel[]" value="TVE" checked>La 1</label>
	  								<label><input type="checkbox" name="channel[]" value="LA2" checked>La 2</label>
								</div>							
								<div>
									<label><input type="checkbox" name="channel[]" value="C4" checked>Cuatro</label>
	  								<label><input type="checkbox" name="channel[]" value="T5" checked>Telecinco</label>
								</div>
								<div>
									<label><input type="checkbox" name="channel[]" value="A3" checked>Antena 3</label>
	  								<label><input type="checkbox" name="channel[]" value="SEXTA" checked>La Sexta</label>
								</div>
								<div>
									<label><input type="checkbox" name="channel[]" value="MV3" checked>#0</label>
	  								<label><input type="checkbox" name="channel[]" value="MV1" checked>Mov Estrenos</label>
								</div>							
								<div>
									<label><input type="checkbox" name="channel[]" value="CPCOLE" checked>Mov DCine</label>
	  								<label><input type="checkbox" name="channel[]" value="CPACCI" checked>Mov Acción</label>
								</div>
								<div>
									<label><input type="checkbox" name="channel[]" value="CPCOME" checked>Mov Comedia</label>
	  								<label><input type="checkbox" name="channel[]" value="CPXTRA" checked>Mov Xtra</label>
								</div>							
								<div>
									<label><input type="checkbox" name="channel[]" value="AMC" checked>AMC</label>
	  								<label><input type="checkbox" name="channel[]" value="AXN" checked>AXN</label>
								</div>
								<div>
									<label><input type="checkbox" name="channel[]" value="SET" checked>Axn White</label>
	  								<label><input type="checkbox" name="channel[]" value="COSMO" checked>Cosmo</label>
								</div>							
								<div>
									<label><input type="checkbox" name="channel[]" value="CL13" checked>Calle 13</label>
	  								<label><input type="checkbox" name="channel[]" value="PCM" checked>Comedy Central</label>
								</div>
								<div>
									<label><input type="checkbox" name="channel[]" value="DCH" checked>Disney Ch</label>
	  								<label><input type="checkbox" name="channel[]" value="DIVINI" checked>Divinity</label>
								</div>							
								<div>
									<label><input type="checkbox" name="channel[]" value="FOXGE" checked>Fox</label>
	  								<label><input type="checkbox" name="channel[]" value="FOXCR" checked>Fox Life</label>
								</div>
								<div>
									<label><input type="checkbox" name="channel[]" value="HOLLYW" checked>Hollywood</label>
	  								<label><input type="checkbox" name="channel[]" value="NEOX" checked>Neox</label>
								</div>							
								<div>
									<label><input type="checkbox" name="channel[]" value="NOVA" checked>Nova</label>
	  								<label><input type="checkbox" name="channel[]" value="PARCH" checked>Paramount Ch</label>
								</div>
								<div>
									<label><input type="checkbox" name="channel[]" value="SCI-FI" checked>SyFy</label>
	  								<label><input type="checkbox" name="channel[]" value="TCM" checked>TCM</label>
								</div>							
								<div>
									<label><input type="checkbox" name="channel[]" value="TNT" checked>TNT</label>
								</div>
							</div>
							<div>
								<button type="submit">Aceptar</button>
								<button type="button" class="btn-cancel propagation">Cancelar</button>
							</div>
						</form>

					</div>
				</div>
			</div>

			<div class="loop">
				@if (!$list->isEmpty())
					@foreach ($list as $schedule)

						<article>

							<div class="tv-tag tv-tag-alert">
								<div class="tv-time">{!!$schedule->formatTime!!}</div>
								<div class="channel-logo"><div class="channel-logo-{{$schedule->channel_code}}"></div></div>
							</div>

							<div class="inner">

								<a class="loop-poster" href="{{route('show', $schedule->movie->slug)}}" data-id="{{$schedule->movie->id}}">
									<div class="poster-reflex"></div>
									@if ($schedule->movie->check_poster)
										<img src="{{asset('/assets/dbimages/posters/medium') . $schedule->movie->poster}}" alt="{{$schedule->movie->title}}" title="poster de {{$schedule->movie->title}}">
									@else 
										<img src="{{asset('/assets/images/no-poster-medium.png')}}" alt="{{$schedule->movie->title}}" title="poster de {{$schedule->movie->title}}">						
									@endif
								</a>

								<div class="loop-data">
									<div class="loop-title">
										<h3>{{$schedule->movie->title}}</h3>
										<p>{{str_limit($schedule->movie->review, 160)}}</p>
									</div>

									<div class="meta">
										<div class="year">{{$schedule->movie->year}}</div>
										<div class="country country-{{str_slug($schedule->movie->country)}}"></div>
										<div class="stars stars-{{$schedule->movie->average}}"></div>
									</div>
								</div>

							</div>

						</article>
					@endforeach

					@for ($i = 0; $i < 7; $i++)
						<article class="empty-grid empty-grid-invisible js-ignore-edit">
							<div></div>
						</article>
					@endfor

				@else
					<h3 class="empty">No hay nada aún</h3>
				@endif
			</div><!-- loop -->
			
			<div class="darken-overlay"></div>


		</section>


	</div>
@endsection
