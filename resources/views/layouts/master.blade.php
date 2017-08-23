<!DOCTYPE html>
<html lang="es">
<head>
	<title>@yield('title')</title>
	<meta name="description" content="@yield('metadescription')">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="initial-scale=1.0, width=device-width">

	<!-- Facebook objects -->
	<meta property="fb:app_id"          content="311385442544041" /> 
	<meta property="og:type"            content="@yield('og_type')" />
	<meta property="og:url"             content="@yield('og_url')" /> 
	<meta property="og:title"           content="@yield('og_title')" /> 
	<meta property="og:image"           content="@yield('og_image')" /> 
	<meta property="og:image:width"     content="320" /> 
	<meta property="og:image:height"    content="480" /> 
	<meta property="og:description"     content="@yield('og_description')" />
	@yield('more_og')

	<!-- Google analytics -->
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
	  ga('create', 'UA-91524304-1', 'auto');
	  ga('send', 'pageview');
	</script>

	<script src="https://use.fortawesome.com/712aad58.js"></script>
	<link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
	<link rel="stylesheet" href="{{ asset('/assets/css/nouislider.css') }}">
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,900" rel="stylesheet">
	@yield('topscripts')
</head>

<body class="@yield('bodyclass', '')">
		
	<div class="top-bar">
		<div class="wrap">
			<div class="top-bar-inner margins">
				<div class="top-bar-title">
					<div class="logo">
						<a href="{{route('home')}}">
							indicecine
						</a>
					</div>
				</div>
				<div class="top-bar-options">
					<ul>
						<li><a href="{{route('search')}}">
							<span>Buscar</span>
						</a></li>
						@if (Auth::check())
							<li><a href="{{route('userpage', ['name' => str_slug(Auth::user()->name), 'id' => Auth::id()])}}" class="nick">
								<span>{{Auth::user()->nick}}</span>
							</a></li>
						@else
							<li><a href="{{route('login')}}" class="nick">
								<span>Entrar</span>
							</a></li>
						@endif
					</ul>
				</div>
			</div>
		</div>
	</div>

	<div class="background">
		@yield('content')	
	</div>

<!-- Modals -->
	<!-- <div class="modal-wrap"><div class="modal"><div class="inner"></div></div></div> -->

<!-- All site scripts -->
	<script src="{{ asset('/assets/js/nouislider.min.js') }}"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="{{ asset('/assets/js/scripts.js') }}"></script>

<!-- Page scripts -->
	@yield('scripts')

</body>
</html>

