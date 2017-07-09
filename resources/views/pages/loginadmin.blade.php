@extends('layouts.master')

@section('title', 'entrar')

@section('bodyclass', 'login-page')

@section('content')

	<div class="wrap">

		<div class="false-info"></div>

		<div class="login-box">

			<div class="logo">
				<a href="{{route('home')}}">Indicecine</a>
				<p>Entra a la comunidad Indicecine</p>
			</div>

			<form method="POST" action="{{route('postloginadmin')}}" class="form-login">
				{!! csrf_field() !!}
				<label>Email</label>
				<input type="email" name="email">
				<label>Constraseña</label>
				<input type="password" name="password">
				<button type="submit">Entrar</button>
			</form>	  

		</div>

	</div>

@endsection
