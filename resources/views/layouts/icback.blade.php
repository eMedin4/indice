<!DOCTYPE html>
<html lang="es">
<head>
	<title>ICBACK</title>
	<meta name="description" content="@yield('metadescription')">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="initial-scale=1.0, width=device-width">
	<script src="https://use.fortawesome.com/712aad58.js"></script>
	<link rel="stylesheet" href="{{ asset('/assets/css/icback.css') }}">
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600" rel="stylesheet">
</head>

<body>

	<div class="inner">

		<a href="{{route('icback')}}"><h1>icback</h1></a>
		
		<div class="limit">
			@yield('content')	
		</div>

	</div>

</body>
</html>

