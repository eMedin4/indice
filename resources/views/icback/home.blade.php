@extends('layouts.icback')
@section('content')

	@if (count($errors) > 0)
	    <div class="box-message box-message-alert">
	        <ul>
	            @foreach ($errors->all() as $error)
	                <li>{{ $error }}</li>
	            @endforeach
	        </ul>
	    </div>
	@endif

	<h2>Introduce el id de Filmaffinity:</h2>
	<form method="GET" action="{{route('fromfaid')}}">
		{!! csrf_field() !!}
		<div class="inline-block">
			<input type="text" name="id" value="{{old('id')}}" placeholder="id">
		</div>
		<button class="btn" type="submit">Procesar id de Filmaffinity</button>
	</form>

	<h2>Scrapea películas en cartelera</h2>
	<a class="btn" href="{{route('fromcartelera')}}">Iniciar cartelera de Filmaffinity</a>

	<h2>Scrapea guia Movistar</h2>
	<a class="btn" href="{{route('frommovistar')}}">Iniciar programación Movistar</a>

	<h2>Scrapea guia Movistar por url</h2>
	<form method="GET" action="{{route('frommovistarurl')}}">
		{!! csrf_field() !!}
		<div class="inline-block">
			<input type="text" name="url" value="{{old('url')}}" placeholder="url">
		</div>
		<button class="btn" type="submit">Iniciar URL de Movistar</button>
	</form>

	<h2>Scrapea todas las películas:</h2>
	<form method="GET" action="{{route('fromfaletter')}}">
		{!! csrf_field() !!}
		<div class="inline-block">
			<input type="text" name="letter" value="{{old('letter')}}" placeholder="Letra">
			<input type="text" name="first-page" value="{{old('first-page')}}" placeholder="Pag inicio">
			<input type="text" name="total-pages" value="{{old('total-pages')}}" placeholder="Pags totales">
		</div>
		<button type="submit" class="btn">Procesar páginas de Filmaffinity</button>
	</form>


@endsection
