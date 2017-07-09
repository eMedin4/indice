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

			<a class="link btn-social facebook" href="{{route('authsocial', ['provider' => 'facebook'])}}">
		       <i class="fa fa-facebook-fa" aria-hidden="true"></i>
		       <span>Entra con Facebook</span>
		    </a>

		    <a class="link btn-social google" href="{{route('authsocial', ['provider' => 'google'])}}">
		       <i class="fa fa-google" aria-hidden="true"></i>
		       <span>Entra con Google</span>
		    </a>	  

		</div>

	</div>
@endsection
